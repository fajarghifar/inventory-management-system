<?php

namespace App\Livewire\FinanceTransactions;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use Illuminate\Database\Eloquent\Builder;
use App\Services\FinanceTransactionService;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use App\Exceptions\FinanceTransactionException;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class FinanceTransactionTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'finance-transaction-table';
    public string $sortField = 'transaction_date';
    public string $sortDirection = 'desc';

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('finance_transaction_export_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100, 0])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return FinanceTransaction::query()
            ->with(['category', 'creator']);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('code')
            ->add('transaction_date_formatted', fn(FinanceTransaction $model) => Carbon::parse($model->transaction_date)->format('d/m/Y'))
            ->add('reference_display', function (FinanceTransaction $model) {
                $tag = $model->reference_type ? '<span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800">Auto</span>' : '<span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-800">Manual</span>';
                
                if (!empty($model->external_reference)) {
                    return $tag . ' ' . $model->external_reference;
                }
                return $tag . ' ' . $model->code;
            })
            ->add('category_name', fn(FinanceTransaction $model) => $model->category->name)
            ->add('type_badge', function(FinanceTransaction $model) {
                return view('components.status-badge', ['status' => $model->category->type])->render();
            })
            ->add('description', fn(FinanceTransaction $model) => Str::limit($model->description, 30))
            ->add('amount')
            ->add('amount_formatted', function (FinanceTransaction $model) {
                $type = $model->category->type->value ?? '';
                $color = $type === 'income' ? 'text-emerald-600' : 'text-red-600';
                $prefix = $type === 'income' ? '+' : '-';
                return "<div class=\"text-right {$color} font-medium\">{$prefix} " . format_money($model->amount) . "</div>";
            })
            ->add('creator_name', fn(FinanceTransaction $model) => $model->creator->name)
            ->add('created_at')
            ->add('date_period', fn() => '');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Date', 'transaction_date_formatted', 'transaction_date')
                ->sortable(),

            Column::make('Reference', 'reference_display', 'code')
                ->sortable()
                ->searchable(),

            Column::make('Period', 'date_period')
                ->hidden(),

            Column::make('Category', 'category_name', 'finance_category_id')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'type_badge')
                ->sortable(),

            Column::make('Amount', 'amount_formatted', 'amount')
                ->sortable()
                ->headerAttribute('text-right')
                ->bodyAttribute('text-right'),

            Column::make('Description', 'description')
                ->sortable()
                ->searchable(),

            Column::make('Created By', 'creator_name', 'created_by')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::multiSelectAsync('category_name', 'finance_category_id')
                ->url(route('ajax.finance-categories.search'))
                ->method('POST')
                ->optionLabel('text')
                ->optionValue('value'),

            Filter::datepicker('transaction_date_formatted', 'transaction_date')
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
                            $query->whereDate('transaction_date', now());
                            break;
                        case 'yesterday':
                            $query->whereDate('transaction_date', now()->subDay());
                            break;
                        case 'this_week':
                            $query->whereBetween('transaction_date', [now()->startOfWeek(), now()->endOfWeek()]);
                            break;
                        case 'last_week':
                            $query->whereBetween('transaction_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $query->whereMonth('transaction_date', now()->month)
                                ->whereYear('transaction_date', now()->year);
                            break;
                        case 'last_month':
                            $query->whereMonth('transaction_date', now()->subMonth()->month)
                                ->whereYear('transaction_date', now()->subMonth()->year);
                            break;
                    }
                }),
        ];
    }

    public function actions(FinanceTransaction $row): array
    {
        $actions = [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>')
                ->class('bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('view-finance-transaction', ['transaction' => $row->id])
                ->tooltip('View Detail'),
        ];

        // View Source Button for System Generated
        if ($row->reference_type === \App\Models\Sale::class) {
            $actions[] = Button::add('view-source')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>')
                ->class('bg-indigo-500 hover:bg-indigo-600 text-white p-2 rounded-md flex items-center justify-center')
                ->route('sales.show', ['sale' => $row->reference_id])
                ->tooltip('Go to Sale');
        } elseif ($row->reference_type === \App\Models\Purchase::class) {
            $actions[] = Button::add('view-source')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>')
                ->class('bg-indigo-500 hover:bg-indigo-600 text-white p-2 rounded-md flex items-center justify-center')
                ->route('purchases.show', ['purchase' => $row->reference_id])
                ->tooltip('Go to Purchase');
        }

        // Only allow edit/delete for Manual Transactions (where reference_type is null)
        if (is_null($row->reference_type)) {
            $actions[] = Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>')
                ->class('bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('edit-finance-transaction', ['transaction' => $row->id])
                ->tooltip('Edit Transaction');

            $actions[] = Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>')
                ->class('bg-red-500 hover:bg-red-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('open-delete-modal', [
                    'component' => 'finance-transactions.finance-transaction-table',
                    'method' => 'delete',
                    'params' => ['rowId' => $row->id],
                    'title' => 'Delete Transaction?',
                    'description' => "Are you sure you want to delete this transaction? This action cannot be undone.",
                ])
                ->tooltip('Delete Transaction');
        }

        return $actions;
    }

    public function header(): array
    {
        return [
            Button::add('print-selected')
                ->slot('🖨️ Print Selected')
                ->class('bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md font-medium text-sm')
                ->dispatch('bulk-print', []),
        ];
    }

    #[\Livewire\Attributes\On('bulk-print')]
    public function bulkPrint(): void
    {
        $checkboxValues = $this->checkboxValues;

        if (empty($checkboxValues)) {
            $this->dispatch('toast', message: 'No transactions selected.', type: 'warning');
            return;
        }

        // Generate a unique ID for this print session
        $printId = (string) Str::uuid();

        // Store selected IDs in cache for 5 minutes
        \Illuminate\Support\Facades\Cache::put("finance_print_{$printId}", $checkboxValues, now()->addMinutes(5));

        // Get filter info to pass along
        $period = $this->filters['date_period'] ?? null;
        if (is_array($period)) {
            $period = $period[0] ?? null;
        }

        // Construct URL
        $url = route('finance.transactions.print', ['printId' => $printId]);

        if ($period) {
            $url .= '?period=' . $period;
        }

        $this->dispatch('open-print-window', url: $url);
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, FinanceTransactionService $service): void
    {
        $transaction = FinanceTransaction::find($rowId);

        if ($transaction) {
            try {
                $service->deleteTransaction($transaction);
                $this->dispatch('toast', message: 'Transaction deleted successfully.', type: 'success');
            } catch (\Exception $e) {
                $message = $e instanceof FinanceTransactionException
                    ? $e->getMessage()
                    : 'Failed to delete transaction: ' . $e->getMessage();

                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }
}
