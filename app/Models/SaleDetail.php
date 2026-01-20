<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'cost_price',
        'unit_price',
        'discount',
        'final_price',
        'subtotal',
    ];

    protected $casts = [
        'cost_price' => 'integer',
        'unit_price' => 'integer',
        'discount' => 'integer',
        'final_price' => 'integer',
        'subtotal' => 'integer',
        'quantity' => 'integer',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
