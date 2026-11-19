<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Liter', 'symbol' => 'L'],
            ['name' => 'Bottle', 'symbol' => 'btl'],
            ['name' => 'Pack', 'symbol' => 'pk'],
            ['name' => 'Box / Carton', 'symbol' => 'box'],
            ['name' => 'Dozen', 'symbol' => 'doz'],
            ['name' => 'Quintal (100kg)', 'symbol' => 'qnt'], // Essential for Ethiopian grain & teff markets
            ['name' => 'Roll', 'symbol' => 'roll'],
            ['name' => 'Meter', 'symbol' => 'm'],
        ];

        foreach ($units as $unit) {
            $existing = Unit::where('symbol', $unit['symbol'])
                ->orWhere('name', $unit['name'])
                ->first();

            if (!$existing) {
                Unit::create([
                    'name' => $unit['name'],
                    'symbol' => $unit['symbol'],
                ]);
            }
        }
    }
}
