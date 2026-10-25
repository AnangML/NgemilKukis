<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate coupon code for checkout.
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        $code = strtoupper(trim((string) $request->input('code', '')));
        $subtotal = (int) $request->input('subtotal', 0);
        $phone = $request->input('phone');
        $email = $request->input('email');

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Masukkan kode kupon terlebih dahulu.',
            ], 422);
        }

        $coupon = Coupon::query()->where('code', $code)->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => "Kode promo '{$code}' tidak ditemukan atau salah ketik.",
            ], 404);
        }

        $user = auth()->user();
        $eligibility = $coupon->checkEligibility($user, $subtotal, $phone, $email);

        if (! $eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $eligibility['message'],
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'formatted_value' => $coupon->formatted_value,
                'discount_amount' => $eligibility['discount'],
                'is_stackable_with_member' => $coupon->is_stackable_with_member,
            ],
        ]);
    }
}
