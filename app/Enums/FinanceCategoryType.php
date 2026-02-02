<?php

namespace App\Enums;

enum FinanceCategoryType: string
{
    case Expense = 'expense';
    case Income = 'income';

    public function label(): string
    {
        return match ($this) {
            self::Expense => 'Expense',
            self::Income => 'Income',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Expense => 'bg-red-100 text-red-800 border-red-200',
            self::Income => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }
}
