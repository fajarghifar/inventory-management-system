<x-app-layout title="Reports Hub">
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-5">

            {{-- 1. Reports Hub Top Bar & Period Filter --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                        <span>Reports</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Business Assistant
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Understand your store performance, inventory value, and business profits at a glance.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    {{-- Period Dropdown --}}
                    <form method="GET" action="{{ route('reports.index') }}" class="flex items-center">
                        <div class="relative">
                            <select
                                name="period"
                                onchange="this.form.submit()"
                                class="h-10 pl-3 pr-8 text-xs font-bold bg-gray-50 hover:bg-gray-100 text-gray-800 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand cursor-pointer shadow-2xs transition-colors"
                            >
                                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="this_year" {{ $period === 'this_year' ? 'selected' : '' }}>This Year</option>
                            </select>
                        </div>
                    </form>

                    {{-- Print Report --}}
                    <a
                        href="{{ route('reports.print.overview', ['period' => $period]) }}"
                        target="_blank"
                        class="h-10 inline-flex items-center gap-1.5 px-3 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs shadow-2xs transition-all active:scale-95"
                        title="Print Reports Overview"
                    >
                        <x-heroicon-o-printer class="w-4 h-4 text-gray-500" />
                        <span class="hidden sm:inline">Print Report</span>
                    </a>

                    {{-- Export PDF --}}
                    <a
                        href="{{ route('reports.print.overview', ['period' => $period]) }}"
                        target="_blank"
                        class="h-10 inline-flex items-center gap-1.5 px-3.5 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-all active:scale-95"
                        title="Open Print & PDF Document"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span>Export PDF</span>
                    </a>
                </div>
            </div>

            {{-- =========================================================================
                 2. MOBILE FEATURED BANNER & QUICK METRICS TILES (Matching Image 3 Mobile)
                 ========================================================================= --}}
            <div class="block lg:hidden space-y-3">
                {{-- Featured Green Total Sales Card --}}
                <div class="bg-gradient-to-br from-brand-600 via-brand to-brand-800 text-white rounded-3xl p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between text-brand-100">
                        <span class="text-xs font-bold uppercase tracking-wider">Total Sales ({{ $overview['period_label'] }})</span>
                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center">
                            <x-heroicon-o-banknotes class="w-4 h-4 text-white" />
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-black font-mono tracking-tight text-white">
                            ETB {{ number_format($overview['total_sales'], 2) }}
                        </div>
                        <div class="flex items-center gap-2 mt-1.5 text-xs text-brand-100">
                            @if($overview['sales_growth'] !== null)
                                <span class="inline-flex items-center font-bold px-2 py-0.5 rounded-full {{ $overview['sales_growth'] >= 0 ? 'bg-white/20 text-white' : 'bg-rose-500/30 text-rose-100' }}">
                                    {{ $overview['sales_growth'] >= 0 ? '↑' : '↓' }} {{ abs($overview['sales_growth']) }}%
                                </span>
                            @endif
                            <span>{{ $overview['total_transactions'] }} completed sales</span>
                        </div>
                    </div>
                </div>

                {{-- 4 Quick Metric Tiles Grid --}}
                <div class="grid grid-cols-2 gap-2.5">
                    {{-- Tile 1: Sales Count --}}
                    <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-brand flex items-center justify-center shrink-0">
                            <x-heroicon-o-shopping-bag class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sales</span>
                            <span class="font-mono font-black text-sm text-gray-900 truncate block">
                                {{ number_format($overview['total_transactions']) }}
                            </span>
                        </div>
                    </div>

                    {{-- Tile 2: Total Products --}}
                    <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-heroicon-o-cube class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Products</span>
                            <span class="font-mono font-black text-sm text-gray-900 truncate block">
                                {{ number_format($overview['stock_valuation']['total_items']) }}
                            </span>
                        </div>
                    </div>

                    {{-- Tile 3: Low Stock --}}
                    <a href="{{ route('low-stock.index') }}" class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl {{ $overview['low_stock_count'] > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center shrink-0">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Low Stock</span>
                            <span class="font-mono font-black text-sm {{ $overview['low_stock_count'] > 0 ? 'text-amber-600' : 'text-gray-900' }} truncate block">
                                {{ number_format($overview['low_stock_count']) }}
                            </span>
                        </div>
                    </a>

                    {{-- Tile 4: Estimated Profit --}}
                    <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <x-heroicon-o-scale class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Profit</span>
                            <span class="font-mono font-black text-sm text-brand truncate block">
                                ETB {{ number_format($overview['estimated_profit'] / 1000, 1) }}K
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
                 3. DESKTOP 4 KPI SUMMARY CARDS (Matching Image 3 Desktop Top)
                 ========================================================================= --}}
            <div class="hidden lg:grid grid-cols-4 gap-4">
                {{-- Card 1: Total Sales --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs relative overflow-hidden group hover:border-brand/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Sales</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-brand flex items-center justify-center">
                            <x-heroicon-o-shopping-cart class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 font-mono tracking-tight mt-2.5">
                        ETB {{ number_format($overview['total_sales'], 2) }}
                    </div>
                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                        @if($overview['sales_growth'] !== null)
                            <span class="inline-flex items-center gap-0.5 font-bold px-2 py-0.5 rounded-full {{ $overview['sales_growth'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $overview['sales_growth'] >= 0 ? '↑' : '↓' }} {{ abs($overview['sales_growth']) }}%
                            </span>
                        @endif
                        <span>{{ $overview['period_label'] }}</span>
                    </div>
                </div>

                {{-- Card 2: Total Products --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs relative overflow-hidden group hover:border-brand/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Products</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <x-heroicon-o-cube class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 font-mono tracking-tight mt-2.5">
                        {{ number_format($overview['stock_valuation']['total_items']) }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-gray-500">
                        <span class="font-bold text-gray-800">{{ number_format($overview['stock_valuation']['total_units']) }}</span>
                        <span>physical units in stock</span>
                    </div>
                </div>

                {{-- Card 3: Low Stock --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs relative overflow-hidden group hover:border-brand/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Low Stock</span>
                        <div class="w-9 h-9 rounded-xl {{ $overview['low_stock_count'] > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="text-2xl font-black font-mono tracking-tight mt-2.5 {{ $overview['low_stock_count'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                        {{ number_format($overview['low_stock_count']) }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-gray-500">
                        <span>Need attention / reorder</span>
                    </div>
                </div>

                {{-- Card 4: Estimated Profit --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs relative overflow-hidden group hover:border-brand/40 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estimated Profit</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-brand flex items-center justify-center">
                            <x-heroicon-o-arrow-trending-up class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="text-2xl font-black text-brand font-mono tracking-tight mt-2.5">
                        ETB {{ number_format($overview['estimated_profit'], 2) }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-xs text-gray-500">
                        <span class="font-bold text-emerald-700">{{ number_format($overview['profit_margin'], 1) }}% margin</span>
                        <span>after product costs</span>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
                 4. TWO-PANEL CONTENT: SALES OVERVIEW (LEFT) + QUICK REPORTS (RIGHT)
                 ========================================================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                {{-- LEFT SECTION: CHARTS & RECENT ACTIVITY (8 cols on desktop) --}}
                <div class="lg:col-span-8 space-y-5">

                    {{-- Sales Overview Chart Card (Matching Image 3) --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-2xs space-y-4" x-data="{ chartMode: 'sales' }">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <x-heroicon-o-chart-bar class="w-5 h-5 text-brand" />
                                    <span>Sales Overview</span>
                                </h2>
                                <p class="text-xs text-gray-500">7-day performance trend across daily sales and product margins</p>
                            </div>

                            <div class="flex items-center gap-2 self-start sm:self-auto">
                                {{-- Mode Switcher Tabs --}}
                                <div class="inline-flex rounded-xl bg-gray-100 p-0.5 text-xs font-bold">
                                    <button
                                        type="button"
                                        @click="chartMode = 'sales'"
                                        class="px-3 py-1 rounded-lg transition-all"
                                        :class="chartMode === 'sales' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-900'"
                                    >
                                        Sales
                                    </button>
                                    <button
                                        type="button"
                                        @click="chartMode = 'profit'"
                                        class="px-3 py-1 rounded-lg transition-all"
                                        :class="chartMode === 'profit' ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-900'"
                                    >
                                        Profit
                                    </button>
                                </div>

                                <span class="text-xs font-mono font-bold text-brand bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    ETB {{ number_format($overview['total_sales'], 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Visual Trend Bar Chart --}}
                        <div class="pt-4">
                            @php
                                $maxTrendVal = max(1, $overview['sales_trend']->max('revenue') ?? 1);
                            @endphp
                            <div class="grid grid-cols-7 gap-2 sm:gap-4 items-end h-48 sm:h-56 pb-2 border-b border-gray-100">
                                @foreach($overview['sales_trend'] as $point)
                                    @php
                                        $heightPct = min(100, max(6, round(($point['revenue'] / $maxTrendVal) * 100)));
                                        $profitHeightPct = min(100, max(4, round(($point['profit'] / $maxTrendVal) * 100)));
                                    @endphp
                                    <div class="flex flex-col items-center h-full justify-end group relative">
                                        {{-- Tooltip Hover --}}
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-12 z-20 pointer-events-none bg-gray-900 text-white text-[10px] font-mono rounded-lg py-1 px-2 whitespace-nowrap shadow-md">
                                            <div>Sales: ETB {{ number_format($point['revenue'], 0) }}</div>
                                            <div class="text-emerald-300">Profit: ETB {{ number_format($point['profit'], 0) }}</div>
                                        </div>

                                        {{-- Bar Element --}}
                                        <div class="w-full max-w-[40px] flex items-end justify-center rounded-xl overflow-hidden bg-emerald-50 group-hover:bg-emerald-100 transition-colors" style="height: {{ $heightPct }}%;">
                                            <div
                                                class="w-full transition-all duration-300 rounded-t-xl"
                                                :class="chartMode === 'sales' ? 'bg-brand' : 'bg-emerald-400'"
                                                :style="'height: ' + (chartMode === 'sales' ? '100%' : '{{ round(($point['profit'] / max(1, $point['revenue'])) * 100) }}%')"
                                            ></div>
                                        </div>

                                        {{-- Label Day --}}
                                        <span class="text-[11px] font-bold text-gray-500 mt-2">
                                            {{ $point['day'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Recent Sales Table Card (Matching Image 3) --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                        <div class="p-4 sm:p-5 flex items-center justify-between border-b border-gray-100">
                            <div>
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <x-heroicon-o-receipt-percent class="w-5 h-5 text-gray-400" />
                                    <span>Recent Sales</span>
                                </h2>
                                <p class="text-xs text-gray-500">Latest completed customer orders</p>
                            </div>
                            <a
                                href="{{ route('sales.index') }}"
                                class="text-xs font-bold text-brand hover:text-brand-700 flex items-center gap-1"
                            >
                                <span>View All</span>
                                <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                            </a>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                                    <tr>
                                        <th class="px-4 py-3">Invoice</th>
                                        <th class="px-4 py-3">Date & Time</th>
                                        <th class="px-4 py-3">Cashier</th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($overview['recent_sales'] as $sale)
                                        <tr class="hover:bg-gray-50/60 transition-colors">
                                            <td class="px-4 py-3 font-mono font-bold text-brand">
                                                <a href="{{ route('sales.show', $sale) }}" class="hover:underline">
                                                    {{ $sale->invoice_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                                {{ $sale->sale_date ? $sale->sale_date->format('M d, Y · h:i A') : '-' }}
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $sale->creator?->name ?? 'Staff' }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                                ETB {{ number_format($sale->total, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    Completed
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1">
                                                    <a
                                                        href="{{ route('sales.show', $sale) }}"
                                                        class="p-1 text-gray-400 hover:text-brand hover:bg-brand-50 rounded-lg transition-colors"
                                                        title="View Details"
                                                    >
                                                        <x-heroicon-o-eye class="w-4 h-4" />
                                                    </a>
                                                    <a
                                                        href="{{ route('sales.receipt', $sale) }}"
                                                        target="_blank"
                                                        class="p-1 text-gray-400 hover:text-brand hover:bg-brand-50 rounded-lg transition-colors"
                                                        title="Print Receipt"
                                                    >
                                                        <x-heroicon-o-printer class="w-4 h-4" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                                No completed sales recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- RIGHT SECTION: QUICK REPORTS DIRECTORY (4 cols on desktop, full-width on mobile) --}}
                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div>
                                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                    <x-heroicon-o-bolt class="w-4 h-4 text-amber-500" />
                                    <span>Quick Reports</span>
                                </h2>
                                <p class="text-[11px] text-gray-400">Business insights & statements</p>
                            </div>
                        </div>

                        {{-- Report Cards Stack (Matching Image 3) --}}
                        <div class="space-y-2">
                            {{-- 1. Daily Sales Summary --}}
                            <a
                                href="{{ route('reports.sales') }}"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 hover:bg-emerald-50/70 border border-gray-100 hover:border-emerald-200 transition-all group select-none active:scale-98"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-brand flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <x-heroicon-o-shopping-cart class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 group-hover:text-brand transition-colors">Daily Sales Summary</h3>
                                        <p class="text-[11px] text-gray-400 truncate">See today's & daily sales</p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-brand group-hover:translate-x-0.5 transition-all shrink-0" />
                            </a>

                            {{-- 2. Stock Valuation --}}
                            <a
                                href="{{ route('reports.stock-valuation') }}"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 hover:bg-emerald-50/70 border border-gray-100 hover:border-emerald-200 transition-all group select-none active:scale-98"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <x-heroicon-o-archive-box class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 group-hover:text-brand transition-colors">Stock Valuation</h3>
                                        <p class="text-[11px] text-gray-400 truncate">Know your current stock value</p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-brand group-hover:translate-x-0.5 transition-all shrink-0" />
                            </a>

                            {{-- 3. Product Sales & Margins --}}
                            <a
                                href="{{ route('reports.sales') }}#products"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 hover:bg-emerald-50/70 border border-gray-100 hover:border-emerald-200 transition-all group select-none active:scale-98"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <x-heroicon-o-chart-bar class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 group-hover:text-brand transition-colors">Product Sales</h3>
                                        <p class="text-[11px] text-gray-400 truncate">Top selling products & margins</p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-brand group-hover:translate-x-0.5 transition-all shrink-0" />
                            </a>

                            {{-- 4. Profit & Loss Statement --}}
                            <a
                                href="{{ route('reports.profit-loss') }}"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 hover:bg-emerald-50/70 border border-gray-100 hover:border-emerald-200 transition-all group select-none active:scale-98"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <x-heroicon-o-scale class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 group-hover:text-brand transition-colors">Profit & Loss</h3>
                                        <p class="text-[11px] text-gray-400 truncate">Your complete profit overview</p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-brand group-hover:translate-x-0.5 transition-all shrink-0" />
                            </a>

                            {{-- 5. Slow-Moving Stock --}}
                            <a
                                href="{{ route('reports.slow-moving') }}"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 hover:bg-emerald-50/70 border border-gray-100 hover:border-emerald-200 transition-all group select-none active:scale-98"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <x-heroicon-o-clock class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 group-hover:text-brand transition-colors">Slow-Moving Stock</h3>
                                        <p class="text-[11px] text-gray-400 truncate">Products not selling / tied capital</p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-brand group-hover:translate-x-0.5 transition-all shrink-0" />
                            </a>

                            {{-- 6. Stock Movement Audit Trail --}}
                            <a
                                href="{{ route('stock.movements') }}"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50/70 hover:bg-emerald-50/70 border border-gray-100 hover:border-emerald-200 transition-all group select-none active:scale-98"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 group-hover:text-brand transition-colors">Stock Movements</h3>
                                        <p class="text-[11px] text-gray-400 truncate">Full inventory audit trail</p>
                                    </div>
                                </div>
                                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-brand group-hover:translate-x-0.5 transition-all shrink-0" />
                            </a>
                        </div>
                    </div>

                    {{-- Top Products Mini Card --}}
                    @if($overview['top_products']->isNotEmpty())
                        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs space-y-3">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Top Sellers ({{ $overview['period_label'] }})</h3>
                            <div class="space-y-2.5">
                                @foreach($overview['top_products'] as $idx => $prod)
                                    <div class="flex items-center justify-between gap-2 text-xs">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-5 text-center font-bold text-gray-400 font-mono">{{ $idx + 1 }}</span>
                                            <span class="font-bold text-gray-800 truncate" title="{{ $prod['name'] }}">{{ $prod['name'] }}</span>
                                        </div>
                                        <span class="font-mono font-bold text-brand shrink-0">
                                            {{ $prod['qty'] }} {{ $prod['unit'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
