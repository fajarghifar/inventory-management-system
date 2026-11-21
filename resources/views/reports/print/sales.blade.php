<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Performance Report - {{ $storeName }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        @media screen {
            body {
                background: #f1f5f9;
                padding: 24px 12px;
            }
            .page-container {
                max-width: 210mm;
                margin: 0 auto;
                background: #ffffff;
                padding: 14mm 16mm;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
            .screen-toolbar {
                max-width: 210mm;
                margin: 0 auto 16px auto;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                font-size: 13px;
                font-weight: 600;
                border-radius: 8px;
                cursor: pointer;
                text-decoration: none;
            }
            .btn-primary { background: #0C735B; color: #fff; border: 1px solid #0C735B; }
            .btn-secondary { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        }

        @media print {
            .screen-toolbar { display: none !important; }
            .page-container { width: 100% !important; padding: 0 !important; box-shadow: none !important; }
        }

        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #0C735B;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .doc-logo-area { display: flex; align-items: center; gap: 10px; }
        .doc-logo-icon {
            width: 36px; height: 36px; background: #0C735B; color: #ffffff;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900;
        }
        .doc-store-name { font-size: 15pt; font-weight: 800; color: #0f172a; line-height: 1.15; }
        .doc-store-subtitle { font-size: 8pt; color: #64748b; font-weight: 500; }
        .doc-meta { text-align: right; font-size: 8pt; color: #475569; }

        .doc-banner {
            background-color: #0C735B; color: #ffffff; padding: 9px 12px;
            border-radius: 6px; margin-bottom: 14px; display: flex;
            align-items: center; justify-content: space-between;
        }
        .doc-banner-title { font-size: 12pt; font-weight: 800; margin: 0; }
        .doc-banner-subtitle { font-size: 8pt; opacity: 0.9; margin-top: 1px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px; }
        .kpi-card { background: #F8FAF9; border: 1px solid #E2ECE9; border-radius: 6px; padding: 9px 11px; }
        .kpi-label { font-size: 7.5pt; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 2px; }
        .kpi-value { font-size: 12pt; font-weight: 900; font-family: monospace; color: #0f172a; }
        .kpi-explain { font-size: 7pt; color: #64748b; margin-top: 2px; }

        .section-title { font-size: 10pt; font-weight: 800; color: #0f172a; margin: 14px 0 6px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; }
        table.doc-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 14px; }
        table.doc-table thead { display: table-header-group; }
        table.doc-table tr { page-break-inside: avoid; }
        table.doc-table th { background: #f1f5f9; color: #334155; font-weight: 700; font-size: 7.5pt; text-transform: uppercase; padding: 6px; border-top: 1px solid #cbd5e1; border-bottom: 1.5px solid #94a3b8; text-align: left; }
        table.doc-table td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; }
        table.doc-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.doc-table tfoot td { font-weight: 800; background: #f8fafc; border-top: 1.5px solid #94a3b8; border-bottom: 2px solid #334155; padding: 6px; }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important; }
        .text-emerald { color: #0C735B !important; }
        .text-rose { color: #e11d48 !important; }

        .two-col-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }

        .trend-bars-box {
            background: #F8FAF9;
            border: 1px solid #E2ECE9;
            border-radius: 6px;
            padding: 10px;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 7.5pt;
            margin-bottom: 5px;
        }
        .bar-label { width: 70px; font-weight: 600; color: #475569; }
        .bar-track { flex: 1; height: 12px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
        .bar-fill { height: 100%; background: #0C735B; border-radius: 3px; }
        .bar-val { width: 75px; text-align: right; font-family: monospace; font-weight: 700; }

        .doc-footer { margin-top: 20px; border-top: 1px solid #cbd5e1; padding-top: 6px; display: flex; justify-content: space-between; font-size: 7.5pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <a href="{{ route('reports.sales', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn btn-secondary">&larr; Back to Sales Report</a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('reports.export.sales-csv', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn btn-secondary">Export CSV</a>
            <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
        </div>
    </div>

    <div class="page-container">
        {{-- Header matching Image 3 bottom-right --}}
        <header class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-logo-icon">ET</div>
                <div>
                    <div class="doc-store-name">{{ $storeName }}</div>
                    <div class="doc-store-subtitle">Retail POS & Sales Management · Currency: Ethiopian Birr (ETB)</div>
                </div>
            </div>
            <div class="doc-meta">
                <div>Report Generated: <strong>{{ now()->format('M d, Y · h:i A') }}</strong></div>
                <div>Period: <strong>{{ $startDate->format('M d, Y') }} &ndash; {{ $endDate->format('M d, Y') }}</strong></div>
            </div>
        </header>

        {{-- Emerald Banner matching Image 3 --}}
        <div class="doc-banner">
            <div>
                <h1 class="doc-banner-title">Daily Sales Summary</h1>
                <div class="doc-banner-subtitle">Day-by-day sales, customer discounts, and net cashier income</div>
            </div>
            <div style="font-size: 8pt; font-weight: 700; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 4px;">
                CONFIRMED SALES
            </div>
        </div>

        {{-- 4 Summary Cards matching Image 3 --}}
        @php
            $totalGross = $dailySummary->sum('gross');
            $totalDiscount = $dailySummary->sum('discount');
            $totalNet = $totalRevenue;
        @endphp
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Gross Sales</div>
                <div class="kpi-value">ETB {{ number_format($totalGross, 2) }}</div>
                <div class="kpi-explain">Full price before discounts</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Completed Transactions</div>
                <div class="kpi-value">{{ number_format($totalTransactions) }}</div>
                <div class="kpi-explain">Total customer orders served</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Customer Discounts</div>
                <div class="kpi-value text-rose">ETB {{ number_format($totalDiscount, 2) }}</div>
                <div class="kpi-explain">Promotional price reductions</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Net Sales Received</div>
                <div class="kpi-value text-emerald">ETB {{ number_format($totalNet, 2) }}</div>
                <div class="kpi-explain">Actual money received in cash & transfer</div>
            </div>
        </div>

        {{-- Sales Trend & Top Products Grid matching Image 3 --}}
        <div class="two-col-grid">
            <div>
                <div class="section-title" style="margin-top: 0;">Sales Trend</div>
                <div class="trend-bars-box">
                    @php
                        $maxDayVal = max(1, $dailySummary->take(7)->max('net') ?? 1);
                    @endphp
                    @forelse($dailySummary->take(7) as $day)
                        @php
                            $pct = min(100, round(($day['net'] / $maxDayVal) * 100));
                        @endphp
                        <div class="bar-row">
                            <span class="bar-label">{{ Carbon\Carbon::parse($day['date'])->format('D, M d') }}</span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="bar-val font-mono">ETB {{ number_format($day['net'], 0) }}</span>
                        </div>
                    @empty
                        <div style="font-size: 8pt; color: #94a3b8; text-align: center; padding: 8px;">No daily activity recorded.</div>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="section-title" style="margin-top: 0;">Top Selling Products</div>
                <div class="trend-bars-box">
                    @forelse($productSummary->take(5) as $idx => $prod)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; font-size: 8pt;">
                            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;">
                                <strong>{{ $idx + 1 }}.</strong> {{ $prod['product_name'] }}
                            </div>
                            <div class="font-mono text-emerald" style="font-weight: 700;">
                                {{ $prod['quantity_sold'] }} {{ $prod['unit'] }}
                            </div>
                        </div>
                    @empty
                        <div style="font-size: 8pt; color: #94a3b8; text-align: center; padding: 8px;">No products sold.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Daily Summary Table --}}
        <div class="section-title">Daily Sales Activity Breakdown</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-center">Orders</th>
                    <th class="text-right">Gross Sales</th>
                    <th class="text-right">Discounts</th>
                    <th class="text-right">Net Revenue</th>
                    <th class="text-right">Average Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailySummary as $day)
                    <tr>
                        <td class="font-bold">{{ Carbon\Carbon::parse($day['date'])->format('D, M d, Y') }}</td>
                        <td class="text-center font-mono">{{ $day['count'] }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($day['gross'], 2) }}</td>
                        <td class="text-right font-mono text-rose">@if($day['discount'] > 0)-ETB {{ number_format($day['discount'], 2) }}@else-@endif</td>
                        <td class="text-right font-mono font-bold text-emerald">ETB {{ number_format($day['net'], 2) }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($day['avg_order'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Period Total</td>
                    <td class="text-center font-mono">{{ $totalTransactions }}</td>
                    <td class="text-right font-mono">ETB {{ number_format($totalGross, 2) }}</td>
                    <td class="text-right font-mono text-rose">-ETB {{ number_format($totalDiscount, 2) }}</td>
                    <td class="text-right font-mono text-emerald">ETB {{ number_format($totalNet, 2) }}</td>
                    <td class="text-right font-mono">ETB {{ number_format($totalTransactions > 0 ? $totalNet / $totalTransactions : 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Cashier Performance --}}
        @if($staffSummary->isNotEmpty())
            <div class="section-title">Cashier / Staff Member Performance</div>
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>Cashier Name</th>
                        <th>Role</th>
                        <th class="text-center">Orders Handled</th>
                        <th class="text-right">Total Net Revenue</th>
                        <th class="text-right">Average Order</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffSummary as $staff)
                        <tr>
                            <td class="font-bold">{{ $staff['staff_name'] }}</td>
                            <td>{{ $staff['role'] }}</td>
                            <td class="text-center font-mono">{{ $staff['transactions_count'] }}</td>
                            <td class="text-right font-mono font-bold text-emerald">ETB {{ number_format($staff['total_revenue'], 2) }}</td>
                            <td class="text-right font-mono">ETB {{ number_format($staff['avg_order_value'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <footer class="doc-footer">
            <div>Generated by {{ $storeName }} System · Sales Performance Statement</div>
            <div>Page 1 of 1</div>
        </footer>
    </div>
</body>
</html>
