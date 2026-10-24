<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerAddressRequest;
use App\Http\Requests\Customer\UpdateCustomerProfileRequest;
use App\Models\CustomerAddress;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Display the customer account dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = Order::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        if ($user->phone) {
                            $q->where('customer_phone', $user->phone);
                        }
                    });
            })
            ->with(['items.product', 'greetingCardTemplate'])
            ->latest()
            ->paginate(10);

        $addresses = $user->addresses()->latest()->get();

        return view('shop.account', compact('user', 'orders', 'addresses'));
    }

    /**
     * Update customer profile and primary saved address.
     */
    public function updateProfile(UpdateCustomerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? $user->phone,
            'saved_address' => $validated['saved_address'] ?? null,
            'saved_city' => $validated['saved_city'] ?? null,
        ]);

        return back()->with('success', 'Profil dan alamat utama berhasil diperbarui!');
    }

    /**
     * Add a new saved shipping address to the address book.
     */
    public function storeAddress(StoreCustomerAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->boolean('is_primary')) {
            $user->addresses()->update(['is_primary' => false]);
        }

        $user->addresses()->create([
            'label' => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'full_address' => $validated['full_address'],
            'city' => $validated['city'],
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan ke buku alamat!');
    }

    /**
     * Delete a saved address.
     */
    public function deleteAddress(Request $request, CustomerAddress $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus dari buku alamat.');
    }
}
