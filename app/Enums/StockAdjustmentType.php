<?php

namespace App\Enums;

enum StockAdjustmentType: string
{
    case ADDITION = 'addition';
    case REDUCTION = 'reduction';

    public function label(): string
    {
        return match($this) {
            self::ADDITION => 'Addition (+)',
            self::REDUCTION => 'Reduction (-)',
        };
    }
}

