<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Authenticate staff user credentials.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->isStaff()) {
                ActivityLogger::auth('auth.staff_login_forbidden', 'Upaya login staf ditolak (bukan staf internal): '.$user->email, $user);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda tidak memiliki hak akses portal staf.',
                ]);
            }

            ActivityLogger::auth(
                'auth.staff_login',
                $user->role_label.' '.$user->name.' berhasil masuk ke Panel Kontrol.',
                $user
            );

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, '.$user->name.' ('.$user->role_label.')!');
        }

        ActivityLogger::log(
            'auth.staff_login_failed',
            'auth',
            'Percobaan login staf gagal untuk email: '.$request->input('email'),
            null,
            ['email' => $request->input('email')]
        );

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Log out staff user.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::auth(
                'auth.staff_logout',
                $user->role_label.' '.$user->name.' telah keluar dari Panel Kontrol.',
                $user
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
