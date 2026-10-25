<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffRoleAndActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $seller;

    protected User $customerService;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Staff Penjual',
            'email' => 'seller@test.com',
            'role' => 'seller',
            'password' => bcrypt('password123'),
        ]);

        $this->customerService = User::factory()->create([
            'name' => 'Staff CS',
            'email' => 'cs@test.com',
            'role' => 'customer_service',
            'password' => bcrypt('password123'),
        ]);

        $this->customer = User::factory()->create([
            'name' => 'Member Customer',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_staff_can_login_to_admin_portal_and_activity_is_logged(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'seller@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->seller);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->seller->id,
            'action' => 'auth.staff_login',
            'category' => 'auth',
            'user_role' => 'seller',
        ]);
    }

    public function test_customer_cannot_login_to_admin_portal(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'customer@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'auth.staff_login_forbidden',
            'category' => 'auth',
        ]);
    }

    public function test_seller_can_manage_products_and_update_orders(): void
    {
        $product = Product::factory()->create(['name' => 'Kukis Red Velvet']);
        $order = Order::factory()->create(['status' => 'pending']);

        // Seller updates product status
        $this->actingAs($this->seller)
            ->patch(route('admin.products.toggle', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->seller->id,
            'action' => 'product.status_toggled',
            'category' => 'catalog',
        ]);

        // Seller updates order status
        $this->actingAs($this->seller)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'baking',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->seller->id,
            'action' => 'order.status_updated',
            'category' => 'order',
        ]);
    }

    public function test_seller_cannot_access_settings_or_user_management(): void
    {
        $this->actingAs($this->seller)
            ->get(route('admin.settings.index'))
            ->assertForbidden();

        $this->actingAs($this->seller)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_customer_service_can_view_orders_but_cannot_access_products_crud(): void
    {
        $this->actingAs($this->customerService)
            ->get(route('admin.orders.index'))
            ->assertOk();

        // CS is forbidden from creating products
        $this->actingAs($this->customerService)
            ->post(route('admin.products.store'), [
                'name' => 'Kukis Ilegal',
                'price' => 25000,
                'stock' => 10,
            ])
            ->assertForbidden();
    }

    public function test_admin_can_create_new_staff_member_and_it_is_logged(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Staf Baru Toko',
                'email' => 'stafbaru@ngemilkukis.com',
                'phone' => '081299998888',
                'password' => 'password123',
                'role' => 'seller',
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'stafbaru@ngemilkukis.com',
            'role' => 'seller',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'staff.created',
            'category' => 'staff',
        ]);
    }

    public function test_activity_logs_page_accessible_by_admin_and_filterable(): void
    {
        ActivityLog::create([
            'user_id' => $this->admin->id,
            'user_name' => $this->admin->name,
            'user_role' => 'admin',
            'action' => 'test.action',
            'category' => 'order',
            'description' => 'Aktivitas pengujian otomatis',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index', ['category' => 'order']));

        $response->assertOk();
        $response->assertSee('Aktivitas pengujian otomatis');

        // Seller and CS cannot view activity logs
        $this->actingAs($this->seller)
            ->get(route('admin.activity-logs.index'))
            ->assertForbidden();

        $this->actingAs($this->customerService)
            ->get(route('admin.activity-logs.index'))
            ->assertForbidden();
    }
}
