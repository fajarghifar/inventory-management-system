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
            self::DRAFT => 'text-gray-600 bg-gray-50 border-gray-200',
            self::ORDERED => 'text-sky-700 bg-sky-50 border-sky-200',
            self::RECEIVED => 'text-green-700 bg-green-50 border-green-200',
            self::PAID => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            self::CANCELLED => 'text-red-700 bg-red-50 border-red-200',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::ORDERED], true);
    }

    public function isDeletable(): bool
    {
        return in_array($this, [self::DRAFT, self::CANCELLED], true);
    }

    public function canBeOrdered(): bool
    {
        return $this === self::DRAFT;
    }

    public function canBeReceived(): bool
    {
        return in_array($this, [self::DRAFT, self::ORDERED], true);
    }

    public function canBePaid(): bool
    {
        return in_array($this, [self::ORDERED, self::RECEIVED], true);
    }

    /** Sengaja negasi agar setara kode awal: CANCELLED juga boleh di-cancel. */
    public function canBeCancelled(): bool
    {
        return !in_array($this, [self::RECEIVED, self::PAID], true);
    }

    public function canBeRestored(): bool
    {
        return $this === self::CANCELLED;
    }
}
