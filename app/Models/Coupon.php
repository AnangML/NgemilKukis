<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'description',
    'type',
    'value',
    'min_order_amount',
    'max_discount_amount',
    'usage_limit_total',
    'usage_limit_per_user',
    'used_count',
    'start_date',
    'end_date',
    'is_active',
    'is_stackable_with_member',
])]
class Coupon extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'min_order_amount' => 'integer',
            'max_discount_amount' => 'integer',
            'usage_limit_total' => 'integer',
            'usage_limit_per_user' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
            'is_stackable_with_member' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Usages relation.
     *
     * @return HasMany<CouponUsage, $this>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope for currently active and date-valid coupons.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Calculate discount nominal for a given subtotal.
     */
    public function calculateDiscount(int $subtotal): int
    {
        if ($this->type === 'percentage') {
            $discount = (int) round(($subtotal * $this->value) / 100);
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }

            return min($discount, $subtotal);
        }

        return min($this->value, $subtotal);
    }

    /**
     * Validate whether this coupon is eligible for an order.
     *
     * @return array{eligible: bool, message: string, discount: int}
     */
    public function checkEligibility(?User $user, int $subtotal, ?string $phone = null, ?string $email = null): array
    {
        if (! $this->is_active) {
            return ['eligible' => false, 'message' => 'Kupon promo ini sedang tidak aktif.', 'discount' => 0];
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return ['eligible' => false, 'message' => 'Kupon promo belum dapat digunakan.', 'discount' => 0];
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return ['eligible' => false, 'message' => 'Masa berlaku kupon promo telah berakhir.', 'discount' => 0];
        }

        if ($this->usage_limit_total && $this->used_count >= $this->usage_limit_total) {
            return ['eligible' => false, 'message' => 'Kuota pemakaian kupon ini telah habis.', 'discount' => 0];
        }

        if ($subtotal < $this->min_order_amount) {
            $minFormatted = 'Rp '.number_format($this->min_order_amount, 0, ',', '.');

            return [
                'eligible' => false,
                'message' => "Minimal belanja untuk kupon ini adalah {$minFormatted}.",
                'discount' => 0,
            ];
        }

        // Check user limit
        if ($this->usage_limit_per_user > 0) {
            $userUsageQuery = $this->usages();
            if ($user) {
                $userUsageQuery->where('user_id', $user->id);
            } elseif ($phone) {
                $userUsageQuery->where('customer_phone', $phone);
            } elseif ($email) {
                $userUsageQuery->where('customer_email', strtolower($email));
            } else {
                $userUsageQuery = null;
            }

            if ($userUsageQuery && $userUsageQuery->count() >= $this->usage_limit_per_user) {
                return [
                    'eligible' => false,
                    'message' => "Anda telah mencapai batas maksimal pemakaian kupon ini ({$this->usage_limit_per_user}x).",
                    'discount' => 0,
                ];
            }
        }

        $discount = $this->calculateDiscount($subtotal);

        return [
            'eligible' => true,
            'message' => 'Kupon promo berhasil digunakan!',
            'discount' => $discount,
        ];
    }

    /**
     * Human-friendly formatted value (e.g. '15%' or 'Rp 20.000').
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->type === 'percentage'
            ? "{$this->value}%"
            : 'Rp '.number_format($this->value, 0, ',', '.');
    }
}
