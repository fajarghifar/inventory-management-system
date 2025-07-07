<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'account_number' => fake()->numberBetween(1000000000, 9999999999),
            'description' => fake()->sentence(),
            'balance' => fake()->numberBetween(1000, 9999),
        ];
    }
}
