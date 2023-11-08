<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            [
                'id'    => 1,
                'name'  => 'Laptops',
                'slug'  => 'laptops'
            ],
            [
                'id'    => 2,
                'name'  => 'Hardware',
                'slug'  => 'hardware'
            ],
            [
                'id'    => 3,
                'name'  => 'Smartphones',
                'slug'  => 'smartphones'
            ],
            [
                'id'    => 4,
                'name'  => 'Speakers',
                'slug'  => 'speakers'
            ],
            [
                'id'    => 5,
                'name'  => 'Software',
                'slug'  => 'software'
            ]
        ]);

        $categories->each(function ($category){
            Category::insert($category);
        });
    }
}
