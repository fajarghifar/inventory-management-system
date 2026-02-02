<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Cache ID untuk performa (Slug Kategori & Simbol Unit)
        $cats = Category::pluck('id', 'slug')->toArray();
        $units = Unit::pluck('id', 'symbol')->toArray();

        // Helper closure untuk ambil ID
        $getCat = fn($name) => $cats[Str::slug($name)] ?? array_first($cats);
        $getUnit = fn($sym) => $units[$sym] ?? array_first($units);

        $products = [
            // Semen, Mortar & Bubuk (Material Dasar)
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Tiga Roda (50kg)', 'p' => 70000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Dynamix (50kg)', 'p' => 65000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Rajawali (50kg)', 'p' => 59000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Merdeka (50kg)', 'p' => 53500],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Best (50kg)', 'p' => 52000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Acian TR-30 Tiga Roda (40kg)', 'p' => 110000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Putih Tiga Roda (40kg)', 'p' => 110000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Semen Hebel SCG (40kg)', 'p' => 65000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Acian Plester Putih Maxson MC-270', 'p' => 62500],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Casting / Tepung Gipsum (20kg)', 'p' => 37000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Kapur Mill (20kg)', 'p' => 18000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'Kompon A+ (20kg)', 'p' => 60000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'TR-15 Tiga Roda Perekat Hebel (40kg)', 'p' => 80000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'TR-20 Tiga Roda Plester Hebel (40kg)', 'p' => 80000],
            ['cat' => 'Material Dasar', 'u' => 'sak', 'n' => 'MU-301 Tiga Roda Plester Hebel (40kg)', 'p' => 97000],

            // Pasir, Batu & Urugan (Material Dasar)
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Pasir Jumbo (1 Truk)', 'p' => 1100000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Pasir Cor (1 Truk)', 'p' => 1350000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Pasir Cuci (1 Truk)', 'p' => 1500000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Batu Pondasi Hitam (1 Truk)', 'p' => 1200000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Batu Pondasi Putih (1 Truk)', 'p' => 900000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Batu Split - Rata Bak', 'p' => 1800000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Batu Split - Full Bak', 'p' => 2000000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Sirtu Urug (1 Truk)', 'p' => 700000],
            ['cat' => 'Material Dasar', 'u' => 'rit', 'n' => 'Tanah Urug (1 Truk)', 'p' => 550000],

            // Bata Merah (Material Dasar)
            ['cat' => 'Material Dasar', 'u' => 'pcs', 'n' => 'Bata (K) - Kecil', 'p' => 750],
            ['cat' => 'Material Dasar', 'u' => 'pcs', 'n' => 'Bata (S) - Sedang', 'p' => 950],
            ['cat' => 'Material Dasar', 'u' => 'pcs', 'n' => 'Bata (B) - Besar', 'p' => 1000],

            // Triplek / Plywood (Kayu & Atap)
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 3mm Tunas', 'p' => 46000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 4mm Tunas', 'p' => 53500],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 6mm Tunas', 'p' => 67500],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 6mm MC', 'p' => 95000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 8mm MC', 'p' => 82000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 12mm MC', 'p' => 135000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 18mm MC', 'p' => 210000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 9mm UT Better', 'p' => 131000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 12mm UT Better', 'p' => 160000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 15mm UT Better', 'p' => 187000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 18mm UT Better', 'p' => 230000],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 3mm Alba', 'p' => 42500],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 4mm Alba', 'p' => 52500],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek Melaminto Putih 3mm', 'p' => 127500],
            ['cat' => 'Kayu & Atap', 'u' => 'lbr', 'n' => 'Triplek 15mm X Brasi', 'p' => 205000],

            // Terpal (Kayu & Atap)
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Terpal 2x3', 'p' => 40000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Terpal 3x3', 'p' => 60000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Terpal 4x4', 'p' => 94000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Terpal 4x6', 'p' => 137500],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Terpal 5x7', 'p' => 195000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Terpal 5x6', 'p' => 210000],

            // Kuku Macan / Lis Keramik (Lantai & Dinding)
            ['cat' => 'Lantai & Dinding', 'u' => 'dus', 'n' => 'Kuku Macan Fujimi (Dus)', 'p' => 125000],
            ['cat' => 'Lantai & Dinding', 'u' => 'm', 'n' => 'Kuku Macan Fujimi (Meter)', 'p' => 7500],
            ['cat' => 'Lantai & Dinding', 'u' => 'dus', 'n' => 'Kuku Macan Viva (Dus)', 'p' => 75000],
            ['cat' => 'Lantai & Dinding', 'u' => 'm', 'n' => 'Kuku Macan Viva (Meter)', 'p' => 5000],
            ['cat' => 'Lantai & Dinding', 'u' => 'dus', 'n' => 'Kuku Macan Marbel KW (Dus)', 'p' => 165000],
            ['cat' => 'Lantai & Dinding', 'u' => 'm', 'n' => 'Kuku Macan Marbel KW (Meter)', 'p' => 10000],
            ['cat' => 'Lantai & Dinding', 'u' => 'dus', 'n' => 'Kuku Macan Silver/Gold (Dus)', 'p' => 165000],
            ['cat' => 'Lantai & Dinding', 'u' => 'm', 'n' => 'Kuku Macan Silver/Gold (Meter)', 'p' => 10000],

            // Lemkra / Perekat Keramik (Cat & Finishing)
            ['cat' => 'Cat & Finishing', 'u' => 'sak', 'n' => 'Lemkra 5kg 101 (Pasang Kramik)', 'p' => 40000],
            ['cat' => 'Cat & Finishing', 'u' => 'sak', 'n' => 'Lemkra 5kg 111 (Dinding)', 'p' => 45000],
            ['cat' => 'Cat & Finishing', 'u' => 'sak', 'n' => 'Lemkra 5kg 105 (Beton)', 'p' => 70000],

            // Lis Gipsum / Profil Plafon (Kayu & Atap)
            // Ukuran 13 cm
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 13cm - Mata Sapi', 'p' => 16500],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 13cm - Mawar Besar', 'p' => 16500],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 13cm - Tombak Besar', 'p' => 16500],
            // Ukuran 12 cm
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 12cm - Minimalis Besar', 'p' => 15000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 12cm - Kangkung', 'p' => 15000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 12cm - Kupat', 'p' => 15000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 12cm - Bendera', 'p' => 15000],
            // Ukuran 8 cm
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 8cm - Minimalis Kecil', 'p' => 13000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 8cm - Renda', 'p' => 13000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Gipsum 8cm - Piano', 'p' => 13000],
            // Ukuran 5 cm (Biding)
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Biding 5cm - Polos Kecil', 'p' => 13000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Biding 5cm - Tambang', 'p' => 13000],
            ['cat' => 'Kayu & Atap', 'u' => 'btg', 'n' => 'Lis Biding 5cm - Melati', 'p' => 13000],

            // Tabok Lampu / Centerpiece Plafon (Kayu & Atap)
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Bulat Kecil', 'p' => 40000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Sawi Bintang', 'p' => 50000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Sarang Tawon', 'p' => 50000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Oreo/Kotak', 'p' => 50000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Oval Kupu', 'p' => 50000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Oval Batik', 'p' => 70000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Batik Besar', 'p' => 80000],
            ['cat' => 'Kayu & Atap', 'u' => 'pcs', 'n' => 'Tabok Lampu - Segi 8 Besar', 'p' => 80000],

            // Paku & Kawat (Paku & Alat)
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 3/4 inch', 'p' => 25000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 1 inch', 'p' => 24000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 1-1/4 inch', 'p' => 23000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 1-1/2 inch (4cm)', 'p' => 21000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 5cm', 'p' => 20000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 7cm', 'p' => 20000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 10cm', 'p' => 20000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku Kayu Ukuran 12cm', 'p' => 20000],
            ['cat' => 'Paku & Alat', 'u' => 'kg', 'n' => 'Paku GRC / Jalusi', 'p' => 25000],
            ['cat' => 'Paku & Alat', 'u' => 'dus', 'n' => 'Paku Kayu 1 Dus (Isi 30kg)', 'p' => 368000],
            ['cat' => 'Paku & Alat', 'u' => 'roll', 'n' => 'Kawat Tali / Bendrat (PT Family)', 'p' => 300000],
            ['cat' => 'Paku & Alat', 'u' => 'roll', 'n' => 'Kawat Tali / Bendrat (Biasa)', 'p' => 260000],
        ];

        foreach ($products as $item) {
            Product::create([
                'category_id' => $getCat($item['cat']),
                'unit_id' => $getUnit($item['u']),
                'sku' => 'P.' . date('ymd') . '.' . strtoupper(Str::random(4)),
                'name' => $item['n'],
                'description' => 'Stok tersedia untuk ' . $item['n'],
                'purchase_price' => $item['p'] * 0.85, // Margin 15%
                'selling_price' => $item['p'],
                'quantity' => rand(10, 100),
                'min_stock' => 5,
                'is_active' => true,
            ]);
        }
    }
}
