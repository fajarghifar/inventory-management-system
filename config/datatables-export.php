<?php

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

return [

    /*
    |--------------------------------------------------------------------------
    | Method
    |--------------------------------------------------------------------------
    |
    | Method to use to iterate with the query results.
    | Options: lazy, cursor
    |
    | @link https://laravel.com/docs/eloquent#cursors
    | @link https://laravel.com/docs/eloquent#chunking-using-lazy-collections
    |
    */
    'method' => 'lazy',

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Chunk size to be used when using lazy method.
    |
    */
    'chunk' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Export filesystem disk
    |--------------------------------------------------------------------------
    |
    | Export filesystem disk where generated files will be stored.
    |
    */
    'disk' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Use S3 for final file destination
    |--------------------------------------------------------------------------
    |
    | After generating the file locally, it can be uploaded to s3.
    |
    */
    's3_disk' => '',

    /*
    |--------------------------------------------------------------------------
    | Mail from address
    |--------------------------------------------------------------------------
    |
    | Will be used to email report from this address.
    |
    */
    'mail_from' => env('MAIL_FROM_ADDRESS', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Date Format
    |--------------------------------------------------------------------------
    |
    | Default export format for date.
    |
    */
    'default_date_format' => 'yyyy-mm-dd',

    /*
    |--------------------------------------------------------------------------
    | Valid Date Formats
    |--------------------------------------------------------------------------
    |
    | List of valid date formats to be used for auto-detection.
    |
    */
    'date_formats' => [
        'mm/dd/yyyy',
        NumberFormat::FORMAT_DATE_DATETIME,
        NumberFormat::FORMAT_DATE_YYYYMMDD,
        NumberFormat::FORMAT_DATE_XLSX22,
        NumberFormat::FORMAT_DATE_DDMMYYYY,
        NumberFormat::FORMAT_DATE_DMMINUS,
        NumberFormat::FORMAT_DATE_DMYMINUS,
        NumberFormat::FORMAT_DATE_DMYSLASH,
        NumberFormat::FORMAT_DATE_MYMINUS,
        NumberFormat::FORMAT_DATE_TIME1,
        NumberFormat::FORMAT_DATE_TIME2,
        NumberFormat::FORMAT_DATE_TIME3,
        NumberFormat::FORMAT_DATE_TIME4,
        NumberFormat::FORMAT_DATE_TIME5,
        NumberFormat::FORMAT_DATE_TIME6,
        NumberFormat::FORMAT_DATE_TIME7,
        NumberFormat::FORMAT_DATE_XLSX14,
        NumberFormat::FORMAT_DATE_XLSX15,
        NumberFormat::FORMAT_DATE_XLSX16,
        NumberFormat::FORMAT_DATE_XLSX17,
        NumberFormat::FORMAT_DATE_YYYYMMDD2,
        NumberFormat::FORMAT_DATE_YYYYMMDDSLASH,
    ],

    /*
    |--------------------------------------------------------------------------
    | Valid Text Formats
    |--------------------------------------------------------------------------
    |
    | List of valid text formats to be used.
    |
    */
    'text_formats' => [
        '@',
        NumberFormat::FORMAT_GENERAL,
        NumberFormat::FORMAT_TEXT,
    ],

    /*
    |--------------------------------------------------------------------------
    | Purge Options
    |--------------------------------------------------------------------------
    |
    | Purge all exported by purge.days old files.
    |
    */
    'purge' => [
        'days' => 1,
    ],
];
