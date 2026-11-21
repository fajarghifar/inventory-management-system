<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Force ETB currency settings directly through model (which handles Cache::forget)
        $settings = [
            'currency_symbol' => 'ETB',
            'currency_position' => 'left',
            'currency_fraction_digits' => '2',
            'currency_thousand_separator' => ',',
            'currency_decimal_separator' => '.',
            'store_name' => 'Addis Shopping Center',
            'store_address' => 'Bole Medhanialem, Bole Sub-City, Addis Ababa, Ethiopia',
            'store_phone' => '+251 911 234 567',
        ];

        foreach ($settings as $key => $val) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $val, 'updated_at' => now()]
            );
        }

        // 2. Clear application cache so old "Rp" string is immediately discarded
        try {
            Cache::flush();
        } catch (\Throwable $e) {
            // cache driver fallback
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
