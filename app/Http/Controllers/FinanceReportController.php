<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinanceTransaction;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use App\Enums\FinanceCategoryType;

class FinanceReportController extends Controller
{
    public function print(Request $request, string $printId)
    {
        // Retrieve selected IDs from Cache
        $selectedIds = \Illuminate\Support\Facades\Cache::get("finance_print_{$printId}");

        if (!$selectedIds) {
            abort(404, 'Print session expired or invalid. Please select transactions and try again.');
        }

        // Fetch transactions ordered by date
        $cashFlows = FinanceTransaction::with(['category', 'creator'])
            ->whereIn('id', $selectedIds)
            ->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        if ($cashFlows->isEmpty()) {
             abort(404, 'No transactions found');
        }

        // Calculate Totals within the selection
        $totalIncome = $cashFlows->where('category.type', FinanceCategoryType::Income)->sum('amount');
        $totalExpense = $cashFlows->where('category.type', FinanceCategoryType::Expense)->sum('amount');

        // Estimate Opening Balance
        // Logic: specific business requirement usually needs a running balance.
        // For now, we will assume 0 or calculate based on previous transactions if possible.
        // Given the request didn't specify strict accounting period closing, we'll try to calculate
        // the sum of ALL transactions prior to the first date of the selection.

        $firstDate = $cashFlows->first()->transaction_date;

        // Get Opening Balance from Settings
        $settingOpeningBalance = (float) Setting::get('opening_balance_amount', 0);
        $settingOpeningDate = Setting::get('opening_balance_date'); // Format Y-m-d

        // Calculate flows between Setting Date and Report Start Date
        $prevQuery = FinanceTransaction::query()
            ->where('transaction_date', '<', $firstDate);

        if ($settingOpeningDate) {
            $prevQuery->where('transaction_date', '>=', $settingOpeningDate);
        }

        $prevIncome = (clone $prevQuery)
            ->whereHas('category', fn($q) => $q->where('type', FinanceCategoryType::Income))
            ->sum('amount');

        $prevExpense = (clone $prevQuery)
            ->whereHas('category', fn($q) => $q->where('type', FinanceCategoryType::Expense))
            ->sum('amount');

        $openingBalanceAmount = $settingOpeningBalance + $prevIncome - $prevExpense;
        $openingBalanceDate = $firstDate; // The balance AS OF this date

        $estimatedFinalBalance = $openingBalanceAmount + $totalIncome - $totalExpense;

        // Settings for Store Info
        $storeName = Setting::get('store_name', 'My Store');
        $storeAddress = Setting::get('store_address', 'Store Address');
        $storePhone = Setting::get('store_phone', '-');

        // Determine Period Label
        $periodKey = $request->input('period');
        $periodText = '';

        if ($periodKey) {
            $now = Carbon::now();
            switch ($periodKey) {
                case 'today':
                    $periodText = 'Today (' . $now->translatedFormat('d F Y') . ')';
                    break;
                case 'yesterday':
                    $periodText = 'Yesterday (' . $now->subDay()->translatedFormat('d F Y') . ')';
                    break;
                case 'this_week':
                    $periodText = 'This Week (' . $now->startOfWeek()->translatedFormat('d M') . ' - ' . $now->endOfWeek()->translatedFormat('d M Y') . ')';
                    break;
                case 'last_week':
                    $periodText = 'Last Week (' . $now->subWeek()->startOfWeek()->translatedFormat('d M') . ' - ' . $now->subWeek()->endOfWeek()->translatedFormat('d M Y') . ')';
                    break;
                case 'this_month':
                    $periodText = 'This Month (' . $now->translatedFormat('F Y') . ')';
                    break;
                case 'last_month':
                    $periodText = 'Last Month (' . $now->subMonth()->translatedFormat('F Y') . ')';
                    break;
            }
        }

        // Fallback if no period filter or unknown
        if (empty($periodText)) {
            $start = $cashFlows->first()->transaction_date;
            $end = $cashFlows->last()->transaction_date;

            if ($start->isSameDay($end)) {
                $periodText = $start->translatedFormat('d F Y');
            } else {
                $periodText = $start->translatedFormat('d M Y') . ' — ' . $end->translatedFormat('d M Y');
            }
        }

        return view('finance.reports.print', compact(
            'cashFlows',
            'totalIncome',
            'totalExpense',
            'openingBalanceAmount',
            'openingBalanceDate',
            'estimatedFinalBalance',
            'storeName',
            'storeAddress',
            'storePhone',
            'periodText'
        ));
    }
}
