<?php

namespace App\Models;

use App\Enums\StockAdjustmentType;
use App\Enums\StockAdjustmentReason;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StockAdjustment extends Model
{
    protected $table = 'stock_adjustments';

    protected $fillable = [
        'adjustment_number',
        'product_id',
        'type',
        'reason',
        'quantity',
        'cost_price_at_time',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'type' => StockAdjustmentType::class,
        'reason' => StockAdjustmentReason::class,
        'quantity' => 'integer',
        'cost_price_at_time' => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }
}

