<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs with search and category filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->query('search'),
            'role' => $request->query('role', 'all'),
            'category' => $request->query('category', 'all'),
            'date' => $request->query('date', 'all'),
        ];

        $logs = ActivityLog::query()
            ->with('user')
            ->filter($filters)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_today' => ActivityLog::query()->whereDate('created_at', today())->count(),
            'auth_today' => ActivityLog::query()->where('category', 'auth')->whereDate('created_at', today())->count(),
            'order_today' => ActivityLog::query()->where('category', 'order')->whereDate('created_at', today())->count(),
            'staff_actions_today' => ActivityLog::query()->whereIn('user_role', ['admin', 'seller', 'customer_service'])->whereDate('created_at', today())->count(),
        ];

        return view('admin.activity_logs.index', compact('logs', 'filters', 'stats'));
    }
}
