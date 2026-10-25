<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LoyaltyPointService;
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

        app(LoyaltyPointService::class)->awardWelcomeBonus($user, 50);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::auth(
            'auth.customer_register',
            'Member baru mendaftar via email: '.$user->name.' ('.$user->email.')',
            $user
        );

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
            $user = Auth::user();

            ActivityLogger::auth(
                'auth.customer_login',
                'Member masuk via email: '.$user->name.' ('.$user->email.')',
                $user
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Selamat datang kembali, '.$user->name.'! Diskon member aktif.',
                    'user' => $user,
                    'redirect_url' => url()->previous(),
                ]);
            }

            return redirect()->intended(url()->previous())
                ->with('success', 'Selamat datang kembali, '.$user->name.'! Diskon member aktif.');
        }

        ActivityLogger::log(
            'auth.customer_login_failed',
            'auth',
            'Percobaan login member gagal untuk email: '.$request->input('email'),
            null,
            ['email' => $request->input('email')]
        );

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
        $user = Auth::user();
        if ($user) {
            ActivityLogger::auth(
                'auth.customer_logout',
                'Member keluar akun: '.$user->name.' ('.$user->email.')',
                $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
