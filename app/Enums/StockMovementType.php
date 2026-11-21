<?php

namespace App\Enums;

enum StockMovementType: string
{
    case OPENING_STOCK = 'opening_stock';
    case SALE = 'sale';
    case PURCHASE_RECEIVE = 'purchase_receive';
    case ADJUSTMENT_IN = 'adjustment_in';
    case ADJUSTMENT_OUT = 'adjustment_out';
    case SALE_CANCELLATION = 'sale_cancellation';
    case PURCHASE_CANCELLATION = 'purchase_cancellation';

    public function label(): string
    {
        return match($this) {
            self::OPENING_STOCK => 'Opening Stock',
            self::SALE => 'Sale',
            self::PURCHASE_RECEIVE => 'Purchase Received',
            self::ADJUSTMENT_IN => 'Adjustment (Stock In)',
            self::ADJUSTMENT_OUT => 'Adjustment (Stock Out)',
            self::SALE_CANCELLATION => 'Sale Restoral',
            self::PURCHASE_CANCELLATION => 'Purchase Cancellation',
        };
    }

    public function isPositive(): bool
    {
        return match($this) {
            self::OPENING_STOCK, self::PURCHASE_RECEIVE, self::ADJUSTMENT_IN, self::SALE_CANCELLATION => true,
            self::SALE, self::ADJUSTMENT_OUT, self::PURCHASE_CANCELLATION => false,
        };
    }
}

