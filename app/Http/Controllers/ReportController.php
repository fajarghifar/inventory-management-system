<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Setting;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Reports Hub Index.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $overview = $this->reportService->getReportsHubOverview($period);
        $storeName = Setting::get('store_name', 'My Store');

        return view('reports.index', compact('overview', 'storeName', 'period'));
    }

    /**
     * Stock Valuation & Category Breakdown.
     */
    public function stockValuation()
    {
        $valuation = $this->reportService->getStockValuationSummary();
        $byCategory = $this->reportService->getStockByCategory();
        $storeName = Setting::get('store_name', 'My Store');

        return view('reports.stock-valuation', compact('valuation', 'byCategory', 'storeName'));
    }

    /**
     * Slow-Moving and Dead Stock Monitor.
     */
    public function slowMoving(Request $request)
    {
        $days = (int) $request->input('days', 30);
        if ($days <= 0) {
            $days = 30;
        }

        $slowMoving = $this->reportService->getSlowMovingProducts($days);
        $totalCapital = $slowMoving->sum('tied_up_capital');
        $totalCount = $slowMoving->count();
        $storeName = Setting::get('store_name', 'My Store');

        return view('reports.slow-moving', compact('slowMoving', 'days', 'totalCapital', 'totalCount', 'storeName'));
    }

    /**
     * Sales & Staff Performance Reports.
     */
    public function sales(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request);

        $dailySummary = $this->reportService->getDailySalesSummary($startDate, $endDate);
        $productSummary = $this->reportService->getProductSalesSummary($startDate, $endDate);
        $staffSummary = $this->reportService->getSalesByStaff($startDate, $endDate);
        $storeName = Setting::get('store_name', 'My Store');

        $totalRevenue = $dailySummary->sum('net');
        $totalTransactions = $dailySummary->sum('count');

        return view('reports.sales', compact(
            'startDate',
            'endDate',
            'dailySummary',
            'productSummary',
            'staffSummary',
            'totalRevenue',
            'totalTransactions',
            'storeName'
        ));
    }

    /**
     * Comprehensive Profit & Loss (P&L) Statement.
     */
    public function profitLoss(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request);

        $pnl = $this->reportService->getProfitAndLoss($startDate, $endDate);
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');

        return view('reports.profit-loss', compact('pnl', 'startDate', 'endDate', 'storeName', 'storeAddress', 'storePhone'));
    }

    /**
     * Helper to parse date range from request.
     */
    protected function parseDateRange(Request $request): array
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        return [$startDate, $endDate];
    }

    /**
     * Dedicated Printable / PDF Layout: Reports Overview.
     */
    public function printOverview(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $overview = $this->reportService->getReportsHubOverview($period);
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');

        return view('reports.print.overview', compact('overview', 'storeName', 'storeAddress', 'storePhone', 'period'));
    }

    /**
     * Dedicated Printable / PDF Layout: Stock Valuation.
     */
    public function printStockValuation()
    {
        $valuation = $this->reportService->getStockValuationSummary();
        $byCategory = $this->reportService->getStockByCategory();
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');

        return view('reports.print.stock-valuation', compact('valuation', 'byCategory', 'storeName', 'storeAddress', 'storePhone'));
    }

    /**
     * Dedicated Printable / PDF Layout: Daily Sales & Margins.
     */
    public function printSales(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request);

        $dailySummary = $this->reportService->getDailySalesSummary($startDate, $endDate);
        $productSummary = $this->reportService->getProductSalesSummary($startDate, $endDate);
        $staffSummary = $this->reportService->getSalesByStaff($startDate, $endDate);
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');

        $totalRevenue = $dailySummary->sum('net');
        $totalTransactions = $dailySummary->sum('count');

        return view('reports.print.sales', compact(
            'startDate',
            'endDate',
            'dailySummary',
            'productSummary',
            'staffSummary',
            'totalRevenue',
            'totalTransactions',
            'storeName',
            'storeAddress',
            'storePhone'
        ));
    }

    /**
     * Dedicated Printable / PDF Layout: Profit & Loss Statement.
     */
    public function printProfitLoss(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request);

        $pnl = $this->reportService->getProfitAndLoss($startDate, $endDate);
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');

        return view('reports.print.profit-loss', compact('pnl', 'startDate', 'endDate', 'storeName', 'storeAddress', 'storePhone'));
    }

    /**
     * Dedicated Printable / PDF Layout: Slow-Moving Stock.
     */
    public function printSlowMoving(Request $request)
    {
        $days = (int) $request->input('days', 30);
        if ($days <= 0) {
            $days = 30;
        }

        $slowMoving = $this->reportService->getSlowMovingProducts($days);
        $totalCapital = $slowMoving->sum('tied_up_capital');
        $totalCount = $slowMoving->count();
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');

        return view('reports.print.slow-moving', compact('slowMoving', 'days', 'totalCapital', 'totalCount', 'storeName', 'storeAddress', 'storePhone'));
    }

    /**
     * Export Stock Valuation to CSV.
     */
    public function exportStockValuationCsv()
    {
        $valuation = $this->reportService->getStockValuationSummary();
        $fileName = 'stock-valuation-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($valuation) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Quantity on Hand', 'Unit', 'Purchase Price (ETB)', 'Selling Price (ETB)', 'Total Cost Valuation (ETB)', 'Potential Selling Value (ETB)', 'Potential Profit (ETB)']);

            foreach ($valuation['products'] as $product) {
                $costVal = $product->quantity * $product->purchase_price;
                $retailVal = $product->quantity * $product->selling_price;
                fputcsv($handle, [
                    $product->name,
                    $product->sku,
                    $product->category?->name ?? 'Uncategorized',
                    $product->quantity,
                    $product->unit?->symbol ?? 'pcs',
                    number_format($product->purchase_price, 2, '.', ''),
                    number_format($product->selling_price, 2, '.', ''),
                    number_format($costVal, 2, '.', ''),
                    number_format($retailVal, 2, '.', ''),
                    number_format($retailVal - $costVal, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Export Sales Summary to CSV.
     */
    public function exportSalesCsv(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request);
        $dailySummary = $this->reportService->getDailySalesSummary($startDate, $endDate);
        $fileName = 'sales-summary-' . $startDate->format('Ymd') . '-to-' . $endDate->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($dailySummary) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Completed Orders', 'Gross Sales (ETB)', 'Total Discounts (ETB)', 'Net Sales Revenue (ETB)', 'Average Order (ETB)']);

            foreach ($dailySummary as $day) {
                fputcsv($handle, [
                    $day['date'],
                    $day['count'],
                    number_format($day['gross'], 2, '.', ''),
                    number_format($day['discount'], 2, '.', ''),
                    number_format($day['net'], 2, '.', ''),
                    number_format($day['avg_order'], 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Export Slow-Moving Stock to CSV.
     */
    public function exportSlowMovingCsv(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $slowMoving = $this->reportService->getSlowMovingProducts($days);
        $fileName = 'slow-moving-stock-' . $days . 'days-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($slowMoving) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Quantity in Stock', 'Unit', 'Purchase Price (ETB)', 'Tied-Up Capital (ETB)', 'Last Activity Date', 'Days Inactive', 'Never Sold']);

            foreach ($slowMoving as $item) {
                fputcsv($handle, [
                    $item['name'],
                    $item['sku'],
                    $item['category'],
                    $item['quantity'],
                    $item['unit'],
                    number_format($item['purchase_price'], 2, '.', ''),
                    number_format($item['tied_up_capital'], 2, '.', ''),
                    Carbon::parse($item['last_activity_date'])->format('Y-m-d'),
                    $item['days_inactive'],
                    $item['never_sold'] ? 'Yes' : 'No',
                ]);
            }
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}

