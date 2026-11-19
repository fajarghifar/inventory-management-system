<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Enums\SaleStatus;
use App\Enums\PaymentMethod;
use App\Services\SaleService;
use App\Exceptions\SaleException;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = 'all'; // all, completed, pending, cancelled

    #[Url(as: 'period')]
    public string $datePeriod = 'all'; // all, today, yesterday, this_week, this_month

    #[Url(as: 'payment')]
    public string $paymentFilter = 'all'; // all, cash, transfer

    #[Url(as: 'sort')]
    public string $sortBy = 'latest'; // latest, oldest, total_desc, total_asc

    #[Url(as: 'per_page')]
    public int $perPage = 12;

    public string $viewMode = 'adaptive'; // adaptive, table, cards

    protected $listeners = [
        'pg:eventRefresh-sales-table' => '$refresh',
        'sale-saved' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDatePeriod(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'datePeriod', 'paymentFilter', 'sortBy']);
        $this->resetPage();
    }

    public function deleteSale(int $id, SaleService $service): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Unauthorized. Only administrators can delete sales.');

        $sale = Sale::find($id);
        if ($sale) {
            if ($sale->status !== SaleStatus::CANCELLED) {
                $this->dispatch('toast', message: 'Only cancelled sales can be permanently deleted.', type: 'error');
                return;
            }

            try {
                $service->deleteSale($sale);
                $this->dispatch('toast', message: 'Sale invoice deleted permanently.', type: 'success');
            } catch (\Exception $e) {
                $message = $e instanceof SaleException
                    ? $e->getMessage()
                    : 'Failed to delete sale: ' . $e->getMessage();
                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }

    public function exportCsv(): StreamedResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Unauthorized.');
        $fileName = 'sales_export_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Invoice Number',
                'Date',
                'Cashier',
                'Customer',
                'Payment Method',
                'Items Count',
                'Subtotal (ETB)',
                'Total Discount (ETB)',
                'Total (ETB)',
                'Status',
            ]);

            Sale::with(['customer', 'creator', 'items'])
                ->latest('sale_date')
                ->chunk(250, function ($sales) use ($handle) {
                    foreach ($sales as $s) {
                        fputcsv($handle, [
                            $s->id,
                            $s->invoice_number ?: '-',
                            $s->sale_date ? $s->sale_date->format('Y-m-d H:i:s') : '-',
                            $s->creator?->name ?? 'System',
                            $s->customer?->name ?? 'Walk-in Customer',
                            $s->payment_method ? $s->payment_method->label() : '-',
                            $s->items->sum('quantity'),
                            $s->subtotal,
                            $s->total_discount,
                            $s->total,
                            $s->status ? $s->status->label() : '-',
                        ]);
                    }
                });
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    protected function applyDatePeriod(Builder $query, string $period): void
    {
        match ($period) {
            'today' => $query->whereDate('sale_date', now()),
            'yesterday' => $query->whereDate('sale_date', now()->subDay()),
            'this_week' => $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]),
            'this_month' => $query->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year),
            default => null,
        };
    }

    public function render()
    {
        // 1. KPI Overview Metrics (Calculated for current date period)
        $periodQuery = Sale::query();
        $this->applyDatePeriod($periodQuery, $this->datePeriod);

        $totalTransactions = (clone $periodQuery)->count();
        $completedQuery = (clone $periodQuery)->where('status', SaleStatus::COMPLETED);
        $completedCount = (clone $completedQuery)->count();
        $totalRevenue = (clone $completedQuery)->sum('total');
        $avgSale = $completedCount > 0 ? ($totalRevenue / $completedCount) : 0;

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'avg_sale' => $avgSale,
            'completed_count' => $completedCount,
            'period_label' => match ($this->datePeriod) {
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'this_week' => 'This Week',
                'this_month' => 'This Month',
                default => 'All Time',
            },
        ];

        // 2. Query Builder
        $query = Sale::query()
            ->with(['customer', 'creator', 'items.product.unit']);

        // Date period filter
        $this->applyDatePeriod($query, $this->datePeriod);

        // Status filter
        if ($this->statusFilter !== 'all') {
            $statusEnum = SaleStatus::tryFrom($this->statusFilter);
            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
        }

        // Payment method filter
        if ($this->paymentFilter !== 'all') {
            $paymentEnum = PaymentMethod::tryFrom($this->paymentFilter);
            if ($paymentEnum) {
                $query->where('payment_method', $paymentEnum);
            }
        }

        // Search (invoice number, cashier, customer, product name)
        if (trim($this->search) !== '') {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', $search)
                    ->orWhereHas('creator', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', $search);
                    })
                    ->orWhereHas('customer', function ($custQuery) use ($search) {
                        $custQuery->where('name', 'like', $search);
                    })
                    ->orWhereHas('items.product', function ($prodQuery) use ($search) {
                        $prodQuery->where('name', 'like', $search)
                            ->orWhere('sku', 'like', $search);
                    });
            });
        }

        // Sorting
        match ($this->sortBy) {
            'oldest' => $query->orderBy('sale_date', 'asc')->orderBy('id', 'asc'),
            'total_desc' => $query->orderBy('total', 'desc'),
            'total_asc' => $query->orderBy('total', 'asc'),
            default => $query->orderBy('sale_date', 'desc')->orderBy('id', 'desc'),
        };

        $sales = $query->paginate($this->perPage);

        return view('livewire.sales.sales-catalog', [
            'sales' => $sales,
            'stats' => $stats,
        ]);
    }
}
