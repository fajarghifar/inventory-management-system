<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Statement - {{ $storeName }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 14mm;
        }
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

        .pnl-section { margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
        .pnl-header { background: #f8fafc; font-weight: 800; font-size: 9pt; text-transform: uppercase; padding: 8px 12px; border-bottom: 1px solid #e2e8f0; color: #0f172a; display: flex; justify-content: space-between; }
        .pnl-row { display: flex; justify-content: space-between; padding: 6px 12px; border-bottom: 1px solid #f1f5f9; font-size: 9pt; }
        .pnl-row:last-child { border-bottom: none; }
        .pnl-total { display: flex; justify-content: space-between; padding: 8px 12px; background: #f8fafc; font-weight: 800; border-top: 1.5px solid #cbd5e1; font-size: 9.5pt; }

        .font-mono { font-family: ui-monospace, SFMono-Regular, monospace; }
        .text-emerald { color: #0C735B !important; }
        .text-rose { color: #e11d48 !important; }
        .highlight-box { background: #ECFDF5; border: 1.5px solid #A7F3D0; padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
        .doc-footer { margin-top: 24px; border-top: 1px solid #cbd5e1; padding-top: 8px; display: flex; justify-content: space-between; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <a href="{{ route('reports.profit-loss', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn btn-secondary">&larr; Back to Statement</a>
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
    </div>

    <div class="page-container">
        <header class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-logo-icon">ET</div>
                <div>
                    <div class="doc-store-name">{{ $storeName }}</div>
                    <div class="doc-store-subtitle">Financial Performance Statement · Currency: Ethiopian Birr (ETB)</div>
                </div>
            </div>
            <div class="doc-meta">
                <div>Report Generated: <strong>{{ now()->format('M d, Y · h:i A') }}</strong></div>
                <div>Statement Period: <strong>{{ $startDate->format('M d, Y') }} &ndash; {{ $endDate->format('M d, Y') }}</strong></div>
            </div>
        </header>

        <div class="doc-banner">
            <div>
                <h1 class="doc-banner-title">Statement of Profit & Loss</h1>
                <div class="doc-banner-subtitle">Official summary of sales income, product costs, operating expenses, and estimated net profit</div>
            </div>
            <div style="font-size: 8pt; font-weight: 700; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 4px;">
                FINANCIAL STATEMENT
            </div>
        </div>

        {{-- 1. Sales Income --}}
        <div class="pnl-section">
            <div class="pnl-header">
                <span>1. Operating Revenue (Sales Income)</span>
                <span>Amount</span>
            </div>
            <div class="pnl-row">
                <span>Gross Customer Sales ({{ $pnl['sales']['count'] }} completed orders)</span>
                <span class="font-mono">ETB {{ number_format($pnl['sales']['gross_sales'], 2) }}</span>
            </div>
            <div class="pnl-row text-rose">
                <span>Less: Sales Discounts & Allowances</span>
                <span class="font-mono">- ETB {{ number_format($pnl['sales']['discounts'], 2) }}</span>
            </div>
            <div class="pnl-total">
                <span>Net Sales Revenue</span>
                <span class="font-mono text-emerald">ETB {{ number_format($pnl['sales']['net_revenue'], 2) }}</span>
            </div>
        </div>

        {{-- 2. Cost of Goods Sold --}}
        <div class="pnl-section">
            <div class="pnl-header">
                <span>2. Cost of Products Sold (Cost of Goods Sold - COGS)</span>
                <span>Amount</span>
            </div>
            <div class="pnl-row">
                <span>Direct Supplier Purchase Cost of Sold Inventory</span>
                <span class="font-mono text-rose">ETB {{ number_format($pnl['cogs'], 2) }}</span>
            </div>
            <div class="pnl-total">
                <span>Total Cost of Products Sold</span>
                <span class="font-mono text-rose">ETB {{ number_format($pnl['cogs'], 2) }}</span>
            </div>
        </div>

        {{-- 3. Gross Profit Banner --}}
        <div class="highlight-box">
            <div>
                <div style="font-size: 8pt; font-weight: 700; text-transform: uppercase; color: #065F46;">Money Earned After Product Costs</div>
                <div style="font-size: 14pt; font-weight: 900; color: #0C735B;">Gross Business Margin</div>
                <div style="font-size: 8pt; color: #047857;">{{ number_format($pnl['gross_margin'], 1) }}% gross profit margin</div>
            </div>
            <div class="font-mono text-emerald" style="font-size: 16pt; font-weight: 900;">
                ETB {{ number_format($pnl['gross_profit'], 2) }}
            </div>
        </div>

        {{-- 4. Operating Expenses --}}
        <div class="pnl-section">
            <div class="pnl-header">
                <span>3. Operating & Administrative Expenses</span>
                <span>Amount</span>
            </div>
            @forelse($pnl['expenses_by_category'] as $exp)
                @if($exp['category'] !== 'Product Purchases')
                    <div class="pnl-row">
                        <span>{{ $exp['category'] }} ({{ $exp['count'] }} entries)</span>
                        <span class="font-mono text-rose">ETB {{ number_format($exp['total'], 2) }}</span>
                    </div>
                @endif
            @empty
                <div class="pnl-row" style="color: #94a3b8;">
                    <span>No operating expenses recorded for this timeframe</span>
                    <span class="font-mono">ETB 0.00</span>
                </div>
            @endforelse
            <div class="pnl-total">
                <span>Total Operating Expenses</span>
                <span class="font-mono text-rose">ETB {{ number_format($pnl['operating_expenses'], 2) }}</span>
            </div>
        </div>

        {{-- 5. Net Profit Result --}}
        <div class="highlight-box" style="background: {{ $pnl['estimated_net_profit'] >= 0 ? '#ECFDF5' : '#FFF1F2' }}; border-color: {{ $pnl['estimated_net_profit'] >= 0 ? '#10B981' : '#F43F5E' }};">
            <div>
                <div style="font-size: 8pt; font-weight: 700; text-transform: uppercase; color: {{ $pnl['estimated_net_profit'] >= 0 ? '#065F46' : '#9F1239' }};">
                    Final Business Performance
                </div>
                <div style="font-size: 14pt; font-weight: 900; color: {{ $pnl['estimated_net_profit'] >= 0 ? '#0C735B' : '#E11D48' }};">
                    Estimated Net Business Profit
                </div>
                <div style="font-size: 8pt; color: #64748b;">
                    {{ number_format($pnl['net_margin'], 1) }}% net margin on sales revenue
                </div>
            </div>
            <div class="font-mono" style="font-size: 18pt; font-weight: 900; color: {{ $pnl['estimated_net_profit'] >= 0 ? '#0C735B' : '#E11D48' }};">
                ETB {{ number_format($pnl['estimated_net_profit'], 2) }}
            </div>
        </div>

        <footer class="doc-footer">
            <div>Generated by {{ $storeName }} System · Profit & Loss Financial Statement</div>
            <div>Page 1 of 1</div>
        </footer>
    </div>
</body>
</html>
