<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Business Report' }} - {{ $storeName }}</title>
    <style>
        @page {
            size: {{ $pageOrientation ?? 'portrait' }};
            margin: 12mm 14mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Screen only toolbar */
        @media screen {
            body {
                background: #f1f5f9;
                padding: 24px 12px;
            }
            .page-container {
                max-width: {{ ($pageOrientation ?? 'portrait') === 'landscape' ? '297mm' : '210mm' }};
                margin: 0 auto;
                background: #ffffff;
                padding: 16mm 18mm;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
                border-radius: 8px;
            }
            .screen-toolbar {
                max-width: {{ ($pageOrientation ?? 'portrait') === 'landscape' ? '297mm' : '210mm' }};
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
                transition: all 0.15s;
            }
            .btn-primary {
                background-color: #0C735B;
                color: #ffffff;
                border: 1px solid #0C735B;
            }
            .btn-primary:hover {
                background-color: #074E3E;
            }
            .btn-secondary {
                background-color: #ffffff;
                color: #334155;
                border: 1px solid #cbd5e1;
            }
            .btn-secondary:hover {
                background-color: #f8fafc;
            }
        }

        @media print {
            .screen-toolbar {
                display: none !important;
            }
            .page-container {
                width: 100% !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
        }

        /* Document Header */
        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #0C735B;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .doc-logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .doc-logo-icon {
            width: 38px;
            height: 38px;
            background: #0C735B;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
        }

        .doc-store-name {
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        .doc-store-subtitle {
            font-size: 8.5pt;
            color: #64748b;
            font-weight: 500;
            margin-top: 2px;
        }

        .doc-meta {
            text-align: right;
            font-size: 8.5pt;
            color: #475569;
            line-height: 1.4;
        }

        .doc-meta strong {
            color: #0f172a;
        }

        /* Report Banner Title */
        .doc-banner {
            background-color: #0C735B;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .doc-banner-title {
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: -0.01em;
            margin: 0;
        }

        .doc-banner-subtitle {
            font-size: 8.5pt;
            opacity: 0.9;
            margin-top: 1px;
        }

        .doc-banner-badge {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.05em;
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .kpi-card {
            background: #F8FAF9;
            border: 1px solid #E2ECE9;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .kpi-label {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 3px;
        }

        .kpi-value {
            font-size: 13pt;
            font-weight: 900;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            color: #0f172a;
            line-height: 1.1;
        }

        .kpi-value.text-emerald {
            color: #0C735B;
        }

        .kpi-value.text-blue {
            color: #1d4ed8;
        }

        .kpi-explain {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.25;
        }

        /* Section Headings */
        .section-title {
            font-size: 10.5pt;
            font-weight: 800;
            color: #0f172a;
            margin: 16px 0 8px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        /* Clean Report Tables */
        table.doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 16px;
        }

        table.doc-table thead {
            display: table-header-group;
        }

        table.doc-table tr {
            page-break-inside: avoid;
        }

        table.doc-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 7px 8px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1.5px solid #94a3b8;
            text-align: left;
        }

        table.doc-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        table.doc-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table.doc-table tfoot td {
            font-weight: 800;
            background-color: #f8fafc;
            border-top: 1.5px solid #94a3b8;
            border-bottom: 2px solid #334155;
            padding: 8px;
        }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important; }
        .font-bold { font-weight: 700 !important; }
        .text-emerald { color: #0C735B !important; }
        .text-rose { color: #e11d48 !important; }
        .text-slate { color: #64748b !important; }

        /* Document Footer */
        .doc-footer {
            margin-top: 24px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 8pt;
            color: #64748b;
        }
    </style>
</head>
<body>
    {{-- On-Screen Toolbar (Hidden when printing/PDF exporting) --}}
    <div class="screen-toolbar">
        <a href="{{ $backUrl ?? route('reports.index') }}" class="btn btn-secondary">
            &larr; Back to App
        </a>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                Print / Save as PDF
            </button>
        </div>
    </div>

    {{-- Printable Page Container --}}
    <div class="page-container">
        {{-- Document Header --}}
        <header class="doc-header">
            <div class="doc-logo-area">
                <div class="doc-logo-icon">ET</div>
                <div>
                    <div class="doc-store-name">{{ $storeName }}</div>
                    <div class="doc-store-subtitle">Retail POS & Stock Management · Currency: Ethiopian Birr (ETB)</div>
                </div>
            </div>
            <div class="doc-meta">
                <div>Report Generated: <strong>{{ now()->format('M d, Y · h:i A') }}</strong></div>
                @if(!empty($periodLabel))
                    <div>Reporting Period: <strong>{{ $periodLabel }}</strong></div>
                @endif
                @if(!empty($storeAddress))
                    <div style="font-size: 7.5pt; color: #64748b;">{{ $storeAddress }}</div>
                @endif
            </div>
        </header>

        {{-- Main Document Content Slot --}}
        {{ $slot }}

        {{-- Document Footer --}}
        <footer class="doc-footer">
            <div>Generated by {{ $storeName }} System · Confidential Business Document</div>
            <div>Official Report</div>
        </footer>
    </div>
</body>
</html>
