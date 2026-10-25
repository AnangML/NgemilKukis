<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'user_id',
    'user_name',
    'user_role',
    'action',
    'category',
    'description',
    'subject_type',
    'subject_id',
    'properties',
    'ip_address',
    'user_agent',
    'created_at',
])]
class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Activity logs are immutable and do not have an updated_at column.
     */
    public const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * The user who performed this activity.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The polymorphic subject related to this activity (Order, Product, etc.).
     *
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to apply search and filters.
     *
     * @param  Builder<static>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<static>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, function (Builder $query, string $role) {
                if ($role !== 'all') {
                    $query->where('user_role', $role);
                }
            })
            ->when($filters['category'] ?? null, function (Builder $query, string $category) {
                if ($category !== 'all') {
                    $query->where('category', $category);
                }
            })
            ->when($filters['date'] ?? null, function (Builder $query, string $date) {
                if ($date === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($date === 'yesterday') {
                    $query->whereDate('created_at', today()->subDay());
                } elseif ($date === 'week') {
                    $query->where('created_at', '>=', now()->subDays(7));
                } elseif ($date === 'month') {
                    $query->where('created_at', '>=', now()->subDays(30));
                }
            });
    }

    /**
     * Get Indonesian readable label for category.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'auth' => 'Autentikasi',
            'order' => 'Pesanan',
            'catalog' => 'Katalog & Stok',
            'settings' => 'Pengaturan Toko',
            'staff' => 'Manajemen Staf',
            default => ucfirst($this->category),
        };
    }

    /**
     * Get badge color styling for category.
     */
    public function getCategoryBadgeClassesAttribute(): string
    {
        return match ($this->category) {
            'auth' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'order' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'catalog' => 'bg-amber-50 text-amber-700 border-amber-200',
            'settings' => 'bg-purple-50 text-purple-700 border-purple-200',
            'staff' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-stone-50 text-stone-700 border-stone-200',
        };
    }

    /**
     * Get badge color styling for user role snapshot.
     */
    public function getRoleBadgeClassesAttribute(): string
    {
        return match ($this->user_role) {
            'admin' => 'bg-amber-100 text-amber-900 border-amber-300',
            'seller' => 'bg-blue-100 text-blue-900 border-blue-300',
            'customer_service' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
            'customer' => 'bg-stone-100 text-stone-700 border-stone-300',
            default => 'bg-stone-100 text-stone-500 border-stone-200',
        };
    }
}
