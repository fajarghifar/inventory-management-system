<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Milligram', 'symbol' => 'mg'],
            ['name' => 'Meter', 'symbol' => 'm'],
            ['name' => 'Centimeter', 'symbol' => 'cm'],
            ['name' => 'Millimeter', 'symbol' => 'mm'],
            ['name' => 'Liter', 'symbol' => 'l'],
            ['name' => 'Milliliter', 'symbol' => 'ml'],
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Pack', 'symbol' => 'pck'],
            ['name' => 'Dozen', 'symbol' => 'dz'],
            ['name' => 'Pair', 'symbol' => 'pr'],
            ['name' => 'Set', 'symbol' => 'set'],
            ['name' => 'Roll', 'symbol' => 'roll'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['symbol' => $unit['symbol']],
                ['name' => $unit['name']]
            );
        }
    }
}
