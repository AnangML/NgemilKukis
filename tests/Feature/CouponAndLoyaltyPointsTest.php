<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponAndLoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteSettingSeeder::class);
    }

    public function test_customer_can_validate_active_coupon_successfully(): void
    {
        $coupon = Coupon::create([
            'code' => 'HEMAT20',
            'name' => 'Hemat Rp 20.000',
            'type' => 'fixed',
            'value' => 20000,
            'min_order_amount' => 50000,
            'is_active' => true,
            'usage_limit_per_user' => 2,
        ]);

        $response = $this->postJson(route('checkout.validate-coupon'), [
            'code' => 'HEMAT20',
            'subtotal' => 75000,
            'phone' => '081234567890',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'coupon' => [
                    'code' => 'HEMAT20',
                    'discount_amount' => 20000,
                    'is_stackable_with_member' => true,
                ],
            ]);
    }

    public function test_coupon_validation_fails_for_invalid_code_or_below_min_order(): void
    {
        $coupon = Coupon::create([
            'code' => 'MIN100K',
            'name' => 'Min 100K',
            'type' => 'fixed',
            'value' => 15000,
            'min_order_amount' => 100000,
            'is_active' => true,
            'usage_limit_per_user' => 1,
        ]);

        // 1. Non-existent code
        $notFound = $this->postJson(route('checkout.validate-coupon'), [
            'code' => 'SALAHKODE',
            'subtotal' => 150000,
        ]);
        $notFound->assertNotFound();

        // 2. Below minimum order
        $belowMin = $this->postJson(route('checkout.validate-coupon'), [
            'code' => 'MIN100K',
            'subtotal' => 50000,
        ]);
        $belowMin->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_coupon_validation_enforces_per_user_limit(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'phone' => '081299998888',
        ]);

        $coupon = Coupon::create([
            'code' => 'SATUKALI',
            'name' => 'Kupon Sekali Pakai',
            'type' => 'fixed',
            'value' => 10000,
            'min_order_amount' => 0,
            'is_active' => true,
            'usage_limit_per_user' => 1,
        ]);

        // First check as user: eligible
        $res1 = $this->actingAs($customer)->postJson(route('checkout.validate-coupon'), [
            'code' => 'SATUKALI',
            'subtotal' => 50000,
        ]);
        $res1->assertOk();

        // Record 1 usage for this customer
        $order = Order::factory()->create(['user_id' => $customer->id]);
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'customer_phone' => $customer->phone,
            'discount_amount' => 10000,
        ]);

        // Second check: should fail limit
        $res2 = $this->actingAs($customer)->postJson(route('checkout.validate-coupon'), [
            'code' => 'SATUKALI',
            'subtotal' => 50000,
        ]);
        $res2->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_order_creation_with_coupon_applies_correct_discount_and_records_usage(): void
    {
        $product = Product::factory()->create([
            'price' => 50000,
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'KUKIS15',
            'name' => 'Diskon Rp 15.000',
            'type' => 'fixed',
            'value' => 15000,
            'min_order_amount' => 50000,
            'is_active' => true,
            'usage_limit_per_user' => 2,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'member_discount_amount' => 10000,
        ]);

        $response = $this->actingAs($customer)->postJson(route('checkout.place'), [
            'customer_name' => 'Rina Melati',
            'customer_phone' => '081233445566',
            'customer_email' => 'rina@example.com',
            'customer_address' => 'Jl. Mawar No. 10',
            'customer_city' => 'Jakarta Selatan',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'coupon_code' => 'KUKIS15',
            'items' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 2, // subtotal 100.000
                ],
            ],
        ]);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($coupon->id, $order->coupon_id);
        $this->assertEquals('KUKIS15', $order->coupon_code);
        $this->assertEquals(15000, $order->coupon_discount_amount);
        $this->assertEquals(10000, $order->discount_amount); // member discount stacked

        // Check coupon usage recorded
        $this->assertDatabaseHas('coupon_usages', [
            'coupon_id' => $coupon->id,
            'order_id' => $order->id,
            'discount_amount' => 15000,
        ]);

        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_non_stackable_coupon_waives_member_discount(): void
    {
        $product = Product::factory()->create([
            'price' => 100000,
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'STANDALONE50',
            'name' => 'Mega Diskon Rp 50.000',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_amount' => 100000,
            'is_active' => true,
            'is_stackable_with_member' => false,
            'usage_limit_per_user' => 1,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'member_discount_amount' => 10000,
        ]);

        $response = $this->actingAs($customer)->postJson(route('checkout.place'), [
            'customer_name' => 'Bambang',
            'customer_phone' => '081299887766',
            'customer_email' => 'bambang@example.com',
            'customer_address' => 'Jl. Melati No. 5',
            'customer_city' => 'Jakarta Barat',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'coupon_code' => 'STANDALONE50',
            'items' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertEquals(50000, $order->coupon_discount_amount);
        // Member discount waived
        $this->assertEquals(0, $order->discount_amount);
    }

    public function test_order_creation_with_points_redemption_debits_user_balance_and_records_transaction(): void
    {
        $product = Product::factory()->create([
            'price' => 100000,
            'is_active' => true,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'member_discount_amount' => 0,
            'points_balance' => 200,
        ]);

        $response = $this->actingAs($customer)->postJson(route('checkout.place'), [
            'customer_name' => 'Dina Loyalty',
            'customer_phone' => '081211223344',
            'customer_email' => 'dina@example.com',
            'customer_address' => 'Jl. Cempaka No. 8',
            'customer_city' => 'Jakarta Pusat',
            'shipping_method' => 'regular_jne',
            'payment_method' => 'xendit_qris',
            'use_points' => true,
            'points_to_redeem' => 100, // 100 * 50 = Rp 5.000 discount
            'items' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertEquals(100, $order->points_redeemed);
        $this->assertEquals(5000, $order->points_discount_amount);

        // Check user balance debited
        $this->assertEquals(100, $customer->fresh()->points_balance);

        // Check point transaction ledger
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'redeemed',
            'amount' => -100,
            'balance_after' => 100,
        ]);
    }

    public function test_points_redemption_is_capped_at_fifty_percent_of_subtotal(): void
    {
        $product = Product::factory()->create([
            'price' => 40000,
            'is_active' => true,
        ]);

        // Customer has 1.000 points (worth Rp 50.000)
        // But subtotal is Rp 40.000 -> 50% max discount is Rp 20.000 (which is 400 points)
        $customer = User::factory()->create([
            'role' => 'customer',
            'member_discount_amount' => 0,
            'points_balance' => 1000,
        ]);

        $response = $this->actingAs($customer)->postJson(route('checkout.place'), [
            'customer_name' => 'Bayu',
            'customer_phone' => '081277889900',
            'customer_address' => 'Jl. Kenanga 12',
            'customer_city' => 'Jakarta Timur',
            'shipping_method' => 'regular_jne',
            'payment_method' => 'xendit_qris',
            'use_points' => true,
            'points_to_redeem' => 800, // Customer requested 800, should be capped at 400
            'items' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertEquals(400, $order->points_redeemed);
        $this->assertEquals(20000, $order->points_discount_amount);
        $this->assertEquals(600, $customer->fresh()->points_balance);
    }

    public function test_payment_simulation_awards_earned_points_to_customer(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'points_balance' => 0,
        ]);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'status' => 'pending',
            'total_amount' => 75000, // floor(75000 / 1000) = 75 points
        ]);

        // Simulate payment
        $response = $this->post(route('orders.simulate-pay', $order->order_number));
        $response->assertRedirect(route('orders.show', $order->order_number));

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals(75, $order->points_earned);

        // Customer points updated
        $this->assertEquals(75, $customer->fresh()->points_balance);

        // Transaction recorded
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'earned',
            'amount' => 75,
            'balance_after' => 75,
        ]);

        // Re-simulating should not duplicate points
        $this->post(route('orders.simulate-pay', $order->order_number));
        $this->assertEquals(75, $customer->fresh()->points_balance);
        $this->assertEquals(1, PointTransaction::where('order_id', $order->id)->where('type', 'earned')->count());
    }

    public function test_welcome_bonus_awarded_on_new_customer_registration(): void
    {
        $response = $this->postJson(route('customer.register'), [
            'name' => 'Pelanggan Baru',
            'email' => 'baru@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone' => '081233344455',
        ]);

        $response->assertOk();

        $newUser = User::where('email', 'baru@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals(50, $newUser->points_balance);

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $newUser->id,
            'type' => 'bonus',
            'amount' => 50,
            'balance_after' => 50,
        ]);
    }

    public function test_admin_and_seller_can_manage_coupons(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'seller']);
        $cs = User::factory()->create(['role' => 'customer_service']);

        // 1. Admin creates coupon
        $resAdmin = $this->actingAs($admin)->post(route('admin.coupons.store'), [
            'code' => 'PROMOADMIN',
            'name' => 'Promo dari Admin',
            'type' => 'fixed',
            'value' => 25000,
            'min_order_amount' => 50000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ]);
        $resAdmin->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'PROMOADMIN']);

        $coupon = Coupon::where('code', 'PROMOADMIN')->first();

        // 2. Seller can toggle status
        $this->actingAs($seller)->patch(route('admin.coupons.toggle', $coupon));
        $this->assertFalse((bool) $coupon->fresh()->is_active);

        // 3. Customer Service CANNOT manage coupons (forbidden)
        $resCs = $this->actingAs($cs)->post(route('admin.coupons.store'), [
            'code' => 'PROMOTIDAKBOLEH',
            'name' => 'Promo Ilegal',
            'type' => 'fixed',
            'value' => 10000,
            'usage_limit_per_user' => 1,
        ]);
        $resCs->assertForbidden();
    }
}
