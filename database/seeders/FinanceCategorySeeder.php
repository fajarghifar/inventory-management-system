<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\FinanceCategory;
use App\Enums\FinanceCategoryType;

class FinanceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income
            [
                'name' => 'Penjualan Produk',
                'type' => FinanceCategoryType::Income,
                'description' => 'Pendapatan langsung dari penjualan produk toko.',
            ],
            [
                'name' => 'Layanan Jasa',
                'type' => FinanceCategoryType::Income,
                'description' => 'Pendapatan dari layanan jasa service atau konsultasi.',
            ],
            [
                'name' => 'Investasi',
                'type' => FinanceCategoryType::Income,
                'description' => 'Dividen atau bunga dari investasi modal.',
            ],
            [
                'name' => 'Pendapatan Lain-lain',
                'type' => FinanceCategoryType::Income,
                'description' => 'Pendapatan di luar operasional utama.',
            ],

            // Expenses
            [
                'name' => 'Gaji Karyawan',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya gaji bulanan dan tunjangan karyawan.',
            ],
            [
                'name' => 'Sewa Gedung',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya sewa toko atau gudang operasional.',
            ],
            [
                'name' => 'Listrik & Air',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Tagihan utilitas bulanan.',
            ],
            [
                'name' => 'Internet & Telepon',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya komunikasi dan koneksi internet.',
            ],
            [
                'name' => 'Pemasaran & Iklan',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya promosi, iklan sosial media, dan cetak.',
            ],
            [
                'name' => 'Perawatan & Perbaikan',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya maintenance aset dan peralatan.',
            ],
            [
                'name' => 'Transportasi & Logistik',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya bensin, pengiriman, dan perjalanan dinas.',
            ],
            [
                'name' => 'Pembelian Stok',
                'type' => FinanceCategoryType::Expense,
                'description' => 'Biaya pembelian barang dagangan (HPP).',
            ],
        ];

        foreach ($categories as $category) {
            FinanceCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'type' => $category['type'],
                'description' => $category['description'],
            ]);
        }
    }
}
