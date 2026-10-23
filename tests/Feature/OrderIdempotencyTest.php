<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_order_twice_with_same_idempotency_key_does_not_create_duplicate_order(): void
    {
        $product = Product::factory()->create([
            'name' => 'Signature Dark Choco',
            'price' => 30000,
            'sale_price' => null,
            'is_active' => true,
        ]);

        $payload = [
            'idempotency_key' => 'idemp-test-uuid-001',
            'customer_name' => 'Aditya Pratama',
            'customer_phone' => '081234567890',
            'customer_address' => 'Jl. Sudirman Kav 20, Jakarta',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        // First click / submission
        $response1 = $this->postJson(route('checkout.place'), $payload);
        $response1->assertStatus(200);
        $response1->assertJson(['success' => true]);

        $orderNumber1 = $response1->json('order_number');
        $this->assertNotEmpty($orderNumber1);

        // Instant duplicate double-click with identical idempotency_key
        $response2 = $this->postJson(route('checkout.place'), $payload);
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true]);

        $orderNumber2 = $response2->json('order_number');

        // Assert: Both responses return the exact same order number
        $this->assertSame($orderNumber1, $orderNumber2);

        // Assert: Database has strictly ONE order, NOT duplicate orders
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'order_number' => $orderNumber1,
            'idempotency_key' => 'idemp-test-uuid-001',
            'customer_name' => 'Aditya Pratama',
            'subtotal' => 60000,
            'shipping_cost' => 25000,
            'total_amount' => 85000,
        ]);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_submitting_with_header_idempotency_key_is_also_idempotent(): void
    {
        $product = Product::factory()->create([
            'name' => 'Lotus Biscoff Lava',
            'price' => 35000,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Nadia Saphira',
            'customer_phone' => '081987654321',
            'customer_address' => 'Jl. Senopati No. 8, Jakarta',
            'shipping_method' => 'paxel_food',
            'payment_method' => 'xendit_va',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $headerKey = 'header-idemp-uuid-777';

        // First call with header
        $response1 = $this->postJson(route('checkout.place'), $payload, [
            'X-Idempotency-Key' => $headerKey,
        ]);
        $response1->assertStatus(200);
        $orderNumber1 = $response1->json('order_number');

        // Rapid second call with same header
        $response2 = $this->postJson(route('checkout.place'), $payload, [
            'X-Idempotency-Key' => $headerKey,
        ]);
        $response2->assertStatus(200);
        $orderNumber2 = $response2->json('order_number');

        $this->assertSame($orderNumber1, $orderNumber2);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'idempotency_key' => $headerKey,
        ]);
    }

    public function test_submitting_with_different_idempotency_keys_creates_distinct_orders(): void
    {
        $product = Product::factory()->create([
            'name' => 'Matcha White Choco',
            'price' => 28000,
            'is_active' => true,
        ]);

        $basePayload = [
            'customer_name' => 'Kevin Sanjaya',
            'customer_phone' => '081122334455',
            'customer_address' => 'Jl. Menteng No. 10',
            'shipping_method' => 'regular_jne',
            'payment_method' => 'xendit_qris',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        // Call 1
        $res1 = $this->postJson(route('checkout.place'), array_merge($basePayload, [
            'idempotency_key' => 'order-key-alpha',
        ]));
        $res1->assertStatus(200);

        // Call 2 with different key
        $res2 = $this->postJson(route('checkout.place'), array_merge($basePayload, [
            'idempotency_key' => 'order-key-beta',
        ]));
        $res2->assertStatus(200);

        $this->assertNotSame($res1->json('order_number'), $res2->json('order_number'));
        $this->assertDatabaseCount('orders', 2);
    }

    public function test_simulate_payment_is_idempotent_when_called_multiple_times(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'NK-IDEMP-PAY-1',
            'status' => 'pending',
        ]);

        // First payment simulation
        $response1 = $this->post(route('orders.simulate-pay', 'NK-IDEMP-PAY-1'));
        $response1->assertRedirect(route('orders.show', 'NK-IDEMP-PAY-1'));
        $this->assertDatabaseHas('orders', [
            'order_number' => 'NK-IDEMP-PAY-1',
            'status' => 'paid',
        ]);

        // Second payment simulation (double click simulation)
        $response2 = $this->post(route('orders.simulate-pay', 'NK-IDEMP-PAY-1'));
        $response2->assertRedirect(route('orders.show', 'NK-IDEMP-PAY-1'));

        $this->assertDatabaseHas('orders', [
            'order_number' => 'NK-IDEMP-PAY-1',
            'status' => 'paid',
        ]);
    }
}
