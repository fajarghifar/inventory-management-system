<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs', 'symbol' => 'pcs'],       // Satuan umum (Alat, Sambungan Pipa, Kuas)
            ['name' => 'Kilogram', 'symbol' => 'kg'],  // Paku, Kawat, Cat Kiloan
            ['name' => 'Meter', 'symbol' => 'm'],      // Kabel, Talang, Plastik Cor
            ['name' => 'Batang', 'symbol' => 'btg'],  // Pipa, Besi Beton, Baja Ringan, Lis
            ['name' => 'Lembar', 'symbol' => 'lbr'],   // Triplek, Seng, Asbes, GRC
            ['name' => 'Sak', 'symbol' => 'sak'],      // Khusus Semen
            ['name' => 'Dus', 'symbol' => 'dus'],      // Keramik, Granit, Paku (Grosir)
            ['name' => 'Roll', 'symbol' => 'roll'],    // Kabel Besar, Selang, Terpal Gulung
            ['name' => 'Rit', 'symbol' => 'rit'],      // Pasir, Batu (Muatan Truk)
            ['name' => 'Liter', 'symbol' => 'ltr'],    // Thinner, Cat Minyak
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['symbol' => $unit['symbol']],
                ['name' => $unit['name']]
            );
        }
    }
}
