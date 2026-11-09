<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
            background: #fff;
            padding: 8px 10px;
            max-width: 80mm;
            margin: 0 auto;
        }

        /* 58mm narrower mode if requested via query ?width=58 */
        @media screen {
            body.width-58 {
                max-width: 58mm;
            }
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .divider-double {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .receipt-header {
            margin-bottom: 8px;
        }

        .receipt-title {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .receipt-info {
            font-size: 10px;
            margin-top: 2px;
        }

        .meta-table {
            width: 100%;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .meta-table td {
            padding: 1px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .items-table th {
            text-align: left;
            padding: 3px 0;
            border-bottom: 1px dashed #000;
        }

        .items-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            font-size: 11px;
            margin-top: 4px;
        }

        .totals-table td {
            padding: 1.5px 0;
        }

        .grand-total {
            font-size: 13px;
            font-weight: bold;
        }

        .receipt-footer {
            margin-top: 10px;
            font-size: 9.5px;
            color: #333;
        }

        .no-print-toolbar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            font-family: sans-serif;
            font-size: 12px;
        }

        .btn {
            background: #0284c7;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary {
            background: #64748b;
        }

        @media print {
            .no-print-toolbar {
                display: none !important;
            }
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                padding: 4mm 3mm;
                max-width: 100%;
                width: 100%;
            }
            body.width-58 {
                size: 58mm auto;
            }
        }
    </style>
</head>
<body class="{{ request('width') == '58' ? 'width-58' : 'width-80' }}">

    <!-- Non-print control toolbar for cashiers -->
    <div class="no-print-toolbar">
        <div>
            <strong>Receipt Preview</strong>
            <span style="font-size:11px; color:#64748b;">({{ request('width') == '58' ? '58mm' : '80mm' }} Thermal)</span>
        </div>
        <div style="display: flex; gap: 4px;">
            @if(request('width') == '58')
                <a href="{{ request()->fullUrlWithQuery(['width' => '80']) }}" class="btn btn-secondary" style="font-size:11px; padding:3px 8px;">Switch to 80mm</a>
            @else
                <a href="{{ request()->fullUrlWithQuery(['width' => '58']) }}" class="btn btn-secondary" style="font-size:11px; padding:3px 8px;">Switch to 58mm</a>
            @endif
            <button onclick="window.print()" class="btn" style="padding:3px 10px;">🖨️ Print</button>
            <button onclick="window.close()" class="btn btn-secondary" style="padding:3px 10px;">Close</button>
        </div>
    </div>

    <div class="receipt-header text-center">
        <div class="receipt-title">{{ config('app.name', 'STORE REGISTER') }}</div>
        <div class="receipt-info font-bold">RETAIL & SHOPPING CENTER</div>
        <div class="receipt-info">{{ now()->timezone(config('app.timezone', 'UTC'))->format('d M Y - H:i:s') }}</div>
    </div>

    <div class="divider"></div>

    <table class="meta-table">
        <tr>
            <td class="text-left font-bold">INVOICE:</td>
            <td class="text-right font-bold">#{{ $sale->invoice_number }}</td>
        </tr>
        <tr>
            <td class="text-left">CASHIER:</td>
            <td class="text-right">{{ $sale->creator->name ?? 'Staff' }}</td>
        </tr>
        <tr>
            <td class="text-left">CUSTOMER:</td>
            <td class="text-right">{{ $sale->customer->name ?? 'Guest / Walk-In' }}</td>
        </tr>
        <tr>
            <td class="text-left">METHOD:</td>
            <td class="text-right uppercase">{{ $sale->payment_method->label() ?? $sale->payment_method->value ?? 'Cash' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">ITEM</th>
                <th class="text-right" style="width: 20%;">QTY</th>
                <th class="text-right" style="width: 30%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                @php
                    $unitPrice = $item->product ? $item->product->selling_price : 0;
                    $effectivePrice = $unitPrice - ($item->discount ?? 0);
                    $lineTotal = $effectivePrice * $item->quantity;
                @endphp
                <tr>
                    <td colspan="3" class="font-bold" style="padding-top: 3px;">
                        {{ $item->product->name ?? 'Item #' . $item->product_id }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 9.5px; color: #444; padding-left: 2px;">
                        @money($effectivePrice)
                        @if($item->discount > 0)
                            <span style="text-decoration: line-through;">(@money($unitPrice))</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }} {{ $item->product->unit->symbol ?? '' }}</td>
                    <td class="text-right font-bold">@money($lineTotal)</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td class="text-left">Subtotal:</td>
            <td class="text-right">@money($sale->subtotal)</td>
        </tr>
        @if($sale->global_discount > 0)
            <tr>
                <td class="text-left">Discount:</td>
                <td class="text-right">- @money($sale->global_discount)</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td class="text-left" style="padding-top: 3px;">TOTAL:</td>
            <td class="text-right" style="padding-top: 3px;">@money($sale->total)</td>
        </tr>
        @if($sale->cash_received > 0)
            <tr style="border-top: 1px dotted #000;">
                <td class="text-left" style="padding-top: 3px;">Cash Tendered:</td>
                <td class="text-right" style="padding-top: 3px;">@money($sale->cash_received)</td>
            </tr>
            <tr class="font-bold">
                <td class="text-left">CHANGE DUE:</td>
                <td class="text-right">@money($sale->change)</td>
            </tr>
        @endif
    </table>

    <div class="divider-double"></div>

    <div class="receipt-footer text-center">
        <p class="font-bold">THANK YOU FOR YOUR PURCHASE!</p>
        <p style="margin-top: 2px;">Items once sold cannot be returned.</p>
        <p style="margin-top: 4px; font-family: monospace; letter-spacing: 2px;">*{{ $sale->invoice_number }}*</p>
    </div>

    <script>
        // Auto trigger native print dialog on page load
        window.addEventListener('load', () => {
            // Give 300ms for browser layout rendering before calling print
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>

