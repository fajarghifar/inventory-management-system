<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update or Insert Ethiopian Shopping Center Settings
        $settings = [
            'currency_symbol' => 'ETB',
            'currency_position' => 'left',
            'currency_fraction_digits' => '2',
            'currency_thousand_separator' => ',',
            'currency_decimal_separator' => '.',
        ];

        foreach ($settings as $key => $val) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $val, 'updated_at' => now()]
            );
        }

        // If store still has default Indonesian sample name, update to Ethiopian Shopping Center
        $currentStore = DB::table('settings')->where('key', 'store_name')->value('value');
        if (!$currentStore || str_contains($currentStore, 'Kencana Pamijahan')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'store_name'],
                ['value' => 'Addis Shopping Center', 'updated_at' => now()]
            );
            DB::table('settings')->updateOrInsert(
                ['key' => 'store_address'],
                ['value' => 'Bole Medhanialem, Bole Sub-City, Addis Ababa, Ethiopia', 'updated_at' => now()]
            );
            DB::table('settings')->updateOrInsert(
                ['key' => 'store_phone'],
                ['value' => '+251 911 234 567', 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible configuration update
    }
};
