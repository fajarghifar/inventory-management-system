<?php

namespace Database\Factories;

use App\Models\Adjustment;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdjustmentProduct>
 */
class AdjustmentProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'adjustment_id' => Adjustment::factory(),
            'product_id' => Product::inRandomOrder()->value('id'),
            'quantity' => fake()->numberBetween(1, 20),
            'type' => fake()->randomElement(['addition', 'subtraction']),
        ];
    }
}
