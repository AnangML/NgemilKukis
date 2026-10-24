<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPhoneOtpRequest;
use App\Http\Requests\Auth\VerifyPhoneOtpRequest;
use App\Models\User;
use App\Services\PhoneOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerPhoneAuthController extends Controller
{
    public function __construct(
        protected PhoneOtpService $otpService,
    ) {}

    /**
     * Send or simulate OTP code to customer phone number.
     */
    public function sendOtp(SendPhoneOtpRequest $request): JsonResponse
    {
        $phone = $request->input('phone');
        $result = $this->otpService->sendOtp($phone);

        return response()->json($result);
    }

    /**
     * Verify customer OTP and log them in.
     */
    public function verifyOtp(VerifyPhoneOtpRequest $request): JsonResponse
    {
        $phone = $request->input('phone');
        $code = $request->input('otp_code');
        $cleanPhone = $this->otpService->normalizePhone($phone);

        $verified = $this->otpService->verifyOtp($phone, $code);

        if (! $verified) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah atau sudah kedaluwarsa.',
            ], 422);
        }

        // Find existing customer by phone, or create a new one
        $user = User::query()->where('phone', $cleanPhone)->first();

        if (! $user) {
            $displayName = $request->input('name') ?: 'Sahabat Kukis '.substr($cleanPhone, -4);
            $user = User::query()->create([
                'name' => $displayName,
                'email' => $cleanPhone.'@member.ngemilkukis.com',
                'phone' => $cleanPhone,
                'password' => Str::random(32),
                'role' => 'customer',
                'auth_provider' => 'phone',
                'phone_verified_at' => now(),
                'member_discount_amount' => 10000,
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil masuk! Selamat datang di Kukis VIP Club.',
            'redirect' => url()->previous(),
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
            ],
        ]);
    }
}
