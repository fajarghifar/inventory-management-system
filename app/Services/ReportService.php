<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\FinanceTransaction;
use App\Enums\FinanceCategoryType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Get Reports Hub High-Level Business Overview.
     */
    public function getReportsHubOverview(string $period = 'this_month'): array
    {
        $now = Carbon::now();

        // 1. Determine date range based on period
        [$startDate, $endDate, $prevStartDate, $prevEndDate, $periodLabel] = match($period) {
            'today' => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
                'Today'
            ],
            'this_week' => [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek(),
                'This Week'
            ],
            'this_year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
                'This Year'
            ],
            default => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
                'This Month'
            ],
        };

        // 2. Current period sales
        $periodSales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed');
        $totalSales = (float) $periodSales->sum('total');
        $totalTransactions = (int) $periodSales->count();

        // Previous period sales for growth comparison
        $prevPeriodSales = (float) Sale::whereBetween('sale_date', [$prevStartDate, $prevEndDate])
            ->where('status', 'completed')
            ->sum('total');

        $salesGrowth = null;
        if ($prevPeriodSales > 0) {
            $salesGrowth = round((($totalSales - $prevPeriodSales) / $prevPeriodSales) * 100, 1);
        }

        // Today's sales specifically
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $todaySales = (float) Sale::whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('status', 'completed')
            ->sum('total');
        $todayTransactions = (int) Sale::whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('status', 'completed')
            ->count();

        // 3. Stock metrics
        $valuation = $this->getStockValuationSummary();
        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('quantity', '<=', 'min_stock')
            ->count();
        $outOfStockCount = Product::where('is_active', true)
            ->where('quantity', '<=', 0)
            ->count();

        // 4. Estimated Profit for the period
        $pnl = $this->getProfitAndLoss($startDate, $endDate);
        $estimatedProfit = $pnl['estimated_net_profit'];
        $grossProfit = $pnl['gross_profit'];
        $profitMargin = $pnl['gross_margin'];

        // 5. Recent Sales (Latest 5)
        $recentSales = Sale::with('creator', 'items.product')
            ->where('status', 'completed')
            ->orderBy('sale_date', 'desc')
            ->take(5)
            ->get();

        // 6. Top 5 Best Selling Products
        $topProducts = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate])
                  ->where('status', 'completed');
            })
            ->select('product_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(subtotal) as revenue'))
            ->with('product.unit')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product?->name ?? 'Product',
                    'unit' => $item->product?->unit?->symbol ?? 'pcs',
                    'qty' => (int) $item->qty,
                    'revenue' => (float) $item->revenue,
                    'image_url' => $item->product?->image_url,
                ];
            });

        // 7. 7-day Sales & Profit Trend for the visual chart
        $trendDays = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dayDate = $now->copy()->subDays($i);
            $dayStart = $dayDate->copy()->startOfDay();
            $dayEnd = $dayDate->copy()->endOfDay();

            $dayRevenue = (float) Sale::whereBetween('sale_date', [$dayStart, $dayEnd])
                ->where('status', 'completed')
                ->sum('total');

            $dayCogs = (float) SaleItem::whereHas('sale', function ($q) use ($dayStart, $dayEnd) {
                    $q->whereBetween('sale_date', [$dayStart, $dayEnd])
                      ->where('status', 'completed');
                })
                ->sum(DB::raw('quantity * cost_price'));

            $dayProfit = max(0, $dayRevenue - $dayCogs);

            $trendDays->push([
                'day' => $dayDate->format('D'),
                'date' => $dayDate->format('M d'),
                'revenue' => $dayRevenue,
                'profit' => $dayProfit,
            ]);
        }

        return [
            'period' => $period,
            'period_label' => $periodLabel,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_sales' => $totalSales,
            'total_transactions' => $totalTransactions,
            'sales_growth' => $salesGrowth,
            'today_sales' => $todaySales,
            'today_transactions' => $todayTransactions,
            'stock_valuation' => $valuation,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'estimated_profit' => $estimatedProfit,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
            'recent_sales' => $recentSales,
            'top_products' => $topProducts,
            'sales_trend' => $trendDays,
        ];
    }
    /**
     * Get Current Stock Valuation Summary.
     */
    public function getStockValuationSummary(): array
    {
        $products = Product::with(['category', 'unit'])
            ->where('is_active', true)
            ->get();

        $totalItems = $products->count();
        $totalUnits = $products->sum('quantity');
        $totalCostValuation = $products->sum(fn ($p) => $p->quantity * $p->purchase_price);
        $totalRetailValuation = $products->sum(fn ($p) => $p->quantity * $p->selling_price);
        $projectedProfit = $totalRetailValuation - $totalCostValuation;
        $projectedMargin = $totalRetailValuation > 0
            ? ($projectedProfit / $totalRetailValuation) * 100
            : 0;

        return [
            'total_items' => $totalItems,
            'total_units' => $totalUnits,
            'total_cost_valuation' => $totalCostValuation,
            'total_retail_valuation' => $totalRetailValuation,
            'projected_profit' => $projectedProfit,
            'projected_margin' => $projectedMargin,
            'products' => $products,
        ];
    }

    /**
     * Get Stock grouped by Category.
     */
    public function getStockByCategory(): Collection
    {
        return Category::withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->with(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($cat) {
                $totalUnits = $cat->products->sum('quantity');
                $costValue = $cat->products->sum(fn ($p) => $p->quantity * $p->purchase_price);
                $retailValue = $cat->products->sum(fn ($p) => $p->quantity * $p->selling_price);

                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'products_count' => $cat->products_count,
                    'total_units' => $totalUnits,
                    'cost_value' => $costValue,
                    'retail_value' => $retailValue,
                    'potential_profit' => $retailValue - $costValue,
                ];
            });
    }

    /**
     * Get Slow-Moving and Dead Stock.
     * Identifies products with no sales or no movement in the last $days.
     */
    public function getSlowMovingProducts(int $days = 30): Collection
    {
        $thresholdDate = Carbon::now()->subDays($days);

        // Get latest sale date per product
        $latestSales = SaleItem::select('product_id', DB::raw('MAX(created_at) as last_sold_at'))
            ->whereHas('sale', fn ($q) => $q->where('status', 'completed'))
            ->groupBy('product_id')
            ->pluck('last_sold_at', 'product_id');

        return Product::with(['category', 'unit'])
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->get()
            ->filter(function ($product) use ($latestSales, $thresholdDate) {
                $lastSold = $latestSales->get($product->id);

                if (! $lastSold) {
                    // Never sold: check if product was created before threshold date
                    return $product->created_at <= $thresholdDate;
                }

                return Carbon::parse($lastSold) <= $thresholdDate;
            })
            ->map(function ($product) use ($latestSales) {
                $lastSoldStr = $latestSales->get($product->id);
                $lastDate = $lastSoldStr ? Carbon::parse($lastSoldStr) : $product->created_at;
                $daysInactive = (int) $lastDate->diffInDays(Carbon::now());
                $tiedUpCapital = $product->quantity * $product->purchase_price;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category?->name ?? 'Uncategorized',
                    'quantity' => $product->quantity,
                    'unit' => $product->unit?->symbol ?? 'pcs',
                    'purchase_price' => $product->purchase_price,
                    'selling_price' => $product->selling_price,
                    'tied_up_capital' => $tiedUpCapital,
                    'last_activity_date' => $lastDate,
                    'days_inactive' => $daysInactive,
                    'never_sold' => ! $latestSales->has($product->id),
                ];
            })
            ->sortByDesc('days_inactive')
            ->values();
    }

    /**
     * Get Comprehensive Profit & Loss Statement for a Date Range.
     */
    public function getProfitAndLoss(Carbon $startDate, Carbon $endDate): array
    {
        // 1. Sales Revenue
        $salesQuery = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed');

        $salesCount = (int) $salesQuery->count();
        $grossSales = (float) $salesQuery->sum('subtotal');
        $totalDiscounts = (float) $salesQuery->sum('total_discount');
        $netSalesRevenue = (float) $salesQuery->sum('total');

        // 2. Cost of Goods Sold (COGS)
        $cogs = (float) SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate])
                  ->where('status', 'completed');
            })
            ->sum(DB::raw('quantity * cost_price'));

        // 3. Gross Profit
        $grossProfit = $netSalesRevenue - $cogs;
        $grossProfitMargin = $netSalesRevenue > 0 ? ($grossProfit / $netSalesRevenue) * 100 : 0;

        // 4. Operating Expenses (Excluding automatic product purchases, which are in COGS/Inventory)
        $expenseQuery = FinanceTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->whereHas('category', fn ($q) => $q->where('type', FinanceCategoryType::Expense))
            ->with('category');

        $expenses = $expenseQuery->get();

        // Categorized expenses
        $expensesByCategory = $expenses->groupBy('category.name')->map(function ($items, $catName) {
            return [
                'category' => $catName,
                'total' => (float) $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->values();

        // Separate operating expenses from purchase expenses to avoid double counting with COGS
        $purchaseExpense = (float) $expenses->filter(fn ($t) => $t->category?->name === 'Product Purchases')->sum('amount');
        $operatingExpense = (float) $expenses->filter(fn ($t) => $t->category?->name !== 'Product Purchases')->sum('amount');
        $totalExpenses = (float) $expenses->sum('amount');

        // 5. Estimated Net Profit = Gross Profit - Operating Expenses
        $estimatedNetProfit = $grossProfit - $operatingExpense;
        $netProfitMargin = $netSalesRevenue > 0 ? ($estimatedNetProfit / $netSalesRevenue) * 100 : 0;

        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'sales' => [
                'count' => $salesCount,
                'gross_sales' => $grossSales,
                'discounts' => $totalDiscounts,
                'net_revenue' => $netSalesRevenue,
            ],
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'gross_margin' => $grossProfitMargin,
            'operating_expenses' => $operatingExpense,
            'purchase_expenses' => $purchaseExpense,
            'total_expenses' => $totalExpenses,
            'expenses_by_category' => $expensesByCategory,
            'estimated_net_profit' => $estimatedNetProfit,
            'net_margin' => $netProfitMargin,
        ];
    }

    /**
     * Get Daily Sales Summary for a Date Range.
     */
    public function getDailySalesSummary(Carbon $startDate, Carbon $endDate): Collection
    {
        return Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(sale_date) as date, COUNT(*) as count, SUM(subtotal) as gross, SUM(total_discount) as discount, SUM(total) as net')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($row) {
                $avgOrder = $row->count > 0 ? $row->net / $row->count : 0;
                return [
                    'date' => $row->date,
                    'count' => (int) $row->count,
                    'gross' => (float) $row->gross,
                    'discount' => (float) $row->discount,
                    'net' => (float) $row->net,
                    'avg_order' => (float) $avgOrder,
                ];
            });
    }

    /**
     * Get Monthly Sales Summary for a given Year.
     */
    public function getMonthlySalesSummary(int $year): Collection
    {
        return Sale::whereYear('sale_date', $year)
            ->where('status', 'completed')
            ->selectRaw('STRFTIME("%m", sale_date) as month, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) use ($year) {
                $monthNum = (int) $row->month;
                $monthName = Carbon::createFromDate($year, $monthNum, 1)->format('F');
                return [
                    'month_number' => $monthNum,
                    'month_name' => $monthName,
                    'count' => (int) $row->count,
                    'revenue' => (float) $row->revenue,
                ];
            });
    }

    /**
     * Get Product Sales Performance for a Date Range.
     */
    public function getProductSalesSummary(Carbon $startDate, Carbon $endDate): Collection
    {
        return SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate])
                  ->where('status', 'completed');
            })
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(quantity * cost_price) as total_cost')
            )
            ->with('product.category', 'product.unit')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(function ($item) {
                $revenue = (float) $item->total_revenue;
                $cost = (float) $item->total_cost;
                $profit = $revenue - $cost;
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

                return [
                    'product_name' => $item->product?->name ?? 'Deleted Product',
                    'sku' => $item->product?->sku ?? '-',
                    'category' => $item->product?->category?->name ?? 'Uncategorized',
                    'unit' => $item->product?->unit?->symbol ?? 'pcs',
                    'quantity_sold' => (int) $item->total_qty,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'gross_profit' => $profit,
                    'margin_percent' => $margin,
                ];
            });
    }

    /**
     * Get Sales Performance grouped by Staff Member / Cashier.
     */
    public function getSalesByStaff(Carbon $startDate, Carbon $endDate): Collection
    {
        return Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('created_by', DB::raw('COUNT(*) as count, SUM(total) as revenue'))
            ->with('creator')
            ->groupBy('created_by')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($row) {
                $revenue = (float) $row->revenue;
                $count = (int) $row->count;
                $avgOrder = $count > 0 ? $revenue / $count : 0;

                return [
                    'staff_name' => $row->creator?->name ?? 'Unknown',
                    'username' => $row->creator?->username ?? '-',
                    'role' => $row->creator?->role?->label() ?? 'Staff',
                    'transactions_count' => $count,
                    'total_revenue' => $revenue,
                    'avg_order_value' => $avgOrder,
                ];
            });
    }
}

