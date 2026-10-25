<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'KUKISMANIS',
                'name' => 'Diskon Peluncuran Manis Rp 20.000',
                'description' => 'Potongan langsung Rp 20.000 untuk minimal belanja Rp 50.000. Berlaku untuk semua produk!',
                'type' => 'fixed',
                'value' => 20000,
                'min_order_amount' => 50000,
                'max_discount_amount' => null,
                'usage_limit_total' => 500,
                'usage_limit_per_user' => 3,
                'used_count' => 0,
                'is_active' => true,
                'is_stackable_with_member' => true,
            ],
            [
                'code' => 'PAYDAY15',
                'name' => 'Spesial Payday Diskon 15%',
                'description' => 'Nikmati diskon 15% hingga maksimal Rp 45.000 untuk pembelian minimal Rp 100.000.',
                'type' => 'percentage',
                'value' => 15,
                'min_order_amount' => 100000,
                'max_discount_amount' => 45000,
                'usage_limit_total' => 200,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'is_active' => true,
                'is_stackable_with_member' => true,
            ],
            [
                'code' => 'SUPERKUKIS',
                'name' => 'Mega Voucher Rp 50.000 (Standalone)',
                'description' => 'Potongan super Rp 50.000 khusus pembelian minimal Rp 150.000 (tidak dapat digabung dengan diskon member).',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_amount' => 150000,
                'max_discount_amount' => null,
                'usage_limit_total' => 50,
                'usage_limit_per_user' => 1,
                'used_count' => 0,
                'is_active' => true,
                'is_stackable_with_member' => false,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }
    }
}
