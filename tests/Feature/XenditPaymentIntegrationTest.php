<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_real_xendit_invoice_when_api_key_configured(): void
    {
        // Arrange
        Config::set('services.xendit.secret_key', 'xnd_development_test_key_12345');

        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'xnd_live_inv_12345678',
                'invoice_url' => 'https://checkout.xendit.co/web/6718_test_slug',
                'status' => 'PENDING',
            ], 200),
        ]);

        $product = Product::factory()->create([
            'name' => 'Salted Caramel Crunch',
            'price' => 32000,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Rina Gunawan',
            'customer_phone' => '081234567890',
            'customer_email' => 'rina@example.com',
            'customer_address' => 'Jl. Cipete Raya No. 4, Jakarta',
            'shipping_method' => 'instant_grab',
            'payment_method' => 'xendit_qris',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        // Act
        $response = $this->postJson(route('checkout.place'), $payload);

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect_url' => 'https://checkout.xendit.co/web/6718_test_slug',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Rina Gunawan',
            'xendit_invoice_id' => 'xnd_live_inv_12345678',
            'xendit_invoice_url' => 'https://checkout.xendit.co/web/6718_test_slug',
            'status' => 'pending',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.xendit.co/v2/invoices'
                && $request['amount'] === (32000 + 25000); // product + instant_grab
        });
    }

    public function test_xendit_webhook_updates_order_status_to_paid_with_valid_token(): void
    {
        // Arrange
        $webhookToken = 'sec_token_xendit_valid_9988';
        Config::set('services.xendit.webhook_token', $webhookToken);

        $order = Order::factory()->create([
            'order_number' => 'NK-XND-TEST-001',
            'status' => 'pending',
        ]);

        // Act: Xendit sends PAID webhook notification
        $response = $this->postJson(route('xendit.webhook'), [
            'id' => 'inv_test_123',
            'external_id' => 'NK-XND-TEST-001',
            'status' => 'PAID',
            'paid_amount' => 50000,
            'payment_method' => 'QRIS',
        ], [
            'x-callback-token' => $webhookToken,
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('orders', [
            'order_number' => 'NK-XND-TEST-001',
            'status' => 'paid',
        ]);
    }

    public function test_xendit_webhook_rejects_unauthorized_token(): void
    {
        // Arrange
        Config::set('services.xendit.webhook_token', 'legitimate_token_123');

        $order = Order::factory()->create([
            'order_number' => 'NK-XND-TEST-002',
            'status' => 'pending',
        ]);

        // Act: Imposter sends webhook with wrong callback token
        $response = $this->postJson(route('xendit.webhook'), [
            'external_id' => 'NK-XND-TEST-002',
            'status' => 'PAID',
        ], [
            'x-callback-token' => 'invalid_imposter_token',
        ]);

        // Assert
        $response->assertStatus(401);
        $response->assertJson(['status' => 'error']);

        // Order remains pending
        $this->assertDatabaseHas('orders', [
            'order_number' => 'NK-XND-TEST-002',
            'status' => 'pending',
        ]);
    }
}
