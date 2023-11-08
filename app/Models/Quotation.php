<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class Quotation extends Model
{
    use Sortable;

    protected $fillable = [
        'date',
        'reference',
        'customer_id',
        'customer_name',
        'tax_percentage',
        'tax_amount',
        'discount_percentage',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'status',
        'note',
        'created_at',
        'updated_at'
    ];

    public array $sortable = [
        'customer',
        'status',
        'total_amount'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = Quotation::max('id') + 1;
            $model->reference = make_reference_id('QT', $number);
        });
    }

    public function quotationDetails(): HasMany
    {
        return $this->hasMany(QuotationDetails::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

//    protected function totalAmount(): Attribute
//    {
//        return Attribute::make(
//            get: fn ($value) => $value / 100,
//            //set: fn ($value) => $value * 100,
//        );
//    }
}
