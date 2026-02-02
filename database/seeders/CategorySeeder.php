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
                'name' => 'Material Dasar',
                'description' => 'Bahan bangunan utama seperti pasir, semen, batu, bata, hebel, dan besi beton.'
            ],
            [
                'name' => 'Kayu & Atap',
                'description' => 'Material kayu, triplek, kaso, serta penutup atap seperti genteng, asbes, seng, dan terpal.'
            ],
            [
                'name' => 'Cat & Finishing',
                'description' => 'Segala jenis cat (tembok/kayu/besi), thinner, pelapis anti bocor (no drop), dan lem.'
            ],
            [
                'name' => 'Lantai & Dinding',
                'description' => 'Penutup lantai dan dinding termasuk keramik, granit, plint, dan lis profil (kuku macan).'
            ],
            [
                'name' => 'Pipa & Listrik',
                'description' => 'Instalasi air (pipa PVC, kran, toren) dan instalasi listrik (kabel, lampu, saklar).'
            ],
            [
                'name' => 'Paku & Alat',
                'description' => 'Barang kecil/receh seperti paku, baut, sekrup, engsel, gembok, dan peralatan tukang.'
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
