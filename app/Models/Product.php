<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'price',
        'sale_price',
        'type',
        'baking_schedule',
        'stock',
        'image',
        'badge',
        'ingredients',
        'weight_grams',
        'rating',
        'review_count',
        'is_active',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'sale_price' => 'integer',
            'stock' => 'integer',
            'weight_grams' => 'integer',
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Scope for active products.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope for ready stock.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeReadyStock(Builder $query): void
    {
        $query->where('type', 'ready_stock');
    }

    /**
     * Scope for pre-order.
     *
     * @param  Builder<Product>  $query
     */
    public function scopePreOrder(Builder $query): void
    {
        $query->where('type', 'pre_order');
    }

    /**
     * Formatted price in IDR.
     */
    public function formattedPrice(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    /**
     * Formatted sale price in IDR if any.
     */
    public function formattedSalePrice(): ?string
    {
        return $this->sale_price ? 'Rp '.number_format($this->sale_price, 0, ',', '.') : null;
    }

    /**
     * Current effective price.
     */
    public function effectivePrice(): int
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Order items relation.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
