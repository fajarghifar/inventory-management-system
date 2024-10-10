<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentType;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
//            'order_date' => fake()->dateTimeBetween('2022-01-01 00:00:00', now()->addDays(15)),
            'order_date' => fake()->dateTime(),
            'order_status' => fake()->randomElement(OrderStatus::cases()),
            'total_products' => fake()->randomElement([1,5,10,15]),
            'sub_total' => fake()->randomNumber(5, true),
            'vat' => fake()->randomElement([6, 15, 24]),
            'total' => fake()->randomNumber(5, true),
            'invoice_no' => fake()->numerify('INV-##########'),
            'payment_type' => fake()->randomElement(PaymentType::cases()),
            'pay' => fake()->randomNumber(5, true),
            'due' => fake()->randomNumber(5, true),
        ];
    }
}
