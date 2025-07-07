<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductTransaction>
 */
class ProductTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'transaction_id' => Transaction::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price' => fake()->numberBetween(1000, 10000),
        ];
    }
}
