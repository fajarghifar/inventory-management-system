<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $categories = Category::pluck('id')->toArray();
        $units = Unit::pluck('id')->toArray();

        return [
            'category_id' => !empty($categories) ? fake()->randomElement($categories) : Category::factory(),
            'unit_id' => !empty($units) ? fake()->randomElement($units) : Unit::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('???-####-????')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'purchase_price' => fake()->numberBetween(10000, 500000),
            'selling_price' => fake()->numberBetween(550000, 1000000),
            'quantity' => fake()->numberBetween(0, 100),
            'min_stock' => fake()->numberBetween(5, 20),
            'is_active' => fake()->boolean(90),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
