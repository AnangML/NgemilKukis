<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Display a listing of promo coupons.
     */
    public function index(Request $request): View
    {
        $query = Coupon::query()->withCount('usages')->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::active()->count(),
            'total_used' => Coupon::sum('used_count'),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_stackable_with_member'] = $request->boolean('is_stackable_with_member', true);

        $coupon = Coupon::create($data);

        ActivityLogger::catalog(
            'coupon.created',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." membuat kupon promo baru: {$coupon->code} ({$coupon->formatted_value})",
            $coupon,
            ['code' => $coupon->code, 'type' => $coupon->type, 'value' => $coupon->value]
        );

        return redirect()->route('admin.coupons.index')
            ->with('success', "Kupon promo '{$coupon->code}' berhasil ditambahkan!");
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['is_stackable_with_member'] = $request->boolean('is_stackable_with_member');

        $coupon->update($data);

        ActivityLogger::catalog(
            'coupon.updated',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." memperbarui kupon promo: {$coupon->code}",
            $coupon,
            ['code' => $coupon->code, 'type' => $coupon->type, 'value' => $coupon->value]
        );

        return redirect()->route('admin.coupons.index')
            ->with('success', "Kupon promo '{$coupon->code}' berhasil diperbarui!");
    }

    /**
     * Toggle the active status of the coupon.
     */
    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        $stateText = $coupon->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLogger::catalog(
            'coupon.status_toggled',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." mengubah status kupon {$coupon->code} menjadi ".($coupon->is_active ? 'Aktif' : 'Nonaktif'),
            $coupon,
            ['is_active' => $coupon->is_active]
        );

        return back()->with('success', "Status kupon '{$coupon->code}' berhasil {$stateText}.");
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $code = $coupon->code;
        $coupon->delete();

        ActivityLogger::catalog(
            'coupon.deleted',
            (auth()->user()?->role_label ?? 'Staf').' '.(auth()->user()?->name ?? '')." menghapus kupon promo: {$code}",
            null,
            ['code' => $code]
        );

        return redirect()->route('admin.coupons.index')
            ->with('success', "Kupon promo '{$code}' telah dihapus.");
    }
}
