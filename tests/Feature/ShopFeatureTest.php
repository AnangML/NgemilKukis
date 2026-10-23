<?php

namespace Tests\Feature;

use App\Models\GreetingCardTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_cookie_products_and_cards(): void
    {
        Product::factory()->create([
            'name' => 'Signature Choco Chunk',
            'is_active' => true,
        ]);

        GreetingCardTemplate::factory()->create([
            'name' => 'Happy Birthday Sweet Moments',
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('NgemilKukis');
        $response->assertSee('Signature Choco Chunk');
        $response->assertSee('Happy Birthday Sweet Moments');
    }

    public function test_checkout_creates_order_and_items_with_calculated_totals(): void
    {
        $product = Product::factory()->create([
            'name' => 'NYC Choco Chunk',
            'price' => 25000,
            'sale_price' => null,
            'is_active' => true,
        ]);

        $card = GreetingCardTemplate::factory()->create([
            'name' => 'Sweet Birthday',
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi@example.com',
            'customer_address' => 'Jl. Mawar No. 12, Kebayoran Baru',
            'customer_city' => 'Jakarta Selatan',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 2,
                    'notes' => 'Tolong panaskan dulu ya',
                ],
            ],
            'is_gift' => true,
            'gift_recipient_name' => 'Siti Aisyah',
            'gift_sender_name' => 'Budi',
            'gift_card_template_id' => $card->id,
            'gift_message' => 'Selamat ulang tahun Siti!',
            'notes' => 'Pita warna emas',
        ];

        $response = $this->postJson(route('checkout.place'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'subtotal' => 50000,
            'shipping_cost' => 25000,
            'total_amount' => 75000,
            'is_gift' => true,
            'gift_recipient_name' => 'Siti Aisyah',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'NYC Choco Chunk',
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000,
        ]);
    }

    public function test_order_confirmation_page_displays_order_details(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'NK-TEST-001',
            'customer_name' => 'Citra Kirana',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_name' => 'Red Velvet Cookie',
            'price' => 30000,
            'quantity' => 1,
            'subtotal' => 30000,
        ]);

        $response = $this->get(route('orders.show', 'NK-TEST-001'));

        $response->assertStatus(200);
        $response->assertSee('NK-TEST-001');
        $response->assertSee('Citra Kirana');
        $response->assertSee('Red Velvet Cookie');
    }

    public function test_simulate_payment_marks_order_as_paid(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'NK-TEST-002',
            'status' => 'pending',
        ]);

        $response = $this->post(route('orders.simulate-pay', 'NK-TEST-002'));

        $response->assertRedirect(route('orders.show', 'NK-TEST-002'));

        $this->assertDatabaseHas('orders', [
            'order_number' => 'NK-TEST-002',
            'status' => 'paid',
        ]);
    }
}
