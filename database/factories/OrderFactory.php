<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomElement([60000, 95000, 125000, 185000]);
        $shippingCost = fake()->randomElement([15000, 20000, 25000]);

        return [
            'order_number' => 'NK-'.date('Ymd').'-'.strtoupper(fake()->bothify('??##')),
            'customer_name' => fake()->name(),
            'customer_phone' => '08'.fake()->numerify('##########'),
            'customer_email' => fake()->safeEmail(),
            'customer_address' => fake()->address(),
            'customer_city' => fake()->city(),
            'shipping_method' => fake()->randomElement(['instant_grab', 'sameday_gojek', 'paxel_food']),
            'shipping_cost' => $shippingCost,
            'subtotal' => $subtotal,
            'total_amount' => $subtotal + $shippingCost,
            'status' => fake()->randomElement(['pending', 'paid', 'baking', 'shipped', 'delivered']),
            'payment_method' => fake()->randomElement(['xendit_qris', 'xendit_va', 'whatsapp_direct']),
            'xendit_invoice_id' => 'inv_'.fake()->uuid(),
            'xendit_invoice_url' => 'https://checkout.xendit.co/web/'.fake()->uuid(),
            'is_gift' => false,
            'gift_recipient_name' => null,
            'gift_sender_name' => null,
            'gift_card_template_id' => null,
            'gift_message' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
