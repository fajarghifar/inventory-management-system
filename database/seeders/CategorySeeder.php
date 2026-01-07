<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Computers',
            'Smartphones',
            'Accessories',
            'Furniture',
            'Office Supplies',
            'Household',
            'Health & Beauty',
            'Toys & Games',
            'Automotive',
            'Books',
            'Clothing',
            'Groceries',
            'Sports & Outdoors',
            'Tools & Hardware'
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name' => $categoryName,
                    'description' => 'Various items related to ' . strtolower($categoryName),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
