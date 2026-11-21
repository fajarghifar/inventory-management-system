<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Grains, Teff & Flour',
                'description' => 'White and sergegna teff, wheat flour, barley, corn, and lentils (እህል እና ዱቄት)'
            ],
            [
                'name' => 'Edible Oil & Essentials',
                'description' => 'Sunflower oil, palm oil, salt, Ethiopian spices, and berbere (የምግብ ዘይት እና ማጣፈጫ)'
            ],
            [
                'name' => 'Beverages & Soft Drinks',
                'description' => 'Ambo mineral water, soft drinks, packed juices, and bottled water (መጠጦች)'
            ],
            [
                'name' => 'Ethiopian Coffee & Tea',
                'description' => 'Roasted Harar/Yirgacheffe coffee beans, ground coffee, and highland tea (ቡና እና ሻይ)'
            ],
            [
                'name' => 'Dairy & Bakery',
                'description' => 'Fresh milk, butter, cheese, bread, and pastries (የወተት ተዋጽኦ እና ዳቦ)'
            ],
            [
                'name' => 'Personal Care & Hygiene',
                'description' => 'Bath soaps, laundry bars, toothpaste, shampoos, and sanitizers (የግል ንፅህና)'
            ],
            [
                'name' => 'Cleaning & Household Goods',
                'description' => 'Detergents, floor cleaners, kitchenware, thermos, and storage (የቤት እቃዎች)'
            ],
            [
                'name' => 'Snacks & Confectionery',
                'description' => 'Biscuits, chocolates, candies, and packaged nuts (ብስኩት እና ከረሜላ)'
            ],
            [
                'name' => 'Stationery & General Goods',
                'description' => 'Notebooks, pens, batteries, and store counter supplies (የጽህፈት መሳሪያዎች)'
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
