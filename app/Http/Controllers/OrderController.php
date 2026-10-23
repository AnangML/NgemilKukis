<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    /**
     * Store a newly created order from checkout.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $idempotencyKey = $validated['idempotency_key']
            ?? $request->header('X-Idempotency-Key')
            ?? null;

        $order = $this->orderService->createOrder($validated, $idempotencyKey);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'order_number' => $order->order_number,
            'redirect_url' => $order->xendit_invoice_url ?: route('orders.show', $order->order_number),
        ]);
    }

    /**
     * Display order confirmation, receipt, and invoice.
     */
    public function show(string $orderNumber): View
    {
        $order = Order::query()
            ->with(['items.product', 'greetingCardTemplate'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.order-confirmation', [
            'order' => $order,
        ]);
    }

    /**
     * Get real-time payment and order status for polling.
     */
    public function status(string $orderNumber): JsonResponse
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'is_paid' => $order->status === 'paid',
            'status_label' => $order->statusLabel(),
            'invoice_url' => $order->xendit_invoice_url,
        ]);
    }
}
