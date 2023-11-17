<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'name',
        'slug',
        'short_code'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
