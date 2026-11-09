<?php

namespace App\Livewire\StockMovements;

use Carbon\Carbon;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class StockMovementTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'stock-movement-table';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        return [
            PowerGrid::exportable('stock_movements_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return StockMovement::query()
            ->with(['product.unit', 'user']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('product_name', fn (StockMovement $model) => $model->product ? $model->product->name : '-')
            ->add('product_sku', fn (StockMovement $model) => $model->product ? $model->product->sku : '-')
            ->add('movement_badge', function (StockMovement $model) {
                $isPos = $model->quantity > 0;
                $color = $isPos ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold ' . $color . '">' . $model->movement_type->label() . '</span>';
            })
            ->add('quantity_formatted', fn (StockMovement $model) => ($model->quantity > 0 ? '+' : '') . $model->quantity)
            ->add('quantity_before')
            ->add('quantity_after')
            ->add('reference_number', fn (StockMovement $model) => $model->reference_number ?: '-')
            ->add('user_name', fn (StockMovement $model) => $model->user ? $model->user->name : 'System')
            ->add('notes', fn (StockMovement $model) => $model->notes ?: '-')
            ->add('created_at_formatted', fn (StockMovement $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('Date', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Product', 'product_name')
                ->searchable()
                ->sortable(),

            Column::make('SKU', 'product_sku')
                ->searchable(),

            Column::make('Movement Type', 'movement_badge', 'movement_type')
                ->sortable(),

            Column::make('Change', 'quantity_formatted', 'quantity')
                ->sortable(),

            Column::make('Before', 'quantity_before')
                ->sortable(),

            Column::make('After', 'quantity_after')
                ->sortable(),

            Column::make('Ref / Invoice', 'reference_number')
                ->searchable(),

            Column::make('Responsible User', 'user_name')
                ->searchable(),

            Column::make('Notes', 'notes')
                ->searchable(),
        ];
    }
}

