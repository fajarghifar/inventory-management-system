<?php

namespace App\Livewire\Sales;

use Carbon\Carbon;
use App\Models\Sale;
use App\Enums\SaleStatus;
use App\Services\SaleService;
use App\Exceptions\SaleException;
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
            ->add('invoice_number', fn(Sale $model) => $model->invoice_number ?: '-')
            ->add('customer_name', fn(Sale $model) => $model->customer ? $model->customer->name : 'Guest')
            ->add('sale_date_formatted', fn(Sale $model) => Carbon::parse($model->sale_date)->format('d/m/Y'))
            ->add('total_formatted', fn(Sale $model) => 'Rp ' . number_format($model->total, 0, ',', '.'))
            ->add('status_badge', function(Sale $model) {
                return view('components.status-badge', ['status' => $model->status])->render();
            })
            ->add('date_period', fn() => '')
            ->add('creator_name', fn(Sale $model) => $model->creator ? $model->creator->name : '-')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::action('Action'),

            Column::make('ID', 'id')->hidden(),

            Column::make('Invoice', 'invoice_number')
                ->searchable()
                ->sortable(),

            Column::make('Customer', 'customer_name', 'customer_id')
                ->searchable()
                ->sortable(),

            Column::make('Created By', 'creator_name', 'created_by')
                ->sortable(),

            Column::make('Date', 'sale_date_formatted', 'sale_date')
                ->sortable(),

            Column::make('Period', 'date_period')
                ->hidden(),

            Column::make('Total', 'total_formatted', 'total')
                ->sortable()
                ->headerAttribute('text-right')
                ->bodyAttribute('text-right'),

            Column::make('Status', 'status_badge', 'status')
                ->sortable()
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center'),

        ];
    }

    public function relationSearch(): array
    {
        return [
            'customer' => ['name'],
        ];
    }

    public function filters(): array
    {
        return [
            Filter::multiSelectAsync('customer_name', 'customer_id')
                ->url(route('ajax.customers.search'))
                ->method('POST')
                ->optionValue('value')
                ->optionLabel('text'),

            Filter::multiSelect('status', 'status')
                ->dataSource(collect(SaleStatus::cases())->map(fn($status) => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])->toArray())
                ->optionLabel('label')
                ->optionValue('value'),

            Filter::multiSelectAsync('creator_name', 'created_by')
                ->url(route('ajax.users.search'))
                ->method('POST')
                ->optionValue('value')
                ->optionLabel('text'),

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

            Button::add('print')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.198-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>')
                ->class('bg-indigo-500 hover:bg-indigo-600 text-white p-2 rounded-md flex items-center justify-center')
                ->route('sales.print', ['sale' => $row->id])
                ->tooltip('Print Invoice'),

            Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>')
                ->class('bg-red-500 hover:bg-red-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('open-delete-modal', [
                    'component' => 'sales.sales-table',
                    'method' => 'delete',
                    'params' => ['rowId' => $row->id],
                    'title' => 'Delete Sale?',
                    'description' => "Are you sure you want to PERMANENTLY DELETE invoice '{$row->invoice_number}'? This action cannot be undone.",
                ])
                ->tooltip('Delete Sale')
                ->can(fn($row) => $row->status === SaleStatus::CANCELLED),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, SaleService $saleService): void
    {
        $sale = Sale::find($rowId);
        if ($sale) {
            try {
                $saleService->deleteSale($sale);
                $this->dispatch('toast', message: 'Sale deleted successfully.', type: 'success');
            } catch (SaleException $e) {
                $this->dispatch('toast', message: 'Delete failed: ' . $e->getMessage(), type: 'error');
            } catch (\Exception $e) {
                $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
            }
        }
    }
}
