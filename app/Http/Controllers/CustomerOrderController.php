<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackOrderRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    /**
     * Display customer order tracking and history list.
     */
    public function index(TrackOrderRequest $request): View
    {
        $validated = $request->validated();
        $search = trim($validated['search'] ?? '');
        $status = $validated['status'] ?? 'all';
        $orderNumbersString = trim($validated['orders'] ?? '');

        $orderNumbers = [];
        if (! empty($orderNumbersString)) {
            $orderNumbers = array_filter(
                array_map('trim', explode(',', $orderNumbersString)),
                fn ($val) => ! empty($val)
            );
        }

        $query = Order::query()
            ->with(['items.product', 'greetingCardTemplate'])
            ->latest();

        $hasFilter = false;

        if (! empty($search)) {
            $hasFilter = true;
            $query->where(function (Builder $q) use ($search) {
                $q->where('order_number', $search)
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_email', $search);
            });
        } elseif (! empty($orderNumbers)) {
            $hasFilter = true;
            $query->whereIn('order_number', $orderNumbers);
        }

        // Calculate status counts on the matched scope
        $baseScopeQuery = clone $query;
        $scopedOrders = $hasFilter ? $baseScopeQuery->get() : collect();

        $statusCounts = [
            'all' => $scopedOrders->count(),
            'shipped' => $scopedOrders->where('status', 'shipped')->count(),
            'delivered' => $scopedOrders->where('status', 'delivered')->count(),
            'processing' => $scopedOrders->whereIn('status', ['paid', 'baking'])->count(),
            'cancelled' => $scopedOrders->where('status', 'cancelled')->count(),
            'pending' => $scopedOrders->where('status', 'pending')->count(),
        ];

        // Apply status tab filtering
        if ($hasFilter && $status !== 'all') {
            if ($status === 'processing') {
                $query->whereIn('status', ['paid', 'baking']);
            } else {
                $query->where('status', $status);
            }
        }

        $orders = $hasFilter
            ? $query->paginate(10)->withQueryString()
            : Order::query()->whereRaw('1 = 0')->paginate(10);

        return view('shop.orders', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'activeStatus' => $status,
            'searchQuery' => $search,
            'hasFilter' => $hasFilter,
            'orderNumbersString' => $orderNumbersString,
        ]);
    }
}
