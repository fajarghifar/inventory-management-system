<div>
    <div class="space-y-5">

        <!-- 1. Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-xs">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900">Sales & Invoices</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Manage and track your transactions, cash flow, and retail receipts.</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                @can('admin')
                    <button
                        type="button"
                        wire:click="exportCsv"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors shadow-2xs"
                        title="Export transactions to CSV"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-gray-500" />
                        <span class="hidden sm:inline">Export CSV</span>
                    </button>
                @endcan

                <a
                    href="{{ route('sales.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs sm:text-sm font-bold bg-brand hover:bg-brand-700 text-white rounded-xl shadow-sm transition-all active:scale-95"
                >
                    <x-heroicon-o-plus class="w-4 h-4" />
                    <span>Create Sale</span>
                    <span class="hidden md:inline-block text-[10px] bg-white/20 px-1.5 py-0.5 rounded text-white font-mono ml-0.5">F1</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Dynamic with Selected Period) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Sales / Revenue -->
            <div class="p-4 rounded-2xl border border-gray-100 bg-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500">Total Sales</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-banknotes class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-black text-emerald-700 mt-2 font-mono tracking-tight">
                    ETB {{ number_format($stats['total_revenue'], 2) }}
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5">
                    Completed · {{ $stats['period_label'] }}
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="p-4 rounded-2xl border border-gray-100 bg-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500">Transactions</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-heroicon-o-receipt-percent class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-black text-gray-900 mt-2 font-mono">
                    {{ number_format($stats['total_transactions']) }}
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5">
                    Orders · {{ $stats['period_label'] }}
                </div>
            </div>

            <!-- Average Sale (Ticket Size) -->
            <div class="p-4 rounded-2xl border border-gray-100 bg-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500">Average Sale</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <x-heroicon-o-calculator class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-black text-gray-900 mt-2 font-mono tracking-tight">
                    ETB {{ number_format($stats['avg_sale'], 2) }}
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5">
                    Per completed order
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="p-4 rounded-2xl border border-gray-100 bg-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500">Completed</span>
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-black text-teal-700 mt-2 font-mono">
                    {{ number_format($stats['completed_count']) }}
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5">
                    Fulfilled transactions
                </div>
            </div>
        </div>

        <!-- 3. Toolbar (Period Selector, Search, Status, Payment, Sorting) -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
            <!-- Quick Date Period Pills & Calendar Picker -->
            <div class="flex flex-wrap items-center gap-1.5 pb-1 text-xs">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mr-1 whitespace-nowrap">Period:</span>
                @php
                    $periods = [
                        'all' => 'All Time',
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'this_month' => 'This Month',
                    ];
                @endphp
                @foreach($periods as $key => $label)
                    <button
                        type="button"
                        wire:click="setPeriod('{{ $key }}')"
                        class="px-3 py-1.5 rounded-lg font-medium whitespace-nowrap transition-colors {{ $datePeriod === $key && empty($customRange) ? 'bg-brand text-white shadow-xs font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach

                <!-- Interactive Calendar Date Range Picker -->
                <div
                    x-data="{
                        picker: null,
                        init() {
                            const initFlatpickr = () => {
                                if (typeof window.flatpickr === 'function' && this.$refs.pickerInput) {
                                    this.picker = window.flatpickr(this.$refs.pickerInput, {
                                        mode: 'range',
                                        dateFormat: 'Y-m-d',
                                        altInput: false,
                                        defaultDate: @js($customRange ? explode(' to ', $customRange) : null),
                                        onClose: (selectedDates, dateStr) => {
                                            if (dateStr) {
                                                $wire.setCustomRange(dateStr);
                                            }
                                        }
                                    });
                                }
                            };

                            if (typeof window.flatpickr === 'function') {
                                initFlatpickr();
                            } else {
                                window.addEventListener('load', initFlatpickr);
                            }
                        },
                        openCalendar() {
                            if (this.picker) {
                                this.picker.open();
                            } else if (this.$refs.pickerInput) {
                                this.$refs.pickerInput.focus();
                            }
                        }
                    }"
                    class="relative inline-flex items-center"
                >
                    <input
                        x-ref="pickerInput"
                        type="text"
                        class="sr-only"
                        tabindex="-1"
                        aria-hidden="true"
                    />

                    <button
                        type="button"
                        @click="openCalendar()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-medium whitespace-nowrap transition-colors {{ $datePeriod === 'custom' && $customRange ? 'bg-brand text-white shadow-xs font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200/80' }}"
                        title="Click to select a custom date or date range from calendar"
                    >
                        <x-heroicon-o-calendar class="w-3.5 h-3.5 {{ $datePeriod === 'custom' && $customRange ? 'text-white' : 'text-gray-500' }}" />
                        @if($datePeriod === 'custom' && $customRange)
                            <span>{{ $stats['period_label'] }}</span>
                            <span
                                wire:click.stop="clearCustomRange"
                                class="ml-1 p-0.5 rounded-full hover:bg-white/20 text-white cursor-pointer"
                                title="Clear date filter"
                            >
                                <x-heroicon-o-x-mark class="w-3 h-3" />
                            </span>
                        @else
                            <span>Calendar</span>
                        @endif
                    </button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-1 border-t border-gray-100">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search invoice #, cashier, or customer..."
                        class="w-full h-11 pl-11 pr-10 text-sm bg-gray-50/60 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                    />
                    @if($search !== '')
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1"
                        >
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    @endif
                </div>

                <!-- Filters & Sorting Dropdowns (No Emojis!) -->
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                    <!-- Status Filter -->
                    <select
                        wire:model.live="statusFilter"
                        class="h-11 text-xs font-medium bg-gray-50/60 border border-gray-200 rounded-xl px-3 pr-8 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                    >
                        <option value="all">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    <!-- Payment Method Filter -->
                    <select
                        wire:model.live="paymentFilter"
                        class="h-11 text-xs font-medium bg-gray-50/60 border border-gray-200 rounded-xl px-3 pr-8 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                    >
                        <option value="all">All Payments</option>
                        <option value="cash">Cash</option>
                        <option value="transfer">Bank Transfer</option>
                    </select>

                    <!-- Sort By -->
                    <select
                        wire:model.live="sortBy"
                        class="h-11 text-xs font-medium bg-gray-50/60 border border-gray-200 rounded-xl px-3 pr-8 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                    >
                        <option value="latest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="total_desc">Highest Amount</option>
                        <option value="total_asc">Lowest Amount</option>
                    </select>

                    <!-- Reset Filters Button -->
                    @if($search !== '' || $statusFilter !== 'all' || $datePeriod !== 'all' || !empty($customRange) || $paymentFilter !== 'all' || $sortBy !== 'latest')
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="h-11 px-3 inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors whitespace-nowrap"
                            title="Reset all filters"
                        >
                            <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Transactions List -->
        <!-- DESKTOP VIEW (>= 1024px): Data Table -->
        <div class="hidden lg:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm divide-y divide-gray-100">
                    <thead class="bg-gray-50/75 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 pl-5 pr-3">Invoice</th>
                            <th scope="col" class="px-3 py-3.5">Date & Time</th>
                            <th scope="col" class="px-3 py-3.5">Cashier</th>
                            <th scope="col" class="px-3 py-3.5">Items</th>
                            <th scope="col" class="px-3 py-3.5">Payment</th>
                            <th scope="col" class="px-3 py-3.5 text-right">Total (ETB)</th>
                            <th scope="col" class="px-3 py-3.5 text-center">Status</th>
                            <th scope="col" class="py-3.5 pl-3 pr-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <!-- Invoice -->
                                <td class="py-3.5 pl-5 pr-3">
                                    <a
                                        href="{{ route('sales.show', $sale) }}"
                                        class="font-mono font-bold text-brand hover:text-brand-700 hover:underline inline-flex items-center gap-1.5"
                                    >
                                        <x-heroicon-o-document-text class="w-4 h-4 text-brand-500" />
                                        <span>{{ $sale->invoice_number ?: '#' . $sale->id }}</span>
                                    </a>
                                    @if($sale->customer && $sale->customer->name !== 'Guest')
                                        <div class="text-[11px] text-gray-500 mt-0.5">
                                            {{ $sale->customer->name }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Date & Time -->
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">
                                        {{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : '-' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 font-mono">
                                        {{ $sale->sale_date ? $sale->sale_date->format('h:i A') : '' }}
                                    </div>
                                </td>

                                <!-- Cashier -->
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-brand-50 text-brand-700 font-bold text-[10px] flex items-center justify-center ring-1 ring-brand-200">
                                            {{ strtoupper(substr($sale->creator?->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-xs font-medium text-gray-800">
                                            {{ $sale->creator?->name ?? 'System' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Items -->
                                <td class="px-3 py-3.5">
                                    @php
                                        $itemsCount = $sale->items->count();
                                        $totalQty = $sale->items->sum('quantity');
                                    @endphp
                                    <div class="inline-flex items-center gap-1 text-xs text-gray-700 font-medium bg-gray-50 px-2 py-0.5 rounded-md border border-gray-100">
                                        <x-heroicon-o-shopping-bag class="w-3.5 h-3.5 text-gray-400" />
                                        <span>{{ $itemsCount }} {{ \Illuminate\Support\Str::plural('item', $itemsCount) }}</span>
                                        <span class="text-gray-400">({{ $totalQty }} pcs)</span>
                                    </div>
                                </td>

                                <!-- Payment Method -->
                                <td class="px-3 py-3.5 whitespace-nowrap">
                                    @if($sale->payment_method === \App\Enums\PaymentMethod::CASH)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <x-heroicon-o-banknotes class="w-3 h-3" />
                                            Cash
                                        </span>
                                    @elseif($sale->payment_method === \App\Enums\PaymentMethod::TRANSFER)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                            <x-heroicon-o-credit-card class="w-3 h-3" />
                                            Transfer
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Total (ETB) -->
                                <td class="px-3 py-3.5 text-right whitespace-nowrap">
                                    <span class="font-mono font-bold text-gray-900">
                                        ETB {{ number_format($sale->total, 2) }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-3 py-3.5 text-center whitespace-nowrap">
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
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 pl-3 pr-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a
                                            href="{{ route('sales.show', $sale) }}"
                                            class="p-1.5 text-gray-500 hover:text-brand hover:bg-brand-50 rounded-lg transition-colors"
                                            title="View Details"
                                        >
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>

                                        <a
                                            href="{{ route('sales.receipt', $sale) }}"
                                            target="_blank"
                                            class="p-1.5 text-gray-500 hover:text-brand hover:bg-brand-50 rounded-lg transition-colors"
                                            title="Print Thermal Receipt"
                                        >
                                            <x-heroicon-o-receipt-percent class="w-4 h-4" />
                                        </a>

                                        <a
                                            href="{{ route('sales.print', $sale) }}"
                                            target="_blank"
                                            class="p-1.5 text-gray-500 hover:text-brand hover:bg-brand-50 rounded-lg transition-colors"
                                            title="Print A4 Invoice"
                                        >
                                            <x-heroicon-o-printer class="w-4 h-4" />
                                        </a>

                                        @can('admin')
                                            @if($sale->status === \App\Enums\SaleStatus::CANCELLED)
                                                <button
                                                    type="button"
                                                    wire:click="deleteSale({{ $sale->id }})"
                                                    wire:confirm="Are you sure you want to PERMANENTLY delete cancelled invoice {{ $sale->invoice_number }}?"
                                                    class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                    title="Permanently Delete Sale"
                                                >
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-receipt-percent class="w-10 h-10 text-gray-300 stroke-1" />
                                        <p class="mt-2 text-sm font-semibold text-gray-700">No transactions found</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Try adjusting your filters or date period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE / TABLET VIEW (< 1024px): Kyte AI / SumUp-inspired Retail Cards -->
        <div class="lg:hidden space-y-3">
            @forelse($sales as $sale)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-4 space-y-3">
                    <!-- Top Row: Invoice # + Status Badge -->
                    <div class="flex items-center justify-between">
                        <a
                            href="{{ route('sales.show', $sale) }}"
                            class="font-mono font-bold text-sm text-brand hover:text-brand-700 flex items-center gap-1.5"
                        >
                            <x-heroicon-o-document-text class="w-4 h-4 text-brand-500" />
                            <span>{{ $sale->invoice_number ?: '#' . $sale->id }}</span>
                        </a>

                        @if($sale->status === \App\Enums\SaleStatus::COMPLETED)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Completed
                            </span>
                        @elseif($sale->status === \App\Enums\SaleStatus::PENDING)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Pending
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Cancelled
                            </span>
                        @endif
                    </div>

                    <!-- Middle Row: Cashier, Date, Customer -->
                    <div class="text-xs text-gray-500 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-700 font-bold text-[9px] flex items-center justify-center">
                                    {{ strtoupper(substr($sale->creator?->name ?? 'U', 0, 2)) }}
                                </span>
                                <span class="font-medium text-gray-700">{{ $sale->creator?->name ?? 'System' }}</span>
                            </span>
                            <span class="text-gray-400 font-mono text-[11px]">
                                {{ $sale->sale_date ? $sale->sale_date->format('M d, Y · h:i A') : '-' }}
                            </span>
                        </div>

                        @if($sale->customer && $sale->customer->name !== 'Guest')
                            <div class="text-[11px] text-gray-500">
                                Customer: <span class="font-medium text-gray-800">{{ $sale->customer->name }}</span>
                            </div>
                        @endif

                        <!-- Items Preview -->
                        <div class="pt-1 text-[11px] text-gray-600">
                            @php
                                $itemsCount = $sale->items->count();
                                $totalQty = $sale->items->sum('quantity');
                                $firstItem = $sale->items->first()?->product?->name;
                            @endphp
                            <span>{{ $itemsCount }} {{ \Illuminate\Support\Str::plural('item', $itemsCount) }} ({{ $totalQty }} pcs)</span>
                            @if($firstItem)
                                <span class="text-gray-400">· {{ \Illuminate\Support\Str::limit($firstItem, 25) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Row: Payment Method + Total Amount -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <div>
                            @if($sale->payment_method === \App\Enums\PaymentMethod::CASH)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <x-heroicon-o-banknotes class="w-3 h-3" />
                                    Cash
                                </span>
                            @elseif($sale->payment_method === \App\Enums\PaymentMethod::TRANSFER)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    <x-heroicon-o-credit-card class="w-3 h-3" />
                                    Transfer
                                </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="text-base font-black text-gray-900 font-mono">
                                ETB {{ number_format($sale->total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Touch Actions (min 44px height for mobile ergonomics) -->
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                        <a
                            href="{{ route('sales.show', $sale) }}"
                            class="h-11 inline-flex items-center justify-center gap-1.5 px-3 rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-100 font-bold text-xs transition-colors"
                        >
                            <x-heroicon-o-eye class="w-4 h-4" />
                            <span>View Details</span>
                        </a>

                        <a
                            href="{{ route('sales.receipt', $sale) }}"
                            target="_blank"
                            class="h-11 inline-flex items-center justify-center gap-1.5 px-3 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs transition-colors"
                        >
                            <x-heroicon-o-receipt-percent class="w-4 h-4" />
                            <span>Thermal</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-8 text-center text-gray-500">
                    <x-heroicon-o-receipt-percent class="w-10 h-10 text-gray-300 stroke-1 mx-auto" />
                    <p class="mt-2 text-sm font-semibold text-gray-700">No transactions found</p>
                    <p class="text-xs text-gray-400 mt-0.5">Try adjusting your filters or date period.</p>
                </div>
            @endforelse
        </div>

        <!-- 5. Pagination & Results Count -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-xs text-gray-500">
                Showing <span class="font-bold text-gray-800">{{ $sales->firstItem() ?? 0 }}</span>
                to <span class="font-bold text-gray-800">{{ $sales->lastItem() ?? 0 }}</span>
                of <span class="font-bold text-gray-800">{{ $sales->total() }}</span> transactions
            </div>
            <div>
                {{ $sales->links() }}
            </div>
        </div>

    </div>
</div>
