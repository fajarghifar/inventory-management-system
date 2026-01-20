<?php

namespace App\Models;

use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'created_by',
        'sale_date',
        'status',
        'subtotal',
        'total_discount',
        'total',
        'cash_received',
        'change',
        'cash',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'status' => SaleStatus::class,
        'payment_method' => PaymentMethod::class,
        'subtotal' => 'integer',
        'total_discount' => 'integer',
        'total' => 'integer',
        'cash_received' => 'integer',
        'change' => 'integer',
        'cash' => 'integer',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
