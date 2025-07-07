<?php

namespace Database\Seeders;

use App\Models\AttributeValueProductTransaction;
use App\Models\ProductTransaction;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::factory()
            ->count(10)
            ->has(
                ProductTransaction::factory()
                    ->count(3)
                    ->has(
                        AttributeValueProductTransaction::factory()->count(2),
                        'attributeValueProductTransactions'
                    ),
                'productTransactions'
            )->create();
    }
}
