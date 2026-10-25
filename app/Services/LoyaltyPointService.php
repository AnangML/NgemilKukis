<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyPointService
{
    /**
     * Spend amount required to earn 1 Cookie Crumb point (in IDR).
     */
    public const EARN_RATE_PER_RUPIAH = 1000;

    /**
     * Redemption value of 1 Cookie Crumb point (in IDR).
     */
    public const POINT_VALUE_IN_IDR = 50;

    /**
     * Maximum percentage of subtotal that can be discounted using points.
     */
    public const MAX_REDEMPTION_PERCENTAGE = 50;

    /**
     * Award loyalty points to user when an order is paid.
     */
    public function earnPointsForOrder(Order $order): ?PointTransaction
    {
        if (! $order->user_id) {
            return null;
        }

        // Check if points were already earned for this order
        $alreadyEarned = PointTransaction::query()
            ->where('order_id', $order->id)
            ->where('type', 'earned')
            ->exists();

        if ($alreadyEarned) {
            return null;
        }

        // Calculate points based on net paid total amount
        $points = (int) floor($order->total_amount / self::EARN_RATE_PER_RUPIAH);
        if ($points <= 0) {
            return null;
        }

        return DB::transaction(function () use ($order, $points) {
            $user = User::query()->lockForUpdate()->find($order->user_id);
            if (! $user) {
                return null;
            }

            $user->points_balance += $points;
            $user->save();

            $order->update(['points_earned' => $points]);

            $trx = PointTransaction::query()->create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'earned',
                'amount' => $points,
                'balance_after' => $user->points_balance,
                'description' => "Poin perolehan belanja pesanan #{$order->order_number}",
                'created_at' => now(),
            ]);

            ActivityLogger::order(
                'points.earned',
                "Member {$user->name} mendapatkan +{$points} Cookie Crumbs dari pesanan #{$order->order_number}",
                $order,
                ['points' => $points, 'balance_after' => $user->points_balance]
            );

            return $trx;
        });
    }

    /**
     * Redeem user's points during checkout to discount the order.
     */
    public function redeemPointsForOrder(User $user, int $pointsToRedeem, Order $order): ?PointTransaction
    {
        if ($pointsToRedeem <= 0) {
            return null;
        }

        return DB::transaction(function () use ($user, $pointsToRedeem, $order) {
            $lockedUser = User::query()->lockForUpdate()->find($user->id);
            if (! $lockedUser || $lockedUser->points_balance < $pointsToRedeem) {
                return null;
            }

            $lockedUser->points_balance -= $pointsToRedeem;
            $lockedUser->save();

            $discountRupiah = $pointsToRedeem * self::POINT_VALUE_IN_IDR;

            $trx = PointTransaction::query()->create([
                'user_id' => $lockedUser->id,
                'order_id' => $order->id,
                'type' => 'redeemed',
                'amount' => -$pointsToRedeem,
                'balance_after' => $lockedUser->points_balance,
                'description' => "Penukaran {$pointsToRedeem} Cookie Crumbs (Hemat Rp ".number_format($discountRupiah, 0, ',', '.').") pada pesanan #{$order->order_number}",
                'created_at' => now(),
            ]);

            ActivityLogger::order(
                'points.redeemed',
                "Member {$lockedUser->name} menukarkan {$pointsToRedeem} Cookie Crumbs pada pesanan #{$order->order_number}",
                $order,
                ['points' => $pointsToRedeem, 'discount_idr' => $discountRupiah]
            );

            return $trx;
        });
    }

    /**
     * Award welcome bonus points to newly registered member.
     */
    public function awardWelcomeBonus(User $user, int $bonusPoints = 50): PointTransaction
    {
        return DB::transaction(function () use ($user, $bonusPoints) {
            $lockedUser = User::query()->lockForUpdate()->find($user->id);
            $lockedUser->points_balance += $bonusPoints;
            $lockedUser->save();

            $trx = PointTransaction::query()->create([
                'user_id' => $lockedUser->id,
                'order_id' => null,
                'type' => 'bonus',
                'amount' => $bonusPoints,
                'balance_after' => $lockedUser->points_balance,
                'description' => 'Bonus selamat datang pendaftaran Kukis VIP Club (50 Cookie Crumbs)',
                'created_at' => now(),
            ]);

            ActivityLogger::auth(
                'points.welcome_bonus',
                "Member {$lockedUser->name} menerima bonus selamat datang +{$bonusPoints} Cookie Crumbs",
                $lockedUser
            );

            return $trx;
        });
    }

    /**
     * Calculate maximum points a user is allowed to redeem for a given subtotal.
     */
    public function calculateMaxRedeemablePoints(int $subtotal, int $userBalance): int
    {
        $maxDiscountAmount = (int) round(($subtotal * self::MAX_REDEMPTION_PERCENTAGE) / 100);
        $maxPointsFromSubtotal = (int) floor($maxDiscountAmount / self::POINT_VALUE_IN_IDR);

        return min($userBalance, $maxPointsFromSubtotal);
    }
}
