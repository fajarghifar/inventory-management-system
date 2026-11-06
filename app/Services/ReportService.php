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

