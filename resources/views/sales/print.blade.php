<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $sale->invoice_number }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body {
                background: white;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4;
                margin: 0;
            }
            .print-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 40px;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <div class="max-w-[210mm] mx-auto my-8 bg-white shadow-lg print-container relative min-h-[297mm]">

        <!-- Print Toolbar -->
        <div class="no-print absolute top-4 right-4 flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm transition flex items-center text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>
            <a href="{{ route('sales.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm transition flex items-center text-sm font-medium">
                &larr; Back
            </a>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-start mb-12 pt-8 px-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 tracking-tight">INVOICE</h1>
                <p class="text-gray-500 mt-1">#{{ $sale->invoice_number }}</p>
                <div class="mt-4">
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Status</p>
                    <span class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                        {{ $sale->status === \App\Enums\SaleStatus::COMPLETED ? 'bg-green-100 text-green-800 border-green-200' : '' }}
                        {{ $sale->status === \App\Enums\SaleStatus::PENDING ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : '' }}
                        {{ $sale->status === \App\Enums\SaleStatus::CANCELLED ? 'bg-red-100 text-red-800 border-red-200' : '' }}">
                        {{ $sale->status->label() }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-700">Toko Berkah Sejahtera</h2>
                <div class="text-sm text-gray-500 mt-2 space-y-1">
                    <p>Jl. Raya Utama No. 123</p>
                    <p>Jakarta Selatan, 12000</p>
                    <p>contact@berkahsejahtera.com</p>
                    <p>+62 812 3456 7890</p>
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-12 px-8 mb-12">
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2 mb-3">Bill To</p>
                <div class="text-gray-800">
                    @if($sale->customer)
                        <p class="font-bold text-lg">{{ $sale->customer->name }}</p>
                        @if($sale->customer->address)
                            <p class="text-sm text-gray-600 mt-1 max-w-xs">{{ $sale->customer->address }}</p>
                        @endif
                        @if($sale->customer->phone)
                            <p class="text-sm text-gray-600 mt-1">{{ $sale->customer->phone }}</p>
                        @endif
                    @else
                        <p class="font-medium text-gray-700 italic">Guest Customer</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2 mb-3">Invoice Details</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium text-gray-900">{{ $sale->sale_date->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Served By:</span>
                        <span class="font-medium text-gray-900">{{ $sale->creator->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium text-gray-900 uppercase">{{ $sale->payment_method->label() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="px-8 mb-8">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-800 text-gray-800 uppercase tracking-wider font-bold text-xs">
                        <th class="text-left py-3">Item Description</th>
                        <th class="text-center py-3 w-24">Qty</th>
                        <th class="text-right py-3 w-32">Unit Price</th>
                        <th class="text-right py-3 w-32">Discount</th>
                        <th class="text-right py-3 w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sale->items as $item)
                        <tr>
                            <td class="py-4 text-gray-700">
                                <p class="font-semibold">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->product->product_code ?? $item->product->sku }}</p>
                            </td>
                            <td class="py-4 text-center text-gray-700">{{ $item->quantity }}</td>
                            <td class="py-4 text-right text-gray-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="py-4 text-right text-red-500 text-xs">
                                {{ $item->discount > 0 ? '- Rp ' . number_format($item->discount * $item->quantity, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 text-right font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Info -->
        <div class="px-8 flex flex-row justify-between items-start">
            <div class="w-1/2 pr-12">
                @if($sale->notes)
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Notes</p>
                        <p class="text-sm text-gray-600 italic bg-gray-50 p-3 rounded border border-gray-100">{{ $sale->notes }}</p>
                    </div>
                @endif
                <div class="text-xs text-gray-500 mt-8">
                    <p class="font-bold mb-1">Terms & Conditions:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Goods sold are non-refundable unless defective.</li>
                        <li>Please keep this invoice as proof of purchase.</li>
                        <li>Warranty claims require this original invoice.</li>
                    </ul>
                </div>
            </div>

            <div class="w-1/2 max-w-xs">
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($sale->total_discount > 0)
                    <div class="flex justify-between text-red-600 text-sm">
                        <span>Total Discount</span>
                        <span>- Rp {{ number_format($sale->total_discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-900 font-bold text-lg pt-3 border-t-2 border-gray-800">
                        <span>TOTAL</span>
                        <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-6 space-y-2 mt-4 border-t border-gray-200">
                         <div class="flex justify-between text-gray-600 text-sm">
                            <span>Cash Received</span>
                            <span>Rp {{ number_format($sale->cash_received, 0, ',', '.') }}</span>
                        </div>
                         <div class="flex justify-between text-gray-600 text-sm">
                            <span>Change</span>
                            <span>Rp {{ number_format($sale->change, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Area (Optional, for formal invoices) -->
        <div class="absolute bottom-12 left-8 right-8 flex justify-between text-center no-print-height">
           {{-- Only visible in print or long pages --}}
        </div>

        <div class="mt-20 px-8 text-center text-xs text-gray-400">
             <p>Generated by Sales System on {{ now()->format('d M Y H:i:s') }}</p>
        </div>

    </div>
</body>
</html>
