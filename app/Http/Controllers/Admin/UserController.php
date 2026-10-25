<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of internal staff and users with filters.
     */
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        $roleFilter = $request->query('role', 'staff');
        if ($roleFilter === 'staff') {
            $query->whereIn('role', ['admin', 'seller', 'customer_service']);
        } elseif ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $roleCounts = [
            'all' => User::count(),
            'staff' => User::whereIn('role', ['admin', 'seller', 'customer_service'])->count(),
            'admin' => User::where('role', 'admin')->count(),
            'seller' => User::where('role', 'seller')->count(),
            'customer_service' => User::where('role', 'customer_service')->count(),
            'customer' => User::where('role', 'customer')->count(),
        ];

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'auth_provider' => 'email',
        ]);

        ActivityLogger::staff(
            'staff.created',
            auth()->user()->name.' menambahkan staf baru: '.$user->name.' sebagai '.$user->role_label,
            $user,
            ['name' => $user->name, 'email' => $user->email, 'role' => $user->role]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Staf baru '{$user->name}' ({$user->role_label}) berhasil ditambahkan!");
    }

    /**
     * Update the specified staff member.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Prevent self-demotion from admin
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat mengubah peran akun Anda sendiri dari Administrator.']);
        }

        $oldRole = $user->role;
        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLogger::staff(
            'staff.updated',
            auth()->user()->name.' memperbarui profil/peran staf: '.$user->name.($oldRole !== $user->role ? " (Peran diubah dari {$oldRole} ➔ {$user->role})" : ''),
            $user,
            ['old_role' => $oldRole, 'new_role' => $user->role]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna '{$user->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $roleLabel = $user->role_label;
        $user->delete();

        ActivityLogger::staff(
            'staff.deleted',
            auth()->user()->name." menghapus staf/pengguna: {$name} ({$roleLabel})",
            null,
            ['name' => $name, 'role' => $roleLabel]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna '{$name}' telah dihapus dari sistem.");
    }
}
