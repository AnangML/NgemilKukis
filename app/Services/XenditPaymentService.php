<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class XenditPaymentService
{
    /**
     * Xendit Invoices API base endpoint.
     */
    protected const API_URL = 'https://api.xendit.co/v2/invoices';

    /**
     * Create an official invoice with Xendit.
     * If credentials are not configured or placeholder, fall back gracefully to local mock.
     *
     * @return array{id: string, invoice_url: string, status: string, is_live: bool}
     */
    public function createInvoice(Order $order): array
    {
        $secretKey = config('services.xendit.secret_key');

        // Check if a real Secret Key is provided
        if (! $this->isConfiguredKey($secretKey)) {
            return $this->generateMockInvoice($order);
        }

        try {
            $itemsPayload = $order->items->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            })->all();

            $paymentMethods = match ($order->payment_method) {
                'xendit_qris' => ['QRIS'],
                'xendit_va' => ['BCA', 'BNI', 'BRI', 'MANDIRI', 'PERMATA'],
                default => ['QRIS', 'BCA', 'BNI', 'BRI', 'MANDIRI', 'OVO', 'DANA', 'SHOPEEPAY'],
            };

            $response = Http::withBasicAuth($secretKey, '')
                ->timeout(12)
                ->post(self::API_URL, [
                    'external_id' => $order->order_number,
                    'amount' => $order->total_amount,
                    'description' => "Pesanan NgemilKukis Gourmet #{$order->order_number}",
                    'payer_email' => $order->customer_email ?: 'order@ngemilkukis.com',
                    'customer' => [
                        'given_names' => $order->customer_name,
                        'mobile_number' => $order->customer_phone,
                        'email' => $order->customer_email ?: 'order@ngemilkukis.com',
                        'addresses' => [
                            [
                                'street_line1' => $order->customer_address,
                                'city' => $order->customer_city ?? 'Jakarta',
                                'country' => 'Indonesia',
                            ],
                        ],
                    ],
                    'items' => $itemsPayload,
                    'fees' => [
                        [
                            'type' => 'Shipping',
                            'value' => $order->shipping_cost,
                        ],
                    ],
                    'payment_methods' => $paymentMethods,
                    'success_redirect_url' => route('orders.show', $order->order_number),
                    'failure_redirect_url' => route('checkout'),
                    'currency' => 'IDR',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'id' => $data['id'] ?? 'xnd_'.Str::random(12),
                    'invoice_url' => $data['invoice_url'] ?? route('orders.show', $order->order_number),
                    'status' => $data['status'] ?? 'PENDING',
                    'is_live' => true,
                ];
            }

            Log::error('Xendit Invoice creation failed.', [
                'status' => $response->status(),
                'response' => $response->json(),
                'order_number' => $order->order_number,
            ]);

            return $this->generateMockInvoice($order);
        } catch (\Throwable $e) {
            Log::error('Xendit Invoice connection exception.', [
                'error' => $e->getMessage(),
                'order_number' => $order->order_number,
            ]);

            return $this->generateMockInvoice($order);
        }
    }

    /**
     * Verify whether the incoming webhook callback token matches the configured token.
     */
    public function verifyWebhookToken(?string $token): bool
    {
        $configuredToken = config('services.xendit.webhook_token');

        // If no token is set or it's default placeholder, permit local testing
        if (empty($configuredToken) || str_contains($configuredToken, 'token_verifikasi') || str_contains($configuredToken, 'your_webhook')) {
            return true;
        }

        return hash_equals((string) $configuredToken, (string) $token);
    }

    /**
     * Check if the secret key is configured and not a placeholder.
     */
    public function isConfiguredKey(?string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        if (str_contains($key, 'xxxxxxxx') || str_contains($key, 'your_')) {
            return false;
        }

        return str_starts_with($key, 'xnd_development_') || str_starts_with($key, 'xnd_production_');
    }

    /**
     * Generate fallback/mock invoice payload for testing or development.
     *
     * @return array{id: string, invoice_url: string, status: string, is_live: bool}
     */
    protected function generateMockInvoice(Order $order): array
    {
        return [
            'id' => 'mock_xnd_'.Str::random(12),
            'invoice_url' => route('orders.show', $order->order_number),
            'status' => 'PENDING',
            'is_live' => false,
        ];
    }
}
