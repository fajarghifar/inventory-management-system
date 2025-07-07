<?php

namespace App\Filament\Widgets;

use App\Models\Deposit;
use Filament\Widgets\ChartWidget;

class DepositByDateChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Deposit Chart By Date';

    protected static string $color = 'success';

    protected function getData(): array
    {
        $deposits = Deposit::groupBy('deposit_date')
            ->selectRaw('sum(amount) as sum, deposit_date')
            ->pluck('sum', 'deposit_date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Deposits',
                    'data' => array_values($deposits),
                ],
            ],
            'labels' => array_keys($deposits),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
