<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Enums\DatePeriod;
use Illuminate\Support\Facades\DB;
use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\Cache;

class DashboardStatsService
{
    /**
     * Get Sales Statistics (Total Revenue, Net Profit, Count)
     */
    public function getSalesStats(Carbon $startDate, Carbon $endDate, string $periodKey): array
    {
        $cacheKey = "dashboard_sales_{$periodKey}_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($startDate, $endDate) {
            $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->get();

            $totalRevenue = $sales->sum('total');
            $count = $sales->count();

            // Calculate Gross Profit: Revenue - COGS
            // COGS = Sum of (Sales Item Quantity * Recorded Cost Price)
            // Using `cost_price` from sale_items ensures we use the HPP at the time of sale.
            $cogs = SaleItem::whereHas('sale', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('sale_date', [$startDate, $endDate])
                          ->where('status', 'completed');
                })
                ->sum(DB::raw('quantity * cost_price'));

            $grossProfit = $totalRevenue - $cogs;

            return [
                'total_revenue' => $totalRevenue,
                'count' => $count,
                'gross_profit' => $grossProfit,
            ];
        });
    }

    /**
     * Get Cash Flow Statistics (Income, Expense, Net)
     */
    public function getCashFlowStats(Carbon $startDate, Carbon $endDate, string $periodKey): array
    {
        $cacheKey = "dashboard_cashflow_{$periodKey}_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($startDate, $endDate) {
            // Calculate Income and Expense based on Transaction Category type.
            // Income = Transactions where category type is 'income'
            // Expense = Transactions where category type is 'expense'

            $transactions = FinanceTransaction::with('category')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->get();

            $income = $transactions->filter(fn($t) => $t->category?->type->value === 'income')->sum('amount');
            $expense = $transactions->filter(fn($t) => $t->category?->type->value === 'expense')->sum('amount');

            return [
                'income' => $income,
                'expense' => $expense,
                'net_cash_flow' => $income - $expense,
            ];
        });
    }

    /**
     * Get Low Stock Products
     */
    public function getLowStockProducts(int $limit = 5): array
    {
        // Cache for 5 minutes as stock levels change frequently.
        return Cache::remember('dashboard_low_stock', now()->addMinutes(5), function () use ($limit) {
            return Product::whereColumn('quantity', '<=', 'min_stock')
                ->where('is_active', true)
                ->orderBy('quantity', 'asc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get Top Selling Products
     */
    public function getTopProducts(Carbon $startDate, Carbon $endDate, int $limit = 5): array
    {
         $cacheKey = "dashboard_top_products_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

         return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($startDate, $endDate, $limit) {
            return SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
                ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('sale_date', [$startDate, $endDate])
                          ->where('status', 'completed');
                })
                ->with('product:id,name,sku')
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                     return [
                         'product_name' => $item->product->name,
                         'sku' => $item->product->sku,
                         'total_sold' => $item->total_qty
                     ];
                })
                ->toArray();
         });
    }

    /**
     * Get Recent Sales
     */
    public function getRecentSales(int $limit = 5): array
    {
        return Cache::remember('dashboard_recent_sales', now()->addMinutes(1), function () use ($limit) {
            return Sale::with('customer:id,name')
                ->orderByDesc('sale_date')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get Sales Chart Data (Daily Trend)
     */
    public function getSalesTrend(Carbon $startDate, Carbon $endDate): array
    {
         $cacheKey = "dashboard_sales_trend_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

         return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($startDate, $endDate) {
            $data = Sale::selectRaw('DATE(sale_date) as date, SUM(total) as total')
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('total', 'date')
                ->toArray();

            // Fill missing dates with 0
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $chartData = [];

            foreach ($period as $date) {
                $formattedDate = $date->format('Y-m-d');
                $chartData[$formattedDate] = $data[$formattedDate] ?? 0;
            }

            return $chartData;
         });
    }

    /**
     * Get Cash Flow Chart Data (Income vs Expense)
     */
    public function getCashFlowTrend(Carbon $startDate, Carbon $endDate): array
    {
         $cacheKey = "dashboard_cashflow_trend_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

         return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($startDate, $endDate) {
            $transactions = FinanceTransaction::with('category')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->get();

            // Group by date
            $grouped = $transactions->groupBy(function($item) {
                return $item->transaction_date->format('Y-m-d');
            });

            // Fill missing dates
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $incomeData = [];
            $expenseData = [];

            foreach ($period as $date) {
                $formattedDate = $date->format('Y-m-d');
                $dayTransactions = $grouped->get($formattedDate, collect());

                $incomeData[$formattedDate] = $dayTransactions->filter(fn($t) => $t->category?->type->value === 'income')->sum('amount');
                $expenseData[$formattedDate] = $dayTransactions->filter(fn($t) => $t->category?->type->value === 'expense')->sum('amount');
            }

            return [
                'income' => $incomeData,
                'expense' => $expenseData,
            ];
         });
    }
}
