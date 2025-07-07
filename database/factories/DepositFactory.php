<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\DepositCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deposit>
 */
class DepositFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::inRandomOrder()->value('id'),
            'deposit_date' => fake()->date(),
            'amount' => fake()->numberBetween(1000, 10000),
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'deposit_category_id' => DepositCategory::inRandomOrder()->value('id'),
        ];
    }
}
