<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()->get();
        if ($products->isEmpty()) {
            return;
        }

        $p1 = $products->first();
        $p2 = $products->skip(1)->first() ?? $p1;
        $p3 = $products->skip(2)->first() ?? $p1;

        // 1. Order Di Jalan (shipped)
        $shippedOrder = Order::updateOrCreate(
            ['order_number' => 'NK-20261023-TH001'],
            [
                'customer_name' => 'Nicha Thanakorn',
                'customer_phone' => '081298765432',
                'customer_email' => 'nicha.th@example.com',
                'customer_address' => 'Hotel Pullman Jakarta, Room 1204, Jl. M.H. Thamrin No.59',
                'customer_city' => 'Jakarta Pusat',
                'shipping_method' => 'instant_grab',
                'shipping_cost' => 25000,
                'subtotal' => 56000,
                'total_amount' => 81000,
                'status' => 'shipped',
                'payment_method' => 'xendit_card',
                'is_gift' => true,
                'gift_recipient_name' => 'Nicha Thanakorn (From Bangkok)',
                'gift_sender_name' => 'Anang',
                'gift_message' => 'Warmest artisan cookies for you all the way from Jakarta!',
                'notes' => 'Tolong antar ke resepsionis lobi hotel jika sedang tidak di kamar',
            ]
        );

        OrderItem::where('order_id', $shippedOrder->id)->delete();
        OrderItem::create([
            'order_id' => $shippedOrder->id,
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'price' => $p1->effectivePrice(),
            'quantity' => 1,
            'subtotal' => $p1->effectivePrice(),
        ]);
        OrderItem::create([
            'order_id' => $shippedOrder->id,
            'product_id' => $p2->id,
            'product_name' => $p2->name,
            'price' => $p2->effectivePrice(),
            'quantity' => 1,
            'subtotal' => $p2->effectivePrice(),
        ]);

        // 2. Order Sudah Sampai (delivered)
        $deliveredOrder = Order::updateOrCreate(
            ['order_number' => 'NK-20261023-ID002'],
            [
                'customer_name' => 'Raditya Pratama',
                'customer_phone' => '081234567890',
                'customer_email' => 'raditya@example.com',
                'customer_address' => 'Jl. Senopati No. 45, Kebayoran Baru',
                'customer_city' => 'Jakarta Selatan',
                'shipping_method' => 'sameday_gojek',
                'shipping_cost' => 18000,
                'subtotal' => 60000,
                'total_amount' => 78000,
                'status' => 'delivered',
                'payment_method' => 'xendit_qris',
                'is_gift' => false,
            ]
        );

        OrderItem::where('order_id', $deliveredOrder->id)->delete();
        OrderItem::create([
            'order_id' => $deliveredOrder->id,
            'product_id' => $p3->id,
            'product_name' => $p3->name,
            'price' => $p3->effectivePrice(),
            'quantity' => 2,
            'subtotal' => $p3->effectivePrice() * 2,
        ]);

        // 3. Order Dibatalkan (cancelled)
        $cancelledOrder = Order::updateOrCreate(
            ['order_number' => 'NK-20261023-ID003'],
            [
                'customer_name' => 'Maya Salsabila',
                'customer_phone' => '081388889999',
                'customer_email' => 'maya@example.com',
                'customer_address' => 'Apartemen Sudirman Tower A Lantai 15',
                'customer_city' => 'Jakarta Pusat',
                'shipping_method' => 'regular_jne',
                'shipping_cost' => 12000,
                'subtotal' => 28000,
                'total_amount' => 40000,
                'status' => 'cancelled',
                'payment_method' => 'xendit_va',
                'is_gift' => false,
                'notes' => 'Pembayaran kedaluwarsa oleh sistem Xendit',
            ]
        );

        OrderItem::where('order_id', $cancelledOrder->id)->delete();
        OrderItem::create([
            'order_id' => $cancelledOrder->id,
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'price' => $p1->effectivePrice(),
            'quantity' => 1,
            'subtotal' => $p1->effectivePrice(),
        ]);
    }
}
