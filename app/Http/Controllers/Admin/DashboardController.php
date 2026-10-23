<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin overview dashboard with KPIs and recent activity.
     */
    public function index(): View
    {
        $totalRevenue = Order::whereIn('status', ['paid', 'baking', 'shipped', 'delivered'])->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $paidOrders = Order::whereIn('status', ['paid', 'baking'])->count();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('stock', '<=', 5)->count();

        $recentOrders = Order::with('items.product')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'paidOrders',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'recentOrders'
        ));
    }
}
