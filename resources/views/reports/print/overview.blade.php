<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Performance Overview - {{ $storeName }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm 14mm; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 9.5pt; line-height: 1.4; color: #1e293b; background: #fff; margin: 0; padding: 0; }
        @media screen {
            body { background: #f1f5f9; padding: 24px 12px; }
            .page-container { max-width: 210mm; margin: 0 auto; background: #fff; padding: 16mm 18mm; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border-radius: 8px; }
            .screen-toolbar { max-width: 210mm; margin: 0 auto 16px auto; display: flex; justify-content: space-between; }
            .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px; font-weight: 600; border-radius: 8px; text-decoration: none; cursor: pointer; }
            .btn-primary { background: #0C735B; color: #fff; border: 1px solid #0C735B; }
            .btn-secondary { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        }
        @media print { .screen-toolbar { display: none !important; } .page-container { width: 100% !important; padding: 0 !important; box-shadow: none !important; } }
        .doc-header { display: flex; justify-content: space-between; border-bottom: 2px solid #0C735B; padding-bottom: 12px; margin-bottom: 14px; }
        .doc-logo-area { display: flex; align-items: center; gap: 10px; }
        .doc-logo-icon { width: 38px; height: 38px; background: #0C735B; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900; }
        .doc-store-name { font-size: 16pt; font-weight: 800; color: #0f172a; line-height: 1.15; }
        .doc-store-subtitle { font-size: 8.5pt; color: #64748b; }
        .doc-meta { text-align: right; font-size: 8.5pt; color: #475569; }
        .doc-banner { background-color: #0C735B; color: #fff; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
        .doc-banner-title { font-size: 13pt; font-weight: 800; margin: 0; }
        .doc-banner-subtitle { font-size: 8.5pt; opacity: 0.9; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 18px; }
        .kpi-card { background: #F8FAF9; border: 1px solid #E2ECE9; border-radius: 6px; padding: 10px 12px; }
        .kpi-label { font-size: 7.5pt; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 3px; }
        .kpi-value { font-size: 13pt; font-weight: 900; font-family: monospace; color: #0f172a; }
        .kpi-explain { font-size: 7.5pt; color: #64748b; margin-top: 3px; }

        .section-title { font-size: 10.5pt; font-weight: 800; color: #0f172a; margin: 16px 0 8px 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        table.doc-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 16px; }
        table.doc-table thead { display: table-header-group; }
        table.doc-table tr { page-break-inside: avoid; }
        table.doc-table th { background: #f1f5f9; color: #334155; font-weight: 700; font-size: 7.5pt; text-transform: uppercase; padding: 7px 8px; border-top: 1px solid #cbd5e1; border-bottom: 1.5px solid #94a3b8; text-align: left; }
        table.doc-table td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        table.doc-table tbody tr:nth-child(even) { background-color: #f8fafc; }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, monospace; }
        .font-bold { font-weight: 700; }
        .text-emerald { color: #0C735B !important; }
        .doc-footer { margin-top: 24px; border-top: 1px solid #cbd5e1; padding-top: 8px; display: flex; justify-content: space-between; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <a href="{{ route('reports.index', ['period' => $period]) }}" class="btn btn-secondary">&larr; Back to Reports Hub</a>
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
    </div>

    <div class="page-container">
        <header class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-logo-icon">ET</div>
                <div>
                    <div class="doc-store-name">{{ $storeName }}</div>
                    <div class="doc-store-subtitle">Executive Business Performance Overview · Currency: Ethiopian Birr (ETB)</div>
                </div>
            </div>
            <div class="doc-meta">
                <div>Report Generated: <strong>{{ now()->format('M d, Y · h:i A') }}</strong></div>
                <div>Reporting Period: <strong>{{ $overview['period_label'] }}</strong></div>
            </div>
        </header>

        <div class="doc-banner">
            <div>
                <h1 class="doc-banner-title">Business Overview Summary</h1>
                <div class="doc-banner-subtitle">Consolidated operational metrics for sales, stock holding, and profitability</div>
            </div>
            <div style="font-size: 8pt; font-weight: 700; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 4px;">
                EXECUTIVE SUMMARY
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Sales Revenue ({{ $overview['period_label'] }})</div>
                <div class="kpi-value text-emerald">ETB {{ number_format($overview['total_sales'], 2) }}</div>
                <div class="kpi-explain">{{ $overview['total_transactions'] }} completed customer sales</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Active Catalog Products</div>
                <div class="kpi-value">{{ number_format($overview['stock_valuation']['total_items']) }}</div>
                <div class="kpi-explain">{{ number_format($overview['stock_valuation']['total_units']) }} total physical units in stock</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Low Stock Alerts</div>
                <div class="kpi-value" style="color: {{ $overview['low_stock_count'] > 0 ? '#d97706' : '#0f172a' }};">
                    {{ number_format($overview['low_stock_count']) }}
                </div>
                <div class="kpi-explain">Products needing reorder soon</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Estimated Business Profit</div>
                <div class="kpi-value text-emerald">ETB {{ number_format($overview['estimated_profit'], 2) }}</div>
                <div class="kpi-explain">After direct product costs and operating expenses</div>
            </div>
        </div>

        <div class="section-title">Best Selling Products ({{ $overview['period_label'] }})</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th class="text-center">Units Sold</th>
                    <th class="text-right">Total Net Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overview['top_products'] as $item)
                    <tr>
                        <td class="font-bold">{{ $item['name'] }}</td>
                        <td class="text-center font-mono font-bold">{{ $item['qty'] }} {{ $item['unit'] }}</td>
                        <td class="text-right font-mono text-emerald">ETB {{ number_format($item['revenue'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding: 10px; color: #64748b;">No products sold during this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Recent Transactions</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Date & Time</th>
                    <th>Cashier</th>
                    <th class="text-right">Order Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overview['recent_sales'] as $sale)
                    <tr>
                        <td class="font-mono font-bold">{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->sale_date ? $sale->sale_date->format('M d, Y · h:i A') : '-' }}</td>
                        <td>{{ $sale->creator?->name ?? 'Staff' }}</td>
                        <td class="text-right font-mono font-bold text-emerald">ETB {{ number_format($sale->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <footer class="doc-footer">
            <div>Generated by {{ $storeName }} System · Overview Report</div>
            <div>Page 1 of 1</div>
        </footer>
    </div>
</body>
</html>
