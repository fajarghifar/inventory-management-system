<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('store_name', 'TB. Kencana Pamijahan');
        Setting::set('store_address', 'Pamijahan, Kec. Plumbon, Kabupaten Cirebon, Jawa Barat 45155');
        Setting::set('store_phone', '081234567890');
        Setting::set('opening_balance_date', now()->startOfYear()->toDateString());
        Setting::set('opening_balance_amount', '10000000');
        Setting::set('currency_symbol', 'Rp');
        Setting::set('currency_position', 'left');
        Setting::set('currency_fraction_digits', '0');
        Setting::set('currency_thousand_separator', '.');
        Setting::set('currency_decimal_separator', ',');
    }
}
