<?php

namespace App\Livewire\Sales;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Customer;
use App\Enums\SaleStatus;
use App\Services\SaleService;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class SalesTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'sales-table';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('sales_export_' . now()->format('Y_m_d'))
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
        return Sale::query()
            ->with(['customer', 'creator']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('invoice_number', fn(Sale $model) => $model->invoice_number ?: '<span class="italic text-gray-400">-</span>')
            ->add('customer_name', fn(Sale $model) => $model->customer ? $model->customer->name : '<span class="italic text-gray-400">Guest</span>')
            ->add('sale_date_formatted', fn(Sale $model) => Carbon::parse($model->sale_date)->format('d/m/Y'))
            ->add('total_formatted', fn(Sale $model) => 'Rp ' . number_format($model->total, 0, ',', '.'))
            ->add('status_badge', function(Sale $model) {
                return view('components.status-badge', ['status' => $model->status])->render();
            })
            ->add('date_period', fn() => '') // Virtual field for filter
            ->add('created_by_name', fn(Sale $model) => $model->creator->name)
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hidden(),

            Column::make('Invoice', 'invoice_number')
                ->searchable()
                ->sortable()
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left text-indigo-600 hover:text-indigo-900'),

            Column::make('Customer', 'customer_name', 'customer_id')
                ->searchable()
                ->sortable()
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left'),

            Column::make('Date', 'sale_date_formatted', 'sale_date')
                ->sortable()
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left'),

            Column::make('Period', 'date_period') // Hidden column for filter
                ->hidden(),

            Column::make('Total', 'total_formatted', 'total')
                ->sortable()
                ->headerAttribute('text-right')
                ->bodyAttribute('text-right'),

            Column::make('Status', 'status_badge', 'status')
                ->sortable()
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),

            Column::action('Action')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::multiSelect('customer_name', 'customer_id')
                ->dataSource(Customer::all())
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('status', 'status')
                ->dataSource(collect(SaleStatus::cases())->map(fn($status) => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])->toArray())
                ->optionLabel('label')
                ->optionValue('value'),

            Filter::datepicker('sale_date_formatted', 'sale_date')
                ->params([
                    'enableTime' => false,
                    'dateFormat' => 'Y-m-d',
                    'altInput' => true,
                    'altFormat' => 'd/m/Y',
                ]),

            Filter::select('date_period')
                ->dataSource([
                    ['name' => 'Today', 'value' => 'today'],
                    ['name' => 'Yesterday', 'value' => 'yesterday'],
                    ['name' => 'This Week', 'value' => 'this_week'],
                    ['name' => 'Last Week', 'value' => 'last_week'],
                    ['name' => 'This Month', 'value' => 'this_month'],
                    ['name' => 'Last Month', 'value' => 'last_month'],
                ])
                ->optionLabel('name')
                ->optionValue('value')
                ->builder(function (Builder $query, string $value) {
                    switch ($value) {
                        case 'today':
                            $query->whereDate('sale_date', now());
                            break;
                        case 'yesterday':
                            $query->whereDate('sale_date', now()->subDay());
                            break;
                        case 'this_week':
                            $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
                            break;
                        case 'last_week':
                            $query->whereBetween('sale_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $query->whereMonth('sale_date', now()->month)
                                ->whereYear('sale_date', now()->year);
                            break;
                        case 'last_month':
                            $query->whereMonth('sale_date', now()->subMonth()->month)
                                ->whereYear('sale_date', now()->subMonth()->year);
                            break;
                    }
                }),
        ];
    }

    public function actions(Sale $row): array
    {
        return [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>')
                ->class('bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md flex items-center justify-center')
                ->route('sales.show', ['sale' => $row->id])
                ->tooltip('View Details'),

            Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>')
                ->class('bg-red-500 hover:bg-red-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('cancel', ['rowId' => $row->id])
                ->attributes(['wire:confirm' => 'Are you sure you want to CANCEL this sale?'])
                ->tooltip('Cancel/Void Sale')
                ->can(fn($row) => $row->status !== SaleStatus::CANCELLED),
        ];
    }

    #[\Livewire\Attributes\On('cancel')]
    public function cancel($rowId, SaleService $saleService): void
    {
        $sale = Sale::find($rowId);
        if ($sale) {
            try {
                $saleService->cancelSale($sale);
                $this->dispatch('toast', message: 'Sale cancelled.', type: 'success');
            } catch (\Exception $e) {
                $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
            }
        }
    }
}
