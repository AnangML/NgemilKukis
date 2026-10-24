<?php

namespace Tests\Feature;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_manual_email_registration_logs_in_immediately(): void
    {
        $response = $this->postJson(route('customer.register'), [
            'name' => 'Maya Anggraini',
            'email' => 'maya@example.com',
            'phone' => '081298765432',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'maya@example.com',
            'name' => 'Maya Anggraini',
            'phone' => '081298765432',
            'role' => 'customer',
        ]);
    }

    public function test_customer_manual_email_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'rian@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        $loginResponse = $this->postJson(route('customer.login'), [
            'email' => 'rian@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertStatus(200);
        $loginResponse->assertJson(['success' => true]);
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post(route('customer.logout'));
        $logoutResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_phone_otp_send_simulation_and_verification(): void
    {
        $sendResponse = $this->postJson(route('auth.phone.send-otp'), [
            'phone' => '081388889999',
        ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJsonStructure([
            'success',
            'message',
            'simulation_code',
        ]);

        $otpCode = $sendResponse->json('simulation_code');
        $this->assertNotNull($otpCode);
        $this->assertEquals(4, strlen($otpCode));

        $verifyResponse = $this->postJson(route('auth.phone.verify-otp'), [
            'phone' => '081388889999',
            'otp_code' => $otpCode,
            'name' => 'Kukis Lover',
        ]);

        $verifyResponse->assertStatus(200);
        $verifyResponse->assertJson(['success' => true]);
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'phone' => '081388889999',
            'role' => 'customer',
        ]);
    }

    public function test_google_oauth_simulation_logs_in_customer(): void
    {
        $response = $this->get(route('auth.google.simulate'));

        $response->assertRedirect();
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('customer', $user->role);
        $this->assertEquals('google', $user->auth_provider);
    }

    public function test_authenticated_customer_checkout_persists_user_id_and_member_discount(): void
    {
        $user = User::factory()->create([
            'name' => 'Citra Lestari',
            'email' => 'citra@example.com',
            'phone' => '081211112222',
            'role' => 'customer',
            'member_discount_amount' => 10000,
        ]);

        $product = Product::factory()->create([
            'name' => 'Matcha White Choco Chunk',
            'price' => 30000,
            'sale_price' => null,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $payload = [
            'customer_name' => 'Citra Lestari',
            'customer_phone' => '081211112222',
            'customer_email' => 'citra@example.com',
            'customer_address' => 'Jl. Senopati No. 45',
            'customer_city' => 'Jakarta Selatan',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'items' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 30000,
                    'quantity' => 2,
                ],
            ],
            'notes' => 'Tolong pita gold',
        ];

        $response = $this->postJson(route('checkout.place'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Subtotal = 60,000. Shipping = 25,000. Discount = 10,000. Total = 75,000.
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'customer_name' => 'Citra Lestari',
            'discount_amount' => 10000,
            'subtotal' => 60000,
            'total_amount' => 75000,
        ]);
    }

    public function test_customer_address_book_crud_and_primary_switch(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        // 1. Store first address (set as primary)
        $storeResponse1 = $this->post(route('customer.addresses.store'), [
            'label' => 'Rumah',
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'full_address' => 'Jl. Anggrek No. 10',
            'city' => 'Jakarta Selatan',
            'is_primary' => '1',
        ]);
        $storeResponse1->assertRedirect();

        $address1 = CustomerAddress::where('user_id', $user->id)->first();
        $this->assertNotNull($address1);
        $this->assertTrue((bool) $address1->is_primary);

        // 2. Store second address as primary (should demote first address)
        $storeResponse2 = $this->post(route('customer.addresses.store'), [
            'label' => 'Kantor',
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'full_address' => 'Gedung Wisma 46 Lt. 12',
            'city' => 'Jakarta Pusat',
            'is_primary' => '1',
        ]);
        $storeResponse2->assertRedirect();

        $address1->refresh();
        $this->assertFalse((bool) $address1->is_primary);

        $address2 = CustomerAddress::where('label', 'Kantor')->first();
        $this->assertNotNull($address2);
        $this->assertTrue((bool) $address2->is_primary);

        // 3. Delete address
        $deleteResponse = $this->delete(route('customer.addresses.destroy', $address1));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('customer_addresses', ['id' => $address1->id]);
    }

    public function test_customer_orders_page_scopes_to_authenticated_customer(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $otherUser = User::factory()->create(['role' => 'customer']);

        $userOrder = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'NK-USER-ORDER-001',
            'customer_name' => $user->name,
            'customer_phone' => $user->phone ?? '0811111111',
            'customer_address' => 'Jl. Alamat User',
            'status' => 'paid',
        ]);

        $otherOrder = Order::factory()->create([
            'user_id' => $otherUser->id,
            'order_number' => 'NK-OTHER-ORDER-999',
            'customer_name' => 'Orang Lain',
            'customer_phone' => '0899999999',
            'customer_address' => 'Jl. Alamat Lain',
            'status' => 'paid',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('customer.orders'));
        $response->assertStatus(200);
        $response->assertSee('NK-USER-ORDER-001');
        $response->assertDontSee('NK-OTHER-ORDER-999');
    }
}
