<?php

namespace App\Enums;

enum PurchaseStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
        };
    }
}
