<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CustomerGoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth or simulation mode.
     */
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect_uri') ?: route('auth.google.callback');

        if (! empty($clientId)) {
            $query = http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'access_type' => 'online',
            ]);

            return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
        }

        // Demo / Simulation Mode when Google OAuth credentials not yet configured in .env
        return $this->simulate();
    }

    /**
     * Handle the OAuth callback from Google.
     */
    public function callback(Request $request): RedirectResponse
    {
        $code = $request->query('code');

        if (! $code) {
            return redirect()->route('home')
                ->with('error', 'Gagal menghubungkan akun Google. Silakan coba lagi.');
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect_uri') ?: route('auth.google.callback');

        try {
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($tokenResponse->failed()) {
                return redirect()->route('home')
                    ->with('error', 'Gagal memverifikasi token Google.');
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            $userResponse = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userResponse->failed()) {
                return redirect()->route('home')
                    ->with('error', 'Gagal mengambil data profil Google.');
            }

            $userData = $userResponse->json();
            $user = $this->findOrCreateGoogleUser($userData);

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))
                ->with('success', 'Berhasil masuk dengan Google! Diskon member Rp 10.000 aktif.');
        } catch (\Throwable $e) {
            return redirect()->route('home')
                ->with('error', 'Terjadi kesalahan saat masuk dengan Google: '.$e->getMessage());
        }
    }

    /**
     * Simulate Google Login for local development and demonstration.
     */
    public function simulate(): RedirectResponse
    {
        $mockData = [
            'sub' => 'google_mock_'.rand(1000, 9999),
            'email' => 'google.member@gmail.com',
            'name' => 'Aditya Pratama (Google Member)',
            'picture' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
        ];

        $user = $this->findOrCreateGoogleUser($mockData);

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(url()->previous())
            ->with('success', 'Berhasil masuk via Google (Mode Demo)! Diskon member Rp 10.000 telah aktif.');
    }

    /**
     * Find or create customer user from Google profile data.
     *
     * @param  array<string, mixed>  $data
     */
    protected function findOrCreateGoogleUser(array $data): User
    {
        $email = strtolower($data['email']);
        $googleId = (string) ($data['sub'] ?? $data['id'] ?? '');

        // 1. Try finding by google_id
        $user = User::query()->where('google_id', $googleId)->first();

        // 2. Try finding by email
        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        if ($user) {
            $user->update([
                'google_id' => $googleId,
                'avatar_url' => $data['picture'] ?? $user->avatar_url,
            ]);

            return $user;
        }

        return User::query()->create([
            'name' => $data['name'] ?? 'Pelanggan Google',
            'email' => $email,
            'google_id' => $googleId,
            'avatar_url' => $data['picture'] ?? null,
            'password' => Str::random(32),
            'role' => 'customer',
            'auth_provider' => 'google',
            'member_discount_amount' => 10000,
        ]);
    }
}
