<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'order_id',
    'type',
    'amount',
    'balance_after',
    'description',
    'created_at',
])]
class PointTransaction extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'balance_after' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * User relation.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Associated order relation.
     *
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Human-friendly Indonesian label for transaction type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'earned' => 'Poin Belanja',
            'redeemed' => 'Penukaran Poin',
            'bonus' => 'Bonus Selamat Datang',
            'adjustment' => 'Penyesuaian Sistem',
            default => ucfirst($this->type),
        };
    }

    /**
     * Badge classes based on transaction direction.
     */
    public function getBadgeClassesAttribute(): string
    {
        return $this->amount > 0
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';
    }
}
