<?php

namespace Database\Factories;

use App\Models\AttributeValue;
use App\Models\ProductTransaction;
use App\Models\ProductTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttributeValueProductTransaction>
 */
class AttributeValueProductTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_transaction_id' => ProductTransaction::factory(),
            'attribute_value_id' => AttributeValue::factory(),
        ];
    }
}
