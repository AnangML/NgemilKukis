<?php

namespace App\Services;

use App\Models\PhoneOtp;
use Illuminate\Support\Facades\Hash;

class PhoneOtpService
{
    /**
     * Send or simulate sending a 4-digit OTP code to the given phone number.
     *
     * @return array{success: bool, message: string, simulation_code?: string}
     */
    public function sendOtp(string $phone): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        // Invalidate any active previous unverified OTPs for this phone
        PhoneOtp::query()
            ->where('phone', $normalizedPhone)
            ->whereNull('verified_at')
            ->delete();

        // Generate 4-digit numeric code
        $code = (string) mt_rand(1000, 9999);

        // Store hashed OTP valid for 5 minutes
        PhoneOtp::query()->create([
            'phone' => $normalizedPhone,
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
        ]);

        return [
            'success' => true,
            'message' => 'Kode OTP berhasil dibuat. Silakan masukkan kode untuk verifikasi.',
            'simulation_code' => $code,
        ];
    }

    /**
     * Verify the 4-digit OTP code for the given phone number.
     */
    public function verifyOtp(string $phone, string $code): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);

        $otpRecord = PhoneOtp::query()
            ->where('phone', $normalizedPhone)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otpRecord || $otpRecord->isExpired()) {
            return false;
        }

        if ($otpRecord->attempts >= 3) {
            $otpRecord->delete();

            return false;
        }

        if (! Hash::check($code, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');

            return false;
        }

        $otpRecord->update(['verified_at' => now()]);

        return true;
    }

    /**
     * Normalize Indonesian phone number formats (e.g. +62 812-3456 -> 08123456).
     */
    public function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($clean, '628')) {
            return '0'.substr($clean, 2);
        }

        return $clean;
    }
}
