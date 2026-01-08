<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case DRAFT = 'draft';
    case ORDERED = 'ordered';
    case RECEIVED = 'received';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::ORDERED => 'Ordered',
            self::RECEIVED => 'Received',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'text-gray-500 bg-gray-100',
            self::ORDERED => 'text-blue-600 bg-blue-100',
            self::RECEIVED => 'text-green-600 bg-green-100',
            self::PAID => 'text-emerald-600 bg-emerald-100',
            self::CANCELLED => 'text-red-600 bg-red-100',
        };
    }
}
