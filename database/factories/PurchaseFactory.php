<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => fake()->randomElement([1, 2, 3, 4, 5]),
            'purchase_date' => now(),
            'purchase_no' => fake()->randomElement([1, 2, 3, 4, 5]),
            'purchase_status' => fake()->randomElement([0, 1]),
            'total_amount' => fake()->randomNumber(2),
            'quantity_alert' => fake()->randomElement([5,10,15]),
            'created_by' => fake()->randomElement([1,2,3]),
        ];
    }
}
