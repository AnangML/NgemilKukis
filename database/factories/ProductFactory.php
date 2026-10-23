<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true).' Cookie';

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'tagline' => fake()->sentence(8),
            'description' => fake()->paragraph(),
            'price' => fake()->randomElement([25000, 28000, 32000, 35000]),
            'sale_price' => null,
            'type' => fake()->randomElement(['ready_stock', 'pre_order']),
            'baking_schedule' => 'Freshly baked daily',
            'stock' => fake()->numberBetween(10, 50),
            'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=800&q=80',
            'badge' => fake()->optional()->randomElement(['Best Seller', 'Favorit!', 'Limited PO']),
            'ingredients' => 'Belgian Chocolate, Butter, Brown Sugar, Sea Salt',
            'weight_grams' => 110,
            'rating' => fake()->randomFloat(2, 4.7, 5.0),
            'review_count' => fake()->numberBetween(10, 200),
            'is_active' => true,
            'is_featured' => fake()->boolean(40),
        ];
    }
}
