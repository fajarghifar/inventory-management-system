<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expense_category_id' => ExpenseCategory::inRandomOrder()->value('id'),
            'account_id' => Account::inRandomOrder()->value('id'),
            'expense_date' => fake()->date(),
            'amount' => fake()->numberBetween(1000, 100000),
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'description' => fake()->sentence(),
        ];
    }
}
