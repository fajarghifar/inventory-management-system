<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'slug',
        'short_code'
    ];

    protected array $sortable = [
        'name',
        'slug',
        'short_code'
    ];

    protected $guarded = [
        'id',
    ];

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
