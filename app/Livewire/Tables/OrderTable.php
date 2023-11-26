<?php

namespace App\Livewire\Tables;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class OrderTable extends DataTableComponent
{
    protected $model = Order::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Customer id", "customer_id")
                ->sortable(),
            Column::make("Order date", "order_date")
                ->sortable(),
            Column::make("Order status", "order_status")
                ->sortable(),
            Column::make("Invoice no", "invoice_no")
                ->sortable(),
            Column::make("Payment type", "payment_type")
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->setFilterPillTitle('Order Status')
                ->setFilterPillValues([
                    'complete' => 'complete',
                    'pending' => 'pending',
                ])
                ->options([
                    '' => 'All',
                    'complete' => 'complete',
                    'pending' => 'pending',
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value === 'complete') {
                        $builder->where('order_status', 'complete');
                    } elseif ($value === 'pending') {
                        $builder->where('order_status', 'pending');
                    }
                }),
        ];
    }
}
