<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\UnitType;
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
        return [
            'name' => fake()->sentence(),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'unit_type_id' => UnitType::factory(),
            'description' => fake()->sentence(),
            'quantity' => fake()->numberBetween(10, 100),
            'price' => fake()->numberBetween(100, 10000),
        ];
    }
}
