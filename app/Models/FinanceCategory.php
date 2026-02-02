<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\FinanceCategoryType;

class FinanceCategory extends Model
{
    use HasFactory;

    protected $table = 'finance_categories';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'type' => FinanceCategoryType::class,
        'description' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
