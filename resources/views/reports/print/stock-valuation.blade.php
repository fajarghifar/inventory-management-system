@php
    $pageOrientation = 'landscape';
    $title = 'Stock Valuation Report';
    $periodLabel = 'As of ' . now()->format('M d, Y');
    $backUrl = route('reports.stock-valuation');
@endphp

@include('reports.print.layout', ['slot' => ''])

{{-- Re-include using component or custom layout wrapper --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Valuation Report - {{ $storeName }}</title>
    <style>
        @page {
            size: A4 landscape;
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
                max-width: 297mm;
                margin: 0 auto;
                background: #ffffff;
                padding: 14mm 16mm;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
            .screen-toolbar {
                max-width: 297mm;
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
        .doc-footer { margin-top: 20px; border-top: 1px solid #cbd5e1; padding-top: 6px; display: flex; justify-content: space-between; font-size: 7.5pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="screen-toolbar">
        <a href="{{ route('reports.stock-valuation') }}" class="btn btn-secondary">&larr; Back to Stock Report</a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('reports.export.stock-valuation-csv') }}" class="btn btn-secondary">Export CSV</a>
            <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
        </div>
    </div>

    <div class="page-container">
        {{-- Header matching Image 3 bottom-left --}}
        <header class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-logo-icon">ET</div>
                <div>
                    <div class="doc-store-name">{{ $storeName }}</div>
                    <div class="doc-store-subtitle">Retail POS & Inventory Management · Currency: Ethiopian Birr (ETB)</div>
                </div>
            </div>
            <div class="doc-meta">
                <div>Report Generated: <strong>{{ now()->format('M d, Y · h:i A') }}</strong></div>
                <div>Period: <strong>As of {{ now()->format('M d, Y') }}</strong></div>
            </div>
        </header>

        {{-- Emerald Banner matching Image 3 --}}
        <div class="doc-banner">
            <div>
                <h1 class="doc-banner-title">Stock Valuation Report</h1>
                <div class="doc-banner-subtitle">How much your current stock is worth at purchase cost and retail selling value</div>
            </div>
            <div style="font-size: 8pt; font-weight: 700; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 4px;">
                ACTIVE INVENTORY
            </div>
        </div>

        {{-- 4 Summary Cards matching Image 3 --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Products in Stock</div>
                <div class="kpi-value">{{ number_format($valuation['total_units']) }} <span style="font-size: 8pt; font-weight: 500; color: #64748b;">units</span></div>
                <div class="kpi-explain">{{ $valuation['total_items'] }} active catalog products</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Money Spent on Stock (Cost)</div>
                <div class="kpi-value">ETB {{ number_format($valuation['total_cost_valuation'], 2) }}</div>
                <div class="kpi-explain">Total purchasing capital tied in stock</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Potential Sales Value</div>
                <div class="kpi-value text-emerald">ETB {{ number_format($valuation['total_retail_valuation'], 2) }}</div>
                <div class="kpi-explain">If all available stock is sold at current prices</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Potential Profit</div>
                <div class="kpi-value text-emerald">ETB {{ number_format($valuation['projected_profit'], 2) }}</div>
                <div class="kpi-explain">{{ number_format($valuation['projected_margin'], 1) }}% estimated gross margin</div>
            </div>
        </div>

        {{-- Category Breakdown Table matching Image 3 --}}
        <div class="section-title">Stock Breakdown by Category</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">Units in Stock</th>
                    <th class="text-right">Purchasing Cost</th>
                    <th class="text-right">Retail Value</th>
                    <th class="text-right">Potential Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byCategory as $cat)
                    <tr>
                        <td class="font-bold">{{ $cat['name'] }}</td>
                        <td class="text-center">{{ $cat['products_count'] }}</td>
                        <td class="text-center font-mono">{{ number_format($cat['total_units']) }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($cat['cost_value'], 2) }}</td>
                        <td class="text-right font-mono text-emerald">ETB {{ number_format($cat['retail_value'], 2) }}</td>
                        <td class="text-right font-mono font-bold text-emerald">ETB {{ number_format($cat['potential_profit'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Portfolio</td>
                    <td class="text-center">{{ $valuation['total_items'] }}</td>
                    <td class="text-center font-mono">{{ number_format($valuation['total_units']) }}</td>
                    <td class="text-right font-mono">ETB {{ number_format($valuation['total_cost_valuation'], 2) }}</td>
                    <td class="text-right font-mono text-emerald">ETB {{ number_format($valuation['total_retail_valuation'], 2) }}</td>
                    <td class="text-right font-mono text-emerald">ETB {{ number_format($valuation['projected_profit'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Product Detail Table --}}
        <div class="section-title">Detailed Product Inventory Valuation</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-center">Units on Hand</th>
                    <th class="text-right">Cost Price</th>
                    <th class="text-right">Selling Price</th>
                    <th class="text-right">Total Cost Value</th>
                    <th class="text-right">Total Retail Value</th>
                    <th class="text-right">Potential Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($valuation['products'] as $product)
                    @php
                        $costVal = $product->quantity * $product->purchase_price;
                        $retailVal = $product->quantity * $product->selling_price;
                        $profitVal = $retailVal - $costVal;
                    @endphp
                    <tr>
                        <td class="font-bold">{{ $product->name }}</td>
                        <td class="font-mono text-slate" style="font-size: 7.5pt;">{{ $product->sku ?: '-' }}</td>
                        <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
                        <td class="text-center font-mono font-bold">{{ $product->quantity }} {{ $product->unit?->symbol }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($product->purchase_price, 2) }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($product->selling_price, 2) }}</td>
                        <td class="text-right font-mono">ETB {{ number_format($costVal, 2) }}</td>
                        <td class="text-right font-mono text-emerald">ETB {{ number_format($retailVal, 2) }}</td>
                        <td class="text-right font-mono font-bold text-emerald">ETB {{ number_format($profitVal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <footer class="doc-footer">
            <div>Generated by {{ $storeName }} System · Stock Valuation Document</div>
            <div>Page 1 of 1</div>
        </footer>
    </div>
</body>
</html>
