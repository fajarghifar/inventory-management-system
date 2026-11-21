<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slow-Moving Stock Report - {{ $storeName }}</title>
    <style>
        @page { size: A4 portrait; margin: 10mm 12mm; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 9pt; line-height: 1.35; color: #1e293b; background: #fff; margin: 0; padding: 0; }
        @media screen {
            body { background: #f1f5f9; padding: 24px 12px; }
            .page-container { max-width: 210mm; margin: 0 auto; background: #fff; padding: 14mm 16mm; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); border-radius: 8px; }
            .screen-toolbar { max-width: 210mm; margin: 0 auto 16px auto; display: flex; justify-content: space-between; }
            .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px; font-weight: 600; border-radius: 8px; text-decoration: none; cursor: pointer; }
            .btn-primary { background: #0C735B; color: #fff; border: 1px solid #0C735B; }
            .btn-secondary { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        }
        @media print { .screen-toolbar { display: none !important; } .page-container { width: 100% !important; padding: 0 !important; box-shadow: none !important; } }
        .doc-header { display: flex; justify-content: space-between; border-bottom: 2px solid #0C735B; padding-bottom: 10px; margin-bottom: 12px; }
        .doc-logo-area { display: flex; align-items: center; gap: 10px; }
        .doc-logo-icon { width: 36px; height: 36px; background: #0C735B; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 900; }
        .doc-store-name { font-size: 15pt; font-weight: 800; color: #0f172a; line-height: 1.15; }
        .doc-store-subtitle { font-size: 8pt; color: #64748b; }
        .doc-meta { text-align: right; font-size: 8pt; color: #475569; }
        .doc-banner { background-color: #0C735B; color: #fff; padding: 9px 12px; border-radius: 6px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; }
        .doc-banner-title { font-size: 12pt; font-weight: 800; margin: 0; }
        .doc-banner-subtitle { font-size: 8pt; opacity: 0.9; }

        .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px; }
        .kpi-card { background: #F8FAF9; border: 1px solid #E2ECE9; border-radius: 6px; padding: 9px 11px; }
        .kpi-label { font-size: 7.5pt; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 2px; }
        .kpi-value { font-size: 13pt; font-weight: 900; font-family: monospace; color: #0f172a; }
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
        .font-mono { font-family: ui-monospace, SFMono-Regular, monospace !important; }
        .text-rose { color: #e11d48 !important; }
        .doc-footer { margin-top: 20px; border-top: 1px solid #cbd5e1; padding-top: 6px; display: flex; justify-content: space-between; font-size: 7.5pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <a href="{{ route('reports.slow-moving', ['days' => $days]) }}" class="btn btn-secondary">&larr; Back to Report</a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('reports.export.slow-moving-csv', ['days' => $days]) }}" class="btn btn-secondary">Export CSV</a>
            <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
        </div>
    </div>

    <div class="page-container">
        <header class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-logo-icon">ET</div>
                <div>
                    <div class="doc-store-name">{{ $storeName }}</div>
                    <div class="doc-store-subtitle">Inventory Audit & Capital Analysis · Currency: Ethiopian Birr (ETB)</div>
                </div>
            </div>
            <div class="doc-meta">
                <div>Report Generated: <strong>{{ now()->format('M d, Y · h:i A') }}</strong></div>
                <div>Threshold: <strong>No sales in {{ $days }}+ days</strong></div>
            </div>
        </header>

        <div class="doc-banner">
            <div>
                <h1 class="doc-banner-title">Slow-Moving and Unsold Stock Report</h1>
                <div class="doc-banner-subtitle">Products tying up capital without generating recent sales</div>
            </div>
            <div style="font-size: 8pt; font-weight: 700; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 4px;">
                INVENTORY RISK AUDIT
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Slow-Moving Products</div>
                <div class="kpi-value text-rose">{{ number_format($totalCount) }}</div>
                <div class="kpi-explain">Products inactive for &ge; {{ $days }} days</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Tied-Up Working Capital</div>
                <div class="kpi-value text-rose">ETB {{ number_format($totalCapital, 2) }}</div>
                <div class="kpi-explain">Money invested in dormant stock</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Dormant Units</div>
                <div class="kpi-value">{{ number_format($slowMoving->sum('quantity')) }}</div>
                <div class="kpi-explain">Physical units occupying shelf space</div>
            </div>
        </div>

        <div class="section-title">Inactive Products Breakdown</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-center">Units in Stock</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Tied-Up Capital</th>
                    <th class="text-center">Days Inactive</th>
                </tr>
            </thead>
            <tbody>
                @forelse($slowMoving as $item)
                    <tr>
                        <td class="font-bold">{{ $item['name'] }}</td>
                        <td class="font-mono text-slate">{{ $item['sku'] ?: '-' }}</td>
                        <td>{{ $item['category'] }}</td>
                        <td class="text-center font-mono font-bold">{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($item['purchase_price'], 2) }}</td>
                        <td class="text-right font-mono font-bold text-rose">ETB {{ number_format($item['tied_up_capital'], 2) }}</td>
                        <td class="text-center font-mono font-bold text-rose">{{ $item['days_inactive'] }} days</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 12px; color: #64748b;">Excellent! No slow-moving products found for this threshold.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total Tied-Up Capital</td>
                    <td class="text-center font-mono">{{ number_format($slowMoving->sum('quantity')) }}</td>
                    <td></td>
                    <td class="text-right font-mono text-rose">ETB {{ number_format($totalCapital, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <footer class="doc-footer">
            <div>Generated by {{ $storeName }} System · Slow Moving Inventory Audit</div>
            <div>Page 1 of 1</div>
        </footer>
    </div>
</body>
</html>
