<?php

namespace Database\Factories;

use App\Models\GreetingCardTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GreetingCardTemplate>
 */
class GreetingCardTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true).' Card';

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'theme_color' => fake()->randomElement(['amber', 'indigo', 'rose', 'emerald', 'pink']),
            'icon' => fake()->randomElement(['🎂', '🎓', '💐', '✨', '💖']),
            'design_style' => 'classic',
            'default_message' => fake()->sentence(12),
            'price' => 0,
            'is_active' => true,
        ];
    }
}
