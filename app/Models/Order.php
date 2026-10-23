<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'idempotency_key',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_city',
        'shipping_method',
        'shipping_cost',
        'subtotal',
        'total_amount',
        'status',
        'payment_method',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'is_gift',
        'gift_recipient_name',
        'gift_sender_name',
        'gift_card_template_id',
        'gift_message',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'shipping_cost' => 'integer',
            'subtotal' => 'integer',
            'total_amount' => 'integer',
            'is_gift' => 'boolean',
        ];
    }

    /**
     * Items relation.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Greeting card template relation.
     *
     * @return BelongsTo<GreetingCardTemplate, $this>
     */
    public function greetingCardTemplate(): BelongsTo
    {
        return $this->belongsTo(GreetingCardTemplate::class, 'gift_card_template_id');
    }

    /**
     * Formatted total amount in IDR.
     */
    public function formattedTotal(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Status label in Indonesian.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'baking' => 'Sedang Dipanggang',
            'shipped' => 'Dalam Pengiriman',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
