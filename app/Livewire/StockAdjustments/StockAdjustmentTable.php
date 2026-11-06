<?php

namespace App\Livewire\StockAdjustments;

use Carbon\Carbon;
use App\Models\StockAdjustment;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class StockAdjustmentTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'stock-adjustment-table';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        return [
            PowerGrid::exportable('stock_adjustments_' . now()->format('Y_m_d'))
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
        return StockAdjustment::query()
            ->with(['product.unit', 'user']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('adjustment_number')
            ->add('product_name', fn (StockAdjustment $model) => $model->product ? $model->product->name : '-')
            ->add('product_sku', fn (StockAdjustment $model) => $model->product ? $model->product->sku : '-')
            ->add('type_badge', fn (StockAdjustment $model) => $model->type->value === 'addition'
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">+ Addition</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">- Reduction</span>')
            ->add('reason_label', fn (StockAdjustment $model) => $model->reason->label())
            ->add('quantity_formatted', fn (StockAdjustment $model) => $model->type->value === 'addition'
                ? '+' . $model->quantity
                : '-' . $model->quantity)
            ->add('user_name', fn (StockAdjustment $model) => $model->user ? $model->user->name : 'System')
            ->add('notes')
            ->add('created_at_formatted', fn (StockAdjustment $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('Adjustment #', 'adjustment_number')
                ->searchable()
                ->sortable(),

            Column::make('Product', 'product_name')
                ->searchable()
                ->sortable(),

            Column::make('SKU', 'product_sku')
                ->searchable(),

            Column::make('Type', 'type_badge', 'type')
                ->sortable(),

            Column::make('Reason', 'reason_label', 'reason')
                ->sortable(),

            Column::make('Quantity', 'quantity_formatted', 'quantity')
                ->sortable(),

            Column::make('Logged By', 'user_name')
                ->searchable(),

            Column::make('Notes', 'notes')
                ->searchable(),

            Column::make('Date', 'created_at_formatted', 'created_at')
                ->sortable(),
        ];
    }
}

