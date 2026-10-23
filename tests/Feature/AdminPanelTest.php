<?php

namespace Tests\Feature;

use App\Models\GreetingCardTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        // Act
        $response = $this->get(route('admin.dashboard'));

        // Assert
        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_user_is_forbidden_from_admin_panel(): void
    {
        // Arrange
        $customer = User::factory()->create(['role' => 'customer']);

        // Act
        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        // Assert
        $response->assertForbidden();
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create([
            'email' => 'admin@ngemilkukis.com',
            'password' => bcrypt('password123'),
        ]);

        // Act
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@ngemilkukis.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_access_dashboard_and_view_metrics(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create();
        Product::factory()->count(3)->create(['is_active' => true]);

        // Act
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Ringkasan Dashboard');
    }

    public function test_admin_can_create_a_new_product(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create();
        $productData = [
            'name' => 'Caramel Speculoos Crunch',
            'tagline' => 'Lotus cream infused',
            'description' => 'Crispy outside, rich spiced Lotus biscoff inside.',
            'price' => 32000,
            'sale_price' => 29000,
            'type' => 'ready_stock',
            'stock' => 15,
            'weight_grams' => 85,
            'is_active' => 1,
            'is_featured' => 1,
        ];

        // Act
        $response = $this->actingAs($admin)->post(route('admin.products.store'), $productData);

        // Assert
        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Caramel Speculoos Crunch',
            'slug' => 'caramel-speculoos-crunch',
            'price' => 32000,
            'sale_price' => 29000,
        ]);
    }

    public function test_admin_can_update_an_existing_product(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'name' => 'Classic Dark Cookie',
            'price' => 25000,
            'stock' => 10,
        ]);

        // Act
        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Classic Dark Cookie Special',
            'tagline' => 'Extra 75% Cocoa',
            'description' => 'Updated rich description',
            'price' => 28000,
            'type' => 'pre_order',
            'baking_schedule' => 'Baking setiap hari Rabu',
            'stock' => 30,
            'is_active' => 1,
        ]);

        // Assert
        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Classic Dark Cookie Special',
            'price' => 28000,
            'stock' => 30,
            'type' => 'pre_order',
        ]);
    }

    public function test_admin_can_delete_a_product(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        // Act
        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        // Assert
        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_view_order_details_and_update_status(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create();
        $card = GreetingCardTemplate::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'status' => 'pending',
            'is_gift' => true,
            'gift_card_template_id' => $card->id,
            'gift_recipient_name' => 'Sinta',
            'gift_sender_name' => 'Rama',
            'gift_message' => 'Happy Graduation!',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
        ]);

        // Act 1: View order detail
        $viewResponse = $this->actingAs($admin)->get(route('admin.orders.show', $order));
        $viewResponse->assertOk();
        $viewResponse->assertSee($order->order_number);
        $viewResponse->assertSee('Sinta');
        $viewResponse->assertSee('Happy Graduation!');

        // Act 2: Update status
        $updateResponse = $this->actingAs($admin)->patch(route('admin.orders.update-status', $order), [
            'status' => 'baking',
        ]);

        // Assert
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'baking',
        ]);
    }

    public function test_admin_can_update_dynamic_site_settings_and_reflect_on_storefront(): void
    {
        // Arrange
        $admin = User::factory()->admin()->create();

        // Act: Admin updates dynamic settings
        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'NgemilKukis Gourmet',
                'hero_headline' => 'Sensasi Renyah Mentega Belgia,',
                'hero_subheadline' => 'Meleleh Sempurna di Lidah!',
                'announcement_text' => 'PROMO SPESIAL GAJIAN: Diskon 20% Semua Hampers!',
                'whatsapp_display' => '+62 888-9999-0000',
            ],
        ]);

        // Assert: Redirects back to settings index
        $response->assertRedirect(route('admin.settings.index'));

        // Assert: Database has the new values
        $this->assertDatabaseHas('site_settings', [
            'key' => 'hero_headline',
            'value' => 'Sensasi Renyah Mentega Belgia,',
        ]);
        $this->assertDatabaseHas('site_settings', [
            'key' => 'announcement_text',
            'value' => 'PROMO SPESIAL GAJIAN: Diskon 20% Semua Hampers!',
        ]);

        // Act 2: Customer visits storefront home page
        $homeResponse = $this->get(route('home'));

        // Assert: Storefront displays updated dynamic texts
        $homeResponse->assertOk();
        $homeResponse->assertSee('Sensasi Renyah Mentega Belgia,');
        $homeResponse->assertSee('Meleleh Sempurna di Lidah!');
        $homeResponse->assertSee('+62 888-9999-0000');
    }
}
