<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, Compoships;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'purchase_no',
        'purchase_status',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

//    public function user(): BelongsTo
//    {
//        // with package
//        //return $this->belongsTo(User::class, ['created_by', 'updated_by'], ['id', 'id']);
//    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class);
    }
}
