<?php

namespace Database\Seeders;

use App\Models\Adjustment;
use App\Models\AdjustmentProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Adjustment::factory()
            ->count(10)
            ->has(AdjustmentProduct::factory()->count(5), 'adjustmentProducts')
            ->create();
    }
}
