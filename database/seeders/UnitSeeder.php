<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = collect([
            [
                'name' => 'Meters',
                'slug' => 'meters',
                'short_code' => 'm',
                'user_id'=>1
            ],
            [
                'name' => 'Centimeters',
                'slug' => 'centimeters',
                'short_code' => 'cm',
                'user_id'=>1
            ],
            [
                'name' => 'Piece',
                'slug' => 'piece',
                'short_code' => 'pc',
                'user_id'=>1
            ]
        ]);

        $units->each(function ($unit){
            Unit::insert($unit);
        });
    }
}
