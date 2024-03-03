<?php

namespace App\Enums;

enum QuotationStatus: int
{
    case PENDING = 0;
    case SENT = 1;
    case CANCELED = 2;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::SENT => __('Sent'),
            self::CANCELED => __('Canceled'),
        };
    }
}
