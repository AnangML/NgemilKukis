<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_orders_page_loads_successfully(): void
    {
        $response = $this->get(route('customer.orders'));

        $response->assertStatus(200);
        $response->assertSee('Pesanan & Pengiriman Saya', false);
        $response->assertSee('Di Jalan');
        $response->assertSee('Sudah Sampai');
        $response->assertSee('Dibatalkan');
    }

    public function test_can_search_order_by_order_number(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'NK-20261023-TEST1',
            'customer_name' => 'Nicha Thanakorn',
            'customer_phone' => '081298765432',
            'status' => 'shipped',
        ]);

        $product = Product::factory()->create(['name' => 'Matcha White Chocolate']);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 28000,
            'quantity' => 2,
            'subtotal' => 56000,
        ]);

        $response = $this->get(route('customer.orders', ['search' => 'NK-20261023-TEST1']));

        $response->assertStatus(200);
        $response->assertSee('NK-20261023-TEST1');
        $response->assertSee('Nicha Thanakorn');
        $response->assertSee('Matcha White Chocolate');
        $response->assertSee('Di Jalan');
    }

    public function test_can_search_orders_by_customer_phone(): void
    {
        $order1 = Order::factory()->create([
            'order_number' => 'NK-20261023-PHON1',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'status' => 'delivered',
        ]);

        $order2 = Order::factory()->create([
            'order_number' => 'NK-20261023-PHON2',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'status' => 'shipped',
        ]);

        $orderOther = Order::factory()->create([
            'order_number' => 'NK-20261023-OTHER',
            'customer_name' => 'Rina Wijaya',
            'customer_phone' => '089999999999',
            'status' => 'pending',
        ]);

        $response = $this->get(route('customer.orders', ['search' => '081234567890']));

        $response->assertStatus(200);
        $response->assertSee('NK-20261023-PHON1');
        $response->assertSee('NK-20261023-PHON2');
        $response->assertDontSee('NK-20261023-OTHER');
    }

    public function test_can_filter_orders_by_status_tabs(): void
    {
        $shippedOrder = Order::factory()->create([
            'order_number' => 'NK-SHIPPED-01',
            'customer_phone' => '081211112222',
            'status' => 'shipped',
        ]);

        $deliveredOrder = Order::factory()->create([
            'order_number' => 'NK-DELIVERED-01',
            'customer_phone' => '081211112222',
            'status' => 'delivered',
        ]);

        $cancelledOrder = Order::factory()->create([
            'order_number' => 'NK-CANCELLED-01',
            'customer_phone' => '081211112222',
            'status' => 'cancelled',
        ]);

        // Filter: Di Jalan (shipped)
        $resShipped = $this->get(route('customer.orders', [
            'search' => '081211112222',
            'status' => 'shipped',
        ]));
        $resShipped->assertStatus(200);
        $resShipped->assertSee('NK-SHIPPED-01');
        $resShipped->assertDontSee('NK-DELIVERED-01');
        $resShipped->assertDontSee('NK-CANCELLED-01');

        // Filter: Sudah Sampai (delivered)
        $resDelivered = $this->get(route('customer.orders', [
            'search' => '081211112222',
            'status' => 'delivered',
        ]));
        $resDelivered->assertStatus(200);
        $resDelivered->assertSee('NK-DELIVERED-01');
        $resDelivered->assertDontSee('NK-SHIPPED-01');

        // Filter: Dibatalkan (cancelled)
        $resCancelled = $this->get(route('customer.orders', [
            'search' => '081211112222',
            'status' => 'cancelled',
        ]));
        $resCancelled->assertStatus(200);
        $resCancelled->assertSee('NK-CANCELLED-01');
        $resCancelled->assertDontSee('NK-SHIPPED-01');
    }

    public function test_can_load_orders_via_comma_separated_device_order_numbers(): void
    {
        $order1 = Order::factory()->create([
            'order_number' => 'NK-DEV-001',
            'status' => 'shipped',
        ]);

        $order2 = Order::factory()->create([
            'order_number' => 'NK-DEV-002',
            'status' => 'delivered',
        ]);

        $orderOther = Order::factory()->create([
            'order_number' => 'NK-DEV-999',
            'status' => 'paid',
        ]);

        $response = $this->get(route('customer.orders', [
            'orders' => 'NK-DEV-001,NK-DEV-002',
        ]));

        $response->assertStatus(200);
        $response->assertSee('NK-DEV-001');
        $response->assertSee('NK-DEV-002');
        $response->assertDontSee('NK-DEV-999');
    }

    public function test_order_model_helper_methods(): void
    {
        $shipped = Order::factory()->create(['status' => 'shipped']);
        $delivered = Order::factory()->create(['status' => 'delivered']);
        $cancelled = Order::factory()->create(['status' => 'cancelled']);

        $this->assertTrue($shipped->isShipped());
        $this->assertFalse($shipped->isDelivered());
        $this->assertEquals(4, $shipped->statusStepIndex());
        $this->assertEquals('🛵', $shipped->statusIcon());

        $this->assertTrue($delivered->isDelivered());
        $this->assertEquals(5, $delivered->statusStepIndex());
        $this->assertEquals('✅', $delivered->statusIcon());

        $this->assertTrue($cancelled->isCancelled());
        $this->assertEquals(-1, $cancelled->statusStepIndex());
        $this->assertEquals('❌', $cancelled->statusIcon());
    }
}
