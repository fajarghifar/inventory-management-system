<x-app-layout title="Order Details #{{ $sale->invoice_number ?: $sale->id }}">
    <div class="py-4 sm:py-6">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4">

            <!-- 1. Top Navigation Bar -->
            <div class="flex items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-100 shadow-xs">
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('sales.index') }}"
                        class="p-2 text-gray-500 hover:text-brand hover:bg-brand-50 rounded-xl transition-colors"
                        title="Back to Sales History"
                    >
                        <x-heroicon-o-arrow-left class="w-5 h-5" />
                    </a>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            <span>Order Details</span>
                            <span class="font-mono text-sm text-brand bg-brand-50 px-2 py-0.5 rounded-lg border border-brand-100">
                                {{ $sale->invoice_number ?: '#' . $sale->id }}
                            </span>
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Recorded {{ $sale->sale_date ? $sale->sale_date->format('M d, Y · h:i A') : 'Recently' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Thermal Receipt Button -->
                    <a
                        href="{{ route('sales.receipt', $sale) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-brand bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-xl transition-colors"
                        title="Print 58/80mm Thermal Receipt"
                    >
                        <x-heroicon-o-receipt-percent class="w-4 h-4" />
                        <span class="hidden sm:inline">Thermal Receipt</span>
                    </a>

                    <!-- A4 Invoice Button -->
                    <a
                        href="{{ route('sales.print', $sale) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors"
                        title="Print A4 Invoice"
                    >
                        <x-heroicon-o-printer class="w-4 h-4" />
                        <span class="hidden sm:inline">A4 Invoice</span>
                    </a>
                </div>
            </div>

            <!-- 2. Order Metadata Card (Matching Mobile Reference) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <!-- Date -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block font-medium">Sale Date</span>
                            <span class="font-medium text-gray-900">
                                {{ $sale->sale_date ? $sale->sale_date->format('d M Y, h:i A') : '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Customer -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                            <x-heroicon-o-user class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block font-medium">Customer Name</span>
                            <span class="font-medium text-gray-900">
                                {{ $sale->customer?->name ?: 'Walk In Customer' }}
                            </span>
                        </div>
                    </div>

                    <!-- Cashier -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-brand shrink-0">
                            <span class="font-bold text-xs">{{ strtoupper(substr($sale->creator?->name ?? 'U', 0, 2)) }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block font-medium">Cashier / Staff</span>
                            <span class="font-medium text-gray-900">
                                {{ $sale->creator?->name ?: 'System' }}
                            </span>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0">
                            @if($sale->status === \App\Enums\SaleStatus::COMPLETED)
                                <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-600" />
                            @elseif($sale->status === \App\Enums\SaleStatus::PENDING)
                                <x-heroicon-o-clock class="w-4 h-4 text-amber-600" />
                            @else
                                <x-heroicon-o-x-circle class="w-4 h-4 text-rose-600" />
                            @endif
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 block font-medium">Status</span>
                            @if($sale->status === \App\Enums\SaleStatus::COMPLETED)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Completed
                                </span>
                            @elseif($sale->status === \App\Enums\SaleStatus::PENDING)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Cancelled
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Method Banner -->
                <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Method:</span>
                        @if($sale->payment_method === \App\Enums\PaymentMethod::CASH)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <x-heroicon-o-banknotes class="w-4 h-4" />
                                <span>CASH</span>
                            </span>
                        @elseif($sale->payment_method === \App\Enums\PaymentMethod::TRANSFER)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                <x-heroicon-o-credit-card class="w-4 h-4" />
                                <span>BANK TRANSFER</span>
                            </span>
                        @else
                            <span class="text-xs text-gray-500">-</span>
                        @endif
                    </div>

                    @if($sale->payment_method === \App\Enums\PaymentMethod::CASH && $sale->cash_received > 0)
                        <div class="flex items-center gap-4 text-xs font-mono">
                            <span class="text-gray-500">
                                Received: <strong class="text-gray-900">ETB {{ number_format($sale->cash_received, 2) }}</strong>
                            </span>
                            <span class="text-emerald-700">
                                Change: <strong class="text-emerald-800">ETB {{ number_format($sale->change, 2) }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                @if($sale->notes)
                    <div class="pt-3 border-t border-gray-100 text-xs">
                        <span class="font-semibold text-gray-400 block mb-1">Transaction Notes</span>
                        <p class="text-gray-600 bg-gray-50 p-2.5 rounded-xl border border-gray-100 italic">
                            {{ $sale->notes }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- 3. Items Section Header -->
            <div class="flex items-center justify-between pt-1">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <x-heroicon-o-shopping-bag class="w-4 h-4 text-brand" />
                    <span>Purchased Items ({{ $sale->items->count() }})</span>
                </h2>
                <span class="text-xs text-gray-400 font-mono">
                    {{ $sale->items->sum('quantity') }} total pcs
                </span>
            </div>

            <!-- DESKTOP ITEM TABLE (>= 640px) -->
            <div class="hidden sm:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <table class="w-full text-left text-sm divide-y divide-gray-100">
                    <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="py-3 pl-5 pr-3">Product</th>
                            <th scope="col" class="px-3 py-3">Unit</th>
                            <th scope="col" class="px-3 py-3 text-center">Qty</th>
                            <th scope="col" class="px-3 py-3 text-right">Unit Price</th>
                            <th scope="col" class="px-3 py-3 text-right">Discount</th>
                            <th scope="col" class="py-3 pl-3 pr-5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($sale->items as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 pl-5 pr-3">
                                    <div class="font-medium text-gray-900">
                                        {{ $item->product->name }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 font-mono">
                                        SKU: {{ $item->product->sku ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $item->product->unit?->symbol ?: $item->product->unit?->name ?: '-' }}
                                </td>
                                <td class="px-3 py-3.5 text-center font-mono font-bold text-gray-900 whitespace-nowrap">
                                    {{ number_format($item->quantity) }}
                                </td>
                                <td class="px-3 py-3.5 text-right font-mono text-gray-700 whitespace-nowrap">
                                    ETB {{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-3 py-3.5 text-right font-mono whitespace-nowrap">
                                    @if($item->discount > 0)
                                        <span class="text-rose-600">- ETB {{ number_format($item->discount, 2) }}</span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 pl-3 pr-5 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                    ETB {{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- MOBILE ITEM CARDS (< 640px) -->
            <div class="sm:hidden space-y-2.5">
                @foreach($sale->items as $item)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-4 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $item->product->name }}</h3>
                                <p class="text-[11px] text-gray-400 font-mono">
                                    SKU: {{ $item->product->sku ?: '-' }} · {{ $item->product->unit?->symbol ?: 'pcs' }}
                                </p>
                            </div>
                            <span class="font-mono font-bold text-sm text-gray-900 whitespace-nowrap">
                                ETB {{ number_format($item->subtotal, 2) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-gray-50 text-xs">
                            <span class="text-gray-500">
                                Quantity: <strong class="text-gray-900 font-mono">{{ $item->quantity }}</strong>
                            </span>
                            <span class="text-gray-500 font-mono">
                                ETB {{ number_format($item->unit_price, 2) }} &times; {{ $item->quantity }}
                            </span>
                        </div>

                        @if($item->discount > 0)
                            <div class="flex items-center justify-between text-xs text-rose-600 font-mono pt-1">
                                <span>Discount</span>
                                <span>- ETB {{ number_format($item->discount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- 4. Financial Breakdown Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-3 font-mono">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>Total Items</span>
                    <span class="font-bold text-gray-800">{{ $sale->items->count() }} ({{ $sale->items->sum('quantity') }} pcs)</span>
                </div>

                <div class="flex justify-between items-center text-xs text-gray-600">
                    <span>Sub Total</span>
                    <span class="font-semibold text-gray-800">ETB {{ number_format($sale->subtotal, 2) }}</span>
                </div>

                @if($sale->total_discount > 0)
                    <div class="flex justify-between items-center text-xs text-rose-600">
                        <span>Discount</span>
                        <span class="font-semibold">- ETB {{ number_format($sale->total_discount, 2) }}</span>
                    </div>
                @endif

                <div class="pt-3 border-t-2 border-gray-100 flex justify-between items-center text-base sm:text-lg">
                    <span class="font-bold text-gray-900">Total Price:</span>
                    <span class="font-black text-brand text-xl sm:text-2xl tracking-tight">
                        ETB {{ number_format($sale->total, 2) }}
                    </span>
                </div>

                @if($sale->payment_method === \App\Enums\PaymentMethod::CASH)
                    <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-2 text-xs">
                        <div class="flex justify-between text-gray-600">
                            <span>Cash Received:</span>
                            <span class="font-bold text-gray-900">ETB {{ number_format($sale->cash_received, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700">
                            <span>Change:</span>
                            <span class="font-bold text-emerald-800">ETB {{ number_format($sale->change, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 5. Action Buttons Workflow (Mobile-First Touch Ergonomics) -->
            <div
                x-data="{
                    actionUrl: '',
                    actionMethod: '',
                    modalTitle: '',
                    modalMessage: '',
                    confirmButtonText: '',
                    confirmButtonClass: '',

                    confirmAction(url, method, title, message, btnText, btnClass) {
                        this.actionUrl = url;
                        this.actionMethod = method;
                        this.modalTitle = title;
                        this.modalMessage = message;
                        this.confirmButtonText = btnText;
                        this.confirmButtonClass = btnClass;
                        $dispatch('open-modal', { name: 'confirmation-modal' });
                    }
                }"
                class="pt-2 flex flex-col sm:flex-row gap-3"
            >
                <!-- Thermal Receipt (Primary Emerald) -->
                <a
                    href="{{ route('sales.receipt', $sale) }}"
                    target="_blank"
                    class="h-12 flex-1 inline-flex items-center justify-center gap-2 px-4 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-sm shadow-sm transition-all active:scale-98"
                >
                    <x-heroicon-o-receipt-percent class="w-5 h-5" />
                    <span>Print Thermal Receipt</span>
                </a>

                <!-- A4 Invoice / PDF -->
                <a
                    href="{{ route('sales.print', $sale) }}"
                    target="_blank"
                    class="h-12 flex-1 inline-flex items-center justify-center gap-2 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-sm transition-colors"
                >
                    <x-heroicon-o-printer class="w-5 h-5" />
                    <span>PDF / A4 Invoice</span>
                </a>

                @if($sale->status === \App\Enums\SaleStatus::PENDING)
                    <!-- Complete Sale Action -->
                    <button
                        type="button"
                        @click="confirmAction('{{ route('sales.complete', $sale) }}', 'PATCH', 'Complete Sale', 'Mark this sale as Completed? This confirms payment has been received and fulfills the order.', 'Complete Sale', 'bg-emerald-600 hover:bg-emerald-700 text-white')"
                        class="h-12 inline-flex items-center justify-center gap-2 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition-colors"
                    >
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        <span>Complete Sale</span>
                    </button>

                    <!-- Cancel Pending Action -->
                    <div x-data="{ cancelOpen: false }">
                        <button
                            type="button"
                            @click="cancelOpen = true"
                            class="h-12 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-sm transition-colors"
                        >
                            <x-heroicon-o-x-circle class="w-5 h-5" />
                            <span>Cancel Sale</span>
                        </button>

                        <!-- Cancel Modal -->
                        <div
                            x-show="cancelOpen"
                            style="display: none;"
                            x-transition.opacity
                            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4"
                        >
                            <div
                                @click.outside="cancelOpen = false"
                                x-transition.scale
                                class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl text-left border border-gray-100"
                            >
                                <h3 class="text-lg font-bold text-gray-900 mb-1">
                                    Cancel Pending Sale
                                </h3>
                                <p class="text-xs text-gray-500 mb-4">
                                    Are you sure you want to cancel this pending sale? Please provide a reason for the record.
                                </p>

                                <form action="{{ route('sales.destroy', $sale) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <div class="mb-4">
                                        <label for="reason" class="block text-xs font-semibold text-gray-700 mb-1">Cancellation Reason</label>
                                        <textarea
                                            name="reason"
                                            id="reason"
                                            rows="3"
                                            class="block w-full text-sm border-gray-200 rounded-xl shadow-xs focus:border-brand focus:ring-brand"
                                            placeholder="Customer changed mind, incorrect items..."
                                            required
                                        ></textarea>
                                    </div>

                                    <div class="mt-5 flex justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="cancelOpen = false"
                                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                                        >
                                            Back
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-colors"
                                        >
                                            Confirm Cancellation
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($sale->status === \App\Enums\SaleStatus::COMPLETED)
                    <!-- Void / Cancel Action -->
                    <button
                        type="button"
                        @click="confirmAction('{{ route('sales.destroy', $sale) }}', 'DELETE', 'Void Sale', 'Are you sure you want to cancel (VOID) this sale? Stocks will be automatically returned to inventory.', 'Yes, Void Sale', 'bg-rose-600 hover:bg-rose-700 text-white')"
                        class="h-12 inline-flex items-center justify-center gap-1.5 px-4 rounded-xl text-rose-600 hover:bg-rose-50 border border-rose-200 font-bold text-xs transition-colors"
                    >
                        <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                        <span>Void Sale</span>
                    </button>
                @endif

                @if($sale->status === \App\Enums\SaleStatus::CANCELLED)
                    <!-- Restore to Pending -->
                    <button
                        type="button"
                        @click="confirmAction('{{ route('sales.restore', $sale) }}', 'PATCH', 'Restore Sale', 'Restore this sale to Pending status? You can then complete it again.', 'Restore to Pending', 'bg-gray-900 hover:bg-gray-800 text-white')"
                        class="h-12 inline-flex items-center justify-center gap-1.5 px-4 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs transition-colors"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                        <span>Restore to Pending</span>
                    </button>
                @endif

                <!-- Shared Confirmation Modal -->
                <x-modal name="confirmation-modal">
                    <div class="p-6" x-data="{ submitting: false }">
                        <h2 class="text-lg font-bold text-gray-900" x-text="modalTitle"></h2>
                        <p class="mt-2 text-xs text-gray-500 leading-relaxed" x-text="modalMessage"></p>

                        <div class="mt-6 flex justify-end gap-2">
                            <button
                                type="button"
                                x-on:click="$dispatch('close-modal', { name: 'confirmation-modal' })"
                                x-bind:disabled="submitting"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                            >
                                Back
                            </button>

                            <form :action="actionUrl" method="POST" @submit="submitting = true">
                                @csrf
                                <input type="hidden" name="_method" :value="actionMethod">

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold shadow-xs transition-all disabled:opacity-50"
                                    x-bind:class="confirmButtonClass"
                                    x-bind:disabled="submitting"
                                >
                                    <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="confirmButtonText"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </x-modal>

            </div>

        </div>
    </div>
</x-app-layout>
