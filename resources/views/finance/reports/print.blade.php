<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas - {{ $storeName }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; margin: 0; padding: 40px; }
        .header-container { border-bottom: 2px solid #444; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-start; }
        .company-info h1 { margin: 0 0 5px 0; font-size: 22px; text-transform: uppercase; letter-spacing: 1px; color: #222; }
        .company-info p { margin: 0; font-size: 11px; color: #666; }
        .report-meta { text-align: right; }
        .report-title { font-size: 16px; font-weight: bold; color: #444; text-transform: uppercase; margin-bottom: 5px; }
        .meta-item { font-size: 11px; color: #666; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 10px 5px; text-align: left; font-weight: bold; font-size: 11px; text-transform: uppercase; color: #000; }
        td { padding: 8px 5px; border-bottom: 1px solid #ddd; vertical-align: top; color: #000; }
        tr:nth-child(even) { background-color: transparent; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge { display: inline-block; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        /* Using strong colors that print well */
        .badge-income { color: #15803d; } /* Green-700 */
        .badge-expense { color: #b91c1c; } /* Red-700 */

        .summary-section { display: flex; justify-content: flex-end; margin-top: 20px; page-break-inside: avoid; }
        .summary-table { width: 40%; border: none; }
        .summary-table td { padding: 5px 10px; border: none; }
        .summary-row-total td { border-top: 2px solid #444; padding-top: 10px; font-size: 14px; font-weight: bold; }

        .signature-area { margin-top: 60px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .signature-box { width: 30%; text-align: center; }
        .signature-line { margin-top: 70px; border-top: 1px solid #aaa; width: 80%; margin-left: auto; margin-right: auto; }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none !important; }
            /* Essential resets */
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; box-shadow: none !important; text-shadow: none !important; }

            /* Force black text generally */
            body, h1, p, table, th, td { color: #000; }

            /* Allow specific colors for badges */
            .badge-income { color: #15803d !important; }
            .badge-expense { color: #b91c1c !important; }

            .header-container { border-bottom: 2px solid #000 !important; }
            th { border-bottom: 2px solid #000 !important; border-top: 2px solid #000 !important; }
            td { border-bottom: 1px solid #ccc !important; }
        }

        .btn-print { background-color: #2563eb; color: white; border: none; padding: 10px 25px; border-radius: 6px; cursor: pointer; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn-print:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">🖨️ Print Report</button>
    </div>

    <div class="header-container">
        <div class="company-info">
            <h1>{{ $storeName }}</h1>
            <p>{{ $storeAddress }}</p>
            @if($storePhone !== '-')
                <p>Telp: {{ $storePhone }}</p>
            @endif
        </div>
        <div class="report-meta">
            <div class="report-title">Cash Flow Report</div>
            <div class="meta-item">
                Period: {{ $periodText }}
            </div>
            <div class="meta-item">
                Printed: {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}
            </div>
            <div class="meta-item">By: {{ auth()->user()->name ?? 'Admin' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Date</th>
                <th width="10%">Type</th>
                <th width="18%">Category</th>
                <th width="40%">Description</th>
                <th width="15%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashFlows as $index => $cf)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $cf->transaction_date->format('d M Y') }}</td>

                    <td>
                        @if($cf->category->type === \App\Enums\FinanceCategoryType::Income)
                            <span class="badge badge-income">INCOME</span>
                        @else
                            <span class="badge badge-expense">EXPENSE</span>
                        @endif
                    </td>

                    <td style="font-weight: 500;">
                        {{ $cf->category->name ?? '-' }}
                    </td>

                    <td>
                        {{ $cf->description }}
                        @if($cf->external_reference)
                            <div style="font-size: 10px; color: #888; margin-top: 2px;">
                                Ref: {{ $cf->external_reference }}
                            </div>
                        @else
                             <div style="font-size: 10px; color: #aaa; margin-top: 2px;">
                                Ref: {{ $cf->code }}
                            </div>
                        @endif
                    </td>

                    <td class="text-right" style="font-family: monospace; font-size: 13px;">
                        {{ number_format($cf->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #888;">
                        No transactions selected.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="text-right" style="color: #666;">Opening Balance ({{ \Carbon\Carbon::parse($openingBalanceDate)->format('d M Y') }})</td>
                <td class="text-right">{{ number_format($openingBalanceAmount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right" style="color: #666;">Total Income</td>
                <td class="text-right" style="color: #065f46;">+ {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right" style="color: #666;">Total Expense</td>
                <td class="text-right" style="color: #991b1b;">- {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
            <tr class="summary-row-total">
                <td class="text-right">Estimated Final Balance</td>
                <td class="text-right">Rp {{ number_format($estimatedFinalBalance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div>Created By</div>
            <div class="signature-line"></div>
            <div style="font-size: 10px; color: #666; margin-top: 5px;">(Finance Admin)</div>
        </div>
        <div class="signature-box">
            <div>Checked By</div>
            <div class="signature-line"></div>
            <div style="font-size: 10px; color: #666; margin-top: 5px;">(Ops Manager)</div>
        </div>
        <div class="signature-box">
            <div>Approved By</div>
            <div class="signature-line"></div>
            <div style="font-size: 10px; color: #666; margin-top: 5px;">(Store Owner)</div>
        </div>
    </div>

</body>
</html>
