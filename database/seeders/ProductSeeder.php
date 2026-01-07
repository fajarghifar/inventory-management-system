<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Electronics' => [
                ['name' => 'Samsung Smart TV 55 Inch', 'price' => 8500000],
                ['name' => 'Sony Wireless Headphones', 'price' => 2100000],
                ['name' => 'LG Washing Machine', 'price' => 4500000],
            ],
            'Computers' => [
                ['name' => 'MacBook Air M2', 'price' => 18000000],
                ['name' => 'Logitech MX Master 3S', 'price' => 1500000],
                ['name' => 'Keychron K2 Mechanical Keyboard', 'price' => 1200000],
                ['name' => 'Dell XPS 13', 'price' => 22000000],
            ],
            'Smartphones' => [
                ['name' => 'iPhone 15 Pro Max', 'price' => 24000000],
                ['name' => 'Samsung Galaxy S24 Ultra', 'price' => 21000000],
                ['name' => 'Google Pixel 8', 'price' => 12000000],
            ],
            'Furniture' => [
                ['name' => 'Ergonomic Office Chair', 'price' => 3500000],
                ['name' => 'Wooden Standing Desk', 'price' => 4200000],
                ['name' => 'Bookshelf 5 Tier', 'price' => 850000],
            ],
            'Grocery' => [
                ['name' => 'Premium Olive Oil 1L', 'price' => 150000],
                ['name' => 'Dark Chocolate 85%', 'price' => 45000],
                ['name' => 'Arabica Coffee Beans 1kg', 'price' => 250000],
            ],
            'Clothing' => [
                ['name' => 'Cotton T-Shirt Black', 'price' => 99000],
                ['name' => 'Denim Jacket', 'price' => 450000],
                ['name' => 'Running Shoes', 'price' => 1200000],
                ['name' => 'Formal Shirt', 'price' => 250000],
                ['name' => 'Chino Pants', 'price' => 350000],
            ],
            'Accessories' => [
                ['name' => 'USB-C Cable 2m', 'price' => 150000],
                ['name' => 'Laptop Stand Aluminum', 'price' => 350000],
                ['name' => 'Wireless Power Bank 10000mAh', 'price' => 450000],
                ['name' => 'HDMI Cable 4K', 'price' => 120000],
            ],
            'Office Supplies' => [
                ['name' => 'A4 Paper Ream (500 sheets)', 'price' => 45000],
                ['name' => 'Ballpoint Pen Box (12pcs)', 'price' => 35000],
                ['name' => 'Stapler Heavy Duty', 'price' => 85000],
                ['name' => 'Whiteboard Marker Set', 'price' => 55000],
            ],
            'Household' => [
                ['name' => 'Philips Blender', 'price' => 650000],
                ['name' => 'Panasonic Electric Iron', 'price' => 350000],
                ['name' => 'Rice Cooker Digital', 'price' => 1200000],
                ['name' => 'Air Purifier', 'price' => 2500000],
            ],
            'Books' => [
                ['name' => 'Learning Laravel 11', 'price' => 450000],
                ['name' => 'Clean Code by Robert Martin', 'price' => 350000],
                ['name' => 'The Pragmatic Programmer', 'price' => 380000],
            ],
            'Sports & Outdoors' => [
                ['name' => 'Yoga Mat Premium', 'price' => 250000],
                ['name' => 'Dumbbell Set 10kg', 'price' => 850000],
                ['name' => 'Camping Tent 4 Person', 'price' => 1500000],
            ],
            'Tools & Hardware' => [
                ['name' => 'Cordless Drill 12V', 'price' => 850000],
                ['name' => 'Toolbox Set 100pcs', 'price' => 1200000],
                ['name' => 'Digital Multimeter', 'price' => 150000],
            ],
        ];

        $unit = Unit::first() ?? Unit::factory()->create();

        foreach ($data as $categoryName => $products) {
            $category = Category::where('name', 'LIKE', "%$categoryName%")->first();

            if ($category) {
                foreach ($products as $item) {
                    Product::create([
                        'category_id' => $category->id,
                        'unit_id' => $unit->id,
                        'sku' => strtoupper(\Illuminate\Support\Str::random(3) . '-' . rand(1000, 9999) . '-' . \Illuminate\Support\Str::random(4)),
                        'name' => $item['name'],
                        'description' => 'High quality ' . $item['name'] . ' with official warranty.',
                        'purchase_price' => $item['price'] * 0.7,
                        'selling_price' => $item['price'],
                        'quantity' => rand(10, 50),
                        'min_stock' => rand(2, 5),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
