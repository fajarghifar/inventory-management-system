<?php

namespace App\Filament\Widgets;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\UnitType;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $purchases = Transaction::sum('total');
        $payments = Payment::sum('amount');
        $dues = $purchases - $payments;
        $deposits = Deposit::sum('amount');

        return [
            Stat::make('Purchased', $purchases),
            Stat::make('Paid', $payments),
            Stat::make('Due', fn () => $dues),
            Stat::make('Deposited', $deposits),
        ];
    }
}
