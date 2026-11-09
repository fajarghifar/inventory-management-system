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
    public function index()
    {
        return view('reports.index');
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
}

