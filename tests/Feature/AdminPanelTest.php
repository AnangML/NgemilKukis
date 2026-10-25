<?php

namespace Tests\Feature;

use App\Models\GreetingCardTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_upload_product_image_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->image('pistachio-cookie.jpg');

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Pistachio Kunafa Crunch',
            'tagline' => 'Dubai Chocolate trend',
            'description' => 'Crispy kataifi pastry infused with pure pistachio butter.',
            'price' => 38000,
            'type' => 'ready_stock',
            'stock' => 20,
            'image_file' => $file,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Pistachio Kunafa Crunch')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('/storage/products/', $product->image);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $product->image));
    }

    public function test_admin_can_toggle_product_status_and_featured(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'is_active' => true,
            'is_featured' => false,
        ]);

        // Toggle Active
        $response1 = $this->actingAs($admin)->patch(route('admin.products.toggle', $product));
        $response1->assertRedirect();
        $this->assertFalse($product->fresh()->is_active);

        // Toggle Featured
        $response2 = $this->actingAs($admin)->patch(route('admin.products.toggle-featured', $product));
        $response2->assertRedirect();
        $this->assertTrue($product->fresh()->is_featured);
    }

    public function test_admin_can_upload_hero_banner_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->image('artisan-hero.jpg');

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'hero_headline' => 'Kukis Hangat Premium',
            ],
            'hero_image_file' => $file,
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertDatabaseHas('site_settings', [
            'key' => 'hero_headline',
            'value' => 'Kukis Hangat Premium',
        ]);

        $heroSetting = SiteSetting::where('key', 'hero_image_url')->first();
        $this->assertNotNull($heroSetting);
        $this->assertStringStartsWith('/storage/hero/', $heroSetting->value);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $heroSetting->value));
    }

    public function test_admin_can_upload_brand_logo_in_supported_format_under_10mb(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->create('brand-logo.svg', 1024, 'image/svg+xml');

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'NgemilKukis Artisan',
            ],
            'site_logo_file' => $file,
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $logoSetting = SiteSetting::where('key', 'site_logo_url')->first();
        $this->assertNotNull($logoSetting);
        $this->assertStringStartsWith('/storage/logos/', $logoSetting->value);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $logoSetting->value));
    }

    public function test_brand_logo_exceeding_10mb_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // 11 MB file (11264 KB > 10240 KB)
        $hugeFile = UploadedFile::fake()->create('huge-logo.png', 11264, 'image/png');

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'NgemilKukis',
            ],
            'site_logo_file' => $hugeFile,
        ]);

        $response->assertSessionHasErrors(['site_logo_file']);
    }

    public function test_admin_can_remove_custom_brand_logo(): void
    {
        $admin = User::factory()->admin()->create();
        SiteSetting::set('site_logo_url', '/storage/logos/old_logo.png');

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'NgemilKukis',
            ],
            'remove_site_logo' => '1',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('', SiteSetting::get('site_logo_url'));
    }

    public function test_admin_can_manage_greeting_cards(): void
    {
        $admin = User::factory()->admin()->create();

        // 1. Create Greeting Card
        $createResponse = $this->actingAs($admin)->post(route('admin.greeting-cards.store'), [
            'name' => 'Eid Mubarak Warm Wishes',
            'theme_color' => 'emerald',
            'design_style' => 'festive_holiday',
            'default_message' => 'Selamat Hari Raya Idul Fitri, mohon maaf lahir dan batin.',
            'price' => 0,
            'is_active' => 1,
        ]);

        $createResponse->assertRedirect(route('admin.greeting-cards.index'));
        $this->assertDatabaseHas('greeting_card_templates', [
            'name' => 'Eid Mubarak Warm Wishes',
            'slug' => 'eid-mubarak-warm-wishes',
            'theme_color' => 'emerald',
        ]);

        $card = GreetingCardTemplate::where('slug', 'eid-mubarak-warm-wishes')->first();

        // 2. Update Greeting Card
        $updateResponse = $this->actingAs($admin)->put(route('admin.greeting-cards.update', $card), [
            'name' => 'Eid Mubarak Gold Edition',
            'theme_color' => 'amber',
            'design_style' => 'elegant_gold',
            'default_message' => 'Taqabbalallahu minna wa minkum.',
            'price' => 5000,
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.greeting-cards.index'));
        $this->assertDatabaseHas('greeting_card_templates', [
            'id' => $card->id,
            'name' => 'Eid Mubarak Gold Edition',
            'price' => 5000,
            'theme_color' => 'amber',
        ]);

        // 3. Toggle Status
        $toggleResponse = $this->actingAs($admin)->patch(route('admin.greeting-cards.toggle', $card));
        $toggleResponse->assertRedirect();
        $this->assertFalse($card->fresh()->is_active);

        // 4. Delete Greeting Card
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.greeting-cards.destroy', $card));
        $deleteResponse->assertRedirect(route('admin.greeting-cards.index'));
        $this->assertDatabaseMissing('greeting_card_templates', ['id' => $card->id]);
    }

    public function test_dynamic_shipping_rates_reflected_in_order_calculation(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'price' => 30000,
            'sale_price' => null,
            'stock' => 10,
        ]);

        // 1. Admin updates instant shipping rate to 35.000 via settings
        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'shipping_instant_grab' => '35000',
            ],
        ]);

        // 2. OrderService calculates total using dynamic rate
        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);
        $order = $orderService->createOrder([
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'customer_email' => 'budi@example.com',
            'customer_address' => 'Jl. Sudirman No 1',
            'customer_city' => 'Jakarta Selatan',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'items' => [
                ['id' => $product->id, 'quantity' => 2],
            ],
        ]);

        // Total should be: subtotal (30.000 * 2 = 60.000) + dynamic shipping (35.000) = 95.000
        $this->assertEquals(35000, $order->shipping_cost);
        $this->assertEquals(95000, $order->total_amount);
    }
}
