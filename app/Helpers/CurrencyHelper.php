<?php

use App\Models\Setting;

if (!function_exists('format_money')) {
    /**
     * Format a number into currency based on application settings.
     *
     * @param float|int $amount
     * @return string
     */
    function format_money($amount)
    {
        // Get settings, defaulting to IDR format if not set
        $symbol = Setting::get('currency_symbol', 'Rp');
        $position = Setting::get('currency_position', 'left'); // 'left' or 'right'
        $fractions = (int) Setting::get('currency_fraction_digits', 0);
        $thousand = Setting::get('currency_thousand_separator', '.');
        $decimal = Setting::get('currency_decimal_separator', ',');
        
        $formattedAmount = number_format((float) $amount, $fractions, $decimal, $thousand);

        if ($position === 'left') {
            return "{$symbol} {$formattedAmount}";
        }

        if ($position === 'right') {
            return "{$formattedAmount} {$symbol}";
        }

        // Fallback
        return "{$symbol} {$formattedAmount}";
    }
}
