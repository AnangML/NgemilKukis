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
            'shipped' => 'Di Jalan (Pengiriman)',
            'delivered' => 'Sudah Sampai (Selesai)',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Detailed status description for customers.
     */
    public function statusDescription(): string
    {
        return match ($this->status) {
            'pending' => 'Selesaikan pembayaran invoice Xendit agar pesanan dapat segera diproses.',
            'paid' => 'Pembayaran terkonfirmasi! Pesanan siap dimasukkan ke jadwal antrean dapur.',
            'baking' => 'Kukis premium kamu sedang dipanggang fresh di oven dan dikemas rapi.',
            'shipped' => 'Pesanan sedang dalam perjalanan bersama kurir menuju alamat pengiriman.',
            'delivered' => 'Kukis telah sukses sampai di tujuan. Selamat menikmati selagi hangat!',
            'cancelled' => 'Pesanan ini telah dibatalkan.',
            default => 'Pesanan sedang dalam proses penanganan.',
        };
    }

    /**
     * Tailwind CSS badge classes based on status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'shipped' => 'bg-amber-50 text-amber-800 border-amber-300 ring-1 ring-amber-300/50',
            'delivered' => 'bg-emerald-50 text-emerald-800 border-emerald-300 ring-1 ring-emerald-300/50',
            'cancelled' => 'bg-rose-50 text-rose-800 border-rose-300 ring-1 ring-rose-300/50',
            'baking' => 'bg-orange-50 text-orange-800 border-orange-300',
            'paid' => 'bg-blue-50 text-blue-800 border-blue-300',
            'pending' => 'bg-stone-50 text-stone-700 border-stone-300',
            default => 'bg-stone-50 text-stone-700 border-stone-200',
        };
    }

    /**
     * Emoji/Icon representation for status.
     */
    public function statusIcon(): string
    {
        return match ($this->status) {
            'shipped' => '🛵',
            'delivered' => '✅',
            'cancelled' => '❌',
            'baking' => '🍪',
            'paid' => '💳',
            'pending' => '⏳',
            default => '📦',
        };
    }

    /**
     * Stepper index: 1 (Pending), 2 (Paid), 3 (Baking), 4 (Shipped), 5 (Delivered), -1 (Cancelled).
     */
    public function statusStepIndex(): int
    {
        return match ($this->status) {
            'cancelled' => -1,
            'pending' => 1,
            'paid' => 2,
            'baking' => 3,
            'shipped' => 4,
            'delivered' => 5,
            default => 1,
        };
    }

    /**
     * Human-friendly shipping method label.
     */
    public function shippingMethodLabel(): string
    {
        return match ($this->shipping_method) {
            'instant_grab' => 'Grab / Gojek Instant',
            'sameday_gojek' => 'Gojek Sameday Delivery',
            'paxel_food' => 'Paxel Chilled Delivery',
            'regular_jne' => 'JNE / SiCepat Reguler',
            default => ucwords(str_replace('_', ' ', $this->shipping_method ?? 'Kurir Pengiriman')),
        };
    }

    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
