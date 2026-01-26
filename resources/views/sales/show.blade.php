<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sale Details') }} #{{ $sale->invoice_number }}
            </h2>
            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ route('sales.create') }}">
                    &plus; New Sale
                </x-button>
                <x-button variant="secondary" href="{{ route('sales.index') }}">
                    &larr; Back to List
                </x-button>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <!-- Header Info -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Sale Information</h3>
                            <p class="text-sm text-gray-500">Details of the sale transaction</p>
                        </div>
                        <div class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                            Invoice: {{ $sale->invoice_number }}
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Customer -->
                        <x-detail-item label="Customer" :value="$sale->customer ? $sale->customer->name : 'Guest'">
                            <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Sale Date -->
                        <x-detail-item label="Sale Date" :value="$sale->sale_date->format('d M Y H:i')">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Payment Method -->
                        <x-detail-item label="Payment Method" :value="$sale->payment_method->label()">
                            <x-heroicon-o-credit-card class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Status -->
                        <div>
                            <label class="text-sm font-medium leading-none text-gray-500">Status</label>
                            <div class="mt-1">
                                <x-status-badge :status="$sale->status" />
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <x-detail-item label="Total Amount" :value="'Rp ' . number_format($sale->total, 0, ',', '.')">
                            <x-heroicon-o-banknotes class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Cashier -->
                        <x-detail-item label="Cashier" :value="$sale->creator->name ?? 'Unknown'">
                            <x-heroicon-o-user-circle class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                         <!-- Cash Received -->
                         <x-detail-item label="Cash Received" :value="'Rp ' . number_format($sale->cash_received, 0, ',', '.')">
                            <x-heroicon-o-banknotes class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Change -->
                        <x-detail-item label="Change Return" :value="'Rp ' . number_format($sale->change, 0, ',', '.')">
                            <x-heroicon-o-arrow-path class="w-4 h-4 text-gray-400" />
                        </x-detail-item>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-gray-500">
                                Notes
                            </label>
                            <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                <p class="text-sm text-slate-700 italic leading-relaxed">{{ $sale->notes ?: 'No additional notes.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Section -->
                    <div class="mt-6 border-t overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Code</th>
                                    <th class="px-6 py-3">Product</th>
                                    <th class="px-6 py-3 text-center">Quantity</th>
                                    <th class="px-6 py-3 text-right">Unit Price</th>
                                    <th class="px-6 py-3 text-right">Discount</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($sale->items as $item)
                                    <tr class="bg-white hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $item->product->product_code ?? $item->product->sku ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ number_format($item->quantity) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-red-500">
                                            {{ $item->discount > 0 ? '- Rp ' . number_format($item->discount * $item->quantity, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-right">Total</td>
                                    <td class="px-6 py-4 text-right text-indigo-600 text-lg">
                                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Workflow -->
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-4">
                @if($sale->status !== \App\Enums\SaleStatus::CANCELLED)


                    <!-- Manually using Form for now since no Livewire component here -->
                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Are you sure you want to CANCEL this sale? This will restore stock.');" >
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger" type="submit">
                            Cancel / Void Sale
                        </x-button>
                    </form>
                @else
                    <x-button variant="secondary" disabled>
                        Sale Cancelled
                    </x-button>
                @endif

                @endif

                 {{-- Print Action --}}
                 <a href="{{ route('sales.print', $sale) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-printer class="w-5 h-5 mr-1" />
                    Print Invoice
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
