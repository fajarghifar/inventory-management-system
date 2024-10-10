<?php

namespace Database\Factories;

use App\Enums\PurchaseStatus;
use App\Models\Supplier;
use App\Models\User;
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
            'supplier_id' => Supplier::factory(),
            'date' => fake()->dateTimeBetween(),
            'purchase_no' => fake()->randomElement([1, 2, 3, 4, 5]),
            'status' => fake()->randomElement(PurchaseStatus::cases()),
            'total_amount' => fake()->randomNumber(2),
            'created_by' => User::factory()
        ];
    }
}
