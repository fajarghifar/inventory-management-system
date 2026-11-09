<?php

namespace App\Enums;

enum StockAdjustmentReason: string
{
    case DAMAGE = 'damage';
    case LOSS = 'loss';
    case EXPIRATION = 'expiration';
    case CORRECTION = 'correction';
    case INTERNAL_USE = 'internal_use';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::DAMAGE => 'Damaged / Broken Goods',
            self::LOSS => 'Loss / Theft / Shrinkage',
            self::EXPIRATION => 'Expired Goods',
            self::CORRECTION => 'Physical Count Discrepancy Correction',
            self::INTERNAL_USE => 'Internal / Store Use',
            self::OTHER => 'Other Reason',
        };
    }
}

