<?php

namespace Database\Seeders;

use App\Models\AttributeProduct;
use App\Models\Product;
use Database\Factories\AttributeProductFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(100)
            ->has(AttributeProduct::factory()->count(3), 'attributeProducts')
            ->create();
    }
}
