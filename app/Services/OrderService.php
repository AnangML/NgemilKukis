<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Resolve the effective shipping rate from dynamic SiteSetting or fallback default.
     */
    public function getShippingRate(string $method): int
    {
        $dynamicRate = SiteSetting::get('shipping_'.$method);

        if ($dynamicRate !== null && is_numeric($dynamicRate)) {
            return (int) $dynamicRate;
        }

        return self::SHIPPING_RATES[$method] ?? 15000;
    }

    /**
     * Shipping rates lookup table in IDR.
     *
     * @var array<string, int>
     */
    public const SHIPPING_RATES = [
        'instant_grab' => 25000,
        'sameday_gojek' => 18000,
        'paxel_food' => 22000,
        'regular_jne' => 12000,
    ];

    public function __construct(
        protected XenditPaymentService $xenditService,
    ) {}

    /**
     * Create an order idempotently. If an order with the provided idempotency
     * key already exists or is being processed concurrently, return the existing order.
     *
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data, ?string $idempotencyKey = null): Order
    {
        // 1. If an idempotency key is provided, check if it was already fulfilled.
        if (! empty($idempotencyKey)) {
            $existing = Order::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }

            // 2. Prevent concurrent race condition using an atomic cache lock.
            $lockKey = 'order_idempotency_lock:'.$idempotencyKey;

            return Cache::lock($lockKey, 15)->block(10, function () use ($data, $idempotencyKey) {
                // Re-check existence inside lock to avoid duplicate work.
                $existing = Order::query()->where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $existing;
                }

                try {
                    return $this->executeOrderCreation($data, $idempotencyKey);
                } catch (UniqueConstraintViolationException|QueryException $e) {
                    // Fallback in case of database-level duplicate race: return the existing order.
                    $existing = Order::query()->where('idempotency_key', $idempotencyKey)->first();
                    if ($existing) {
                        return $existing;
                    }

                    throw $e;
                }
            });
        }

        // 3. Fallback when no idempotency key is passed: create directly.
        return $this->executeOrderCreation($data, null);
    }

    /**
     * Execute the atomic database transaction to persist the order and its items.
     *
     * @param  array<string, mixed>  $data
     */
    protected function executeOrderCreation(array $data, ?string $idempotencyKey): Order
    {
        $shippingCost = $this->getShippingRate($data['shipping_method'] ?? '');

        return DB::transaction(function () use ($data, $shippingCost, $idempotencyKey) {
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($data['items'] as $cartItem) {
                $product = Product::query()->findOrFail($cartItem['id']);
                $itemPrice = $product->effectivePrice();
                $itemQty = (int) $cartItem['quantity'];
                $itemSubtotal = $itemPrice * $itemQty;
                $subtotal += $itemSubtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $itemPrice,
                    'quantity' => $itemQty,
                    'subtotal' => $itemSubtotal,
                    'notes' => $cartItem['notes'] ?? null,
                ];
            }

            $totalAmount = $subtotal + $shippingCost;
            $orderNumber = 'NK-'.date('Ymd').'-'.strtoupper(Str::random(5));

            $order = Order::query()->create([
                'order_number' => $orderNumber,
                'idempotency_key' => $idempotencyKey,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_address' => $data['customer_address'],
                'customer_city' => $data['customer_city'] ?? null,
                'shipping_method' => $data['shipping_method'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'xendit_invoice_id' => null,
                'xendit_invoice_url' => null,
                'is_gift' => (bool) ($data['is_gift'] ?? false),
                'gift_recipient_name' => $data['gift_recipient_name'] ?? null,
                'gift_sender_name' => $data['gift_sender_name'] ?? null,
                'gift_card_template_id' => $data['gift_card_template_id'] ?? null,
                'gift_message' => $data['gift_message'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            // Generate Xendit invoice (Live or mock fallback)
            $order->load('items');
            $invoice = $this->xenditService->createInvoice($order);

            $order->update([
                'xendit_invoice_id' => $invoice['id'],
                'xendit_invoice_url' => $invoice['invoice_url'],
            ]);

            return $order;
        });
    }
}
