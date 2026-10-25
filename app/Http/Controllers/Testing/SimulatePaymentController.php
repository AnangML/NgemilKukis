<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ActivityLogger;
use App\Services\LoyaltyPointService;
use Illuminate\Http\RedirectResponse;

class SimulatePaymentController extends Controller
{
    /**
     * Simulate an instant payment confirmation for local development and testing.
     */
    public function __invoke(string $orderNumber): RedirectResponse
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->status === 'paid') {
            return redirect()
                ->route('orders.show', $orderNumber)
                ->with('status', 'Pembayaran untuk pesanan ini telah terverifikasi lunas sebelumnya.');
        }

        $order->update([
            'status' => 'paid',
        ]);

        app(LoyaltyPointService::class)->earnPointsForOrder($order);

        ActivityLogger::order(
            'order.paid_simulated',
            "Pembayaran pesanan #{$order->order_number} lunas dikonfirmasi via Simulasi Pembayaran (Rp ".number_format($order->grand_total, 0, ',', '.').').',
            $order,
            ['order_number' => $order->order_number, 'grand_total' => $order->grand_total]
        );

        return redirect()
            ->route('orders.show', $orderNumber)
            ->with('status', 'Pembayaran berhasil dikonfirmasi via Xendit! Kukis segera disiapkan.');
    }
}
