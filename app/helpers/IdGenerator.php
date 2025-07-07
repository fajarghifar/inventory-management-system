<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class IdGenerator
{
    /**
     * Generate a unique ID with prefix and padded number.
     *
     * @param array $config Configuration array:
     *  - table: string, the database table name
     *  - field: string, the column to check (e.g., 'code')
     *  - length: int, total length of the numeric part (e.g., 4)
     *  - prefix: string, prefix to use (e.g., 'PC')
     * @return string
     */
    public static function generate(array $config): string
    {
        $table = $config['table'];
        $field = $config['field'];
        $length = $config['length'];
        $prefix = $config['prefix'];

        // Get the latest record that starts with the prefix
        $latest = DB::table($table)
            ->where($field, 'like', "{$prefix}%")
            ->orderBy($field, 'desc')
            ->first();

        // Get the numeric part from the latest code
        if ($latest && isset($latest->{$field})) {
            $lastNumber = (int)substr($latest->{$field}, strlen($prefix));
        } else {
            $lastNumber = 0;
        }

        $nextNumber = $lastNumber + 1;

        // Format with leading zeros
        $suffix = str_pad($nextNumber, $length, '0', STR_PAD_LEFT);

        return $prefix . $suffix;
    }
}
