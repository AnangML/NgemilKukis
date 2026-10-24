<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerEmailAuthController extends Controller
{
    /**
     * Handle manual email registration (Option 1: Instant activation).
     */
    public function register(CustomerRegisterRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'auth_provider' => 'email',
            'member_discount_amount' => 10000,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Selamat datang di Kukis VIP Club! Diskon Rp 10.000 telah aktif.',
                'user' => $user,
                'redirect_url' => url()->previous(),
            ]);
        }

        return redirect()->intended(url()->previous())
            ->with('success', 'Selamat datang di Kukis VIP Club! Diskon Rp 10.000 telah aktif.');
    }

    /**
     * Handle manual email login.
     */
    public function login(CustomerLoginRequest $request): JsonResponse|RedirectResponse
    {
        $credentials = [
            'email' => strtolower($request->input('email')),
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Selamat datang kembali, '.Auth::user()->name.'! Diskon member aktif.',
                    'user' => Auth::user(),
                    'redirect_url' => url()->previous(),
                ]);
            }

            return redirect()->intended(url()->previous())
                ->with('success', 'Selamat datang kembali, '.Auth::user()->name.'! Diskon member aktif.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password yang Anda masukkan salah.',
                'errors' => ['email' => ['Email atau password yang Anda masukkan salah.']],
            ], 422);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.']);
    }

    /**
     * Handle customer logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
