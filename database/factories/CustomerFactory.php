<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'address' => fake()->address(),
            'account_holder' => fake()->name(),
            'account_number' => fake()->randomNumber(8, true),
            'bank_name' => fake()->randomElement(['BRI', 'BNI', 'BCA', 'BSI', 'Mandiri']),
        ];
    }
}
