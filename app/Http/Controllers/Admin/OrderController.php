<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with optional status and search filters.
     */
    public function index(Request $request): View
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $statusCounts = Order::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Display the specified order with customer details, items, and gift card info.
     */
    public function show(Order $order): View
    {
        $order->load(['items.product', 'greetingCardTemplate']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the fulfillment or payment status of an order.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $newStatus = $request->validated('status');
        $oldStatus = $order->status;

        $order->update(['status' => $newStatus]);

        $user = $request->user();
        ActivityLogger::order(
            'order.status_updated',
            ($user?->role_label ?? 'Staf').' '.($user?->name ?? '')." memperbarui status pesanan #{$order->order_number} dari '{$oldStatus}' menjadi '{$newStatus}'.",
            $order,
            [
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            $user
        );

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diubah dari '{$oldStatus}' menjadi '{$newStatus}'.");
    }
}
