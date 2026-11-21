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
        Setting::set('store_name', 'Addis Shopping Center');
        Setting::set('store_address', 'Bole Medhanialem, Bole Sub-City, Addis Ababa, Ethiopia');
        Setting::set('store_phone', '+251 911 234 567');
        Setting::set('opening_balance_date', now()->startOfYear()->toDateString());
        Setting::set('opening_balance_amount', '50000.00');
        Setting::set('currency_symbol', 'ETB');
        Setting::set('currency_position', 'left');
        Setting::set('currency_fraction_digits', '2');
        Setting::set('currency_thousand_separator', ',');
        Setting::set('currency_decimal_separator', '.');
    }
}
