<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PurchaseChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Chart By Date';
    
    protected static ?int $sort = 3;

    protected static string $color = 'danger';

    protected function getData(): array
    {
        $purchases = DB::table('transactions')
            ->select(DB::raw('DATE(transact_at) as date'), DB::raw('sum(total) as sum'))
            ->groupBy('date')
            ->pluck('sum', 'date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Purchases',
                    'data' => array_values($purchases),
                ],
            ],
            'labels' => array_keys($purchases),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
