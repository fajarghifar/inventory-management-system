<x-app-layout title="Sales & Performance Reports">
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-5" x-data="{ activeTab: 'daily', productSearch: '', productSort: 'revenue_desc' }">

            {{-- 1. Page Header & Actions --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-gray-500 hover:text-brand flex items-center gap-1 transition-colors">
                            <x-heroicon-o-arrow-left class="w-3.5 h-3.5" />
                            <span>Reports Hub</span>
                        </a>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                        <span>Sales Performance</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Cashier & Products
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Day-by-day transactional income, product gross margins, and team performance.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    {{-- CSV Export --}}
                    <a
                        href="{{ route('reports.export.sales-csv', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                        class="h-10 inline-flex items-center gap-1.5 px-3 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs shadow-2xs transition-all active:scale-95"
                        title="Download CSV spreadsheet"
                    >
                        <x-heroicon-o-document-arrow-down class="w-4 h-4 text-gray-500" />
                        <span>Export CSV</span>
                    </a>

                    {{-- Print / PDF Report --}}
                    <a
                        href="{{ route('reports.print.sales', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                        target="_blank"
                        class="h-10 inline-flex items-center gap-1.5 px-3.5 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-all active:scale-95"
                        title="Print official document or save as PDF"
                    >
                        <x-heroicon-o-printer class="w-4 h-4" />
                        <span>Print Report / PDF</span>
                    </a>
                </div>
            </div>

            {{-- 2. Date Range Filter Card --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-2xs">
                <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Date Period:</span>
                        <div class="flex items-center gap-1.5">
                            <input
                                type="date"
                                name="start_date"
                                value="{{ $startDate->toDateString() }}"
                                class="h-9 rounded-xl border border-gray-200 text-xs px-3 font-mono font-medium focus:ring-1 focus:ring-brand focus:border-brand"
                            />
                            <span class="text-xs text-gray-400 font-semibold">to</span>
                            <input
                                type="date"
                                name="end_date"
                                value="{{ $endDate->toDateString() }}"
                                class="h-9 rounded-xl border border-gray-200 text-xs px-3 font-mono font-medium focus:ring-1 focus:ring-brand focus:border-brand"
                            />
                        </div>
                        <button
                            type="submit"
                            class="h-9 px-4 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs shadow-2xs transition-all active:scale-95"
                        >
                            Apply Filter
                        </button>
                    </div>

                    <div class="text-xs text-gray-500">
                        Reporting range: <strong class="text-gray-800">{{ $startDate->format('M d, Y') }}</strong> &ndash; <strong class="text-gray-800">{{ $endDate->format('M d, Y') }}</strong>
                    </div>
                </form>
            </div>

            {{-- 3. Four Core Metric Summary Cards (Friendly Language) --}}
            @php
                $totalGross = $dailySummary->sum('gross');
                $totalDiscount = $dailySummary->sum('discount');
                $totalNet = $totalRevenue;
                $avgOrder = $totalTransactions > 0 ? $totalNet / $totalTransactions : 0;
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Card 1: Net Sales Revenue --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Net Money Received</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-brand flex items-center justify-center">
                            <x-heroicon-o-banknotes class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-brand font-mono tracking-tight mt-2">
                        ETB {{ number_format($totalNet, 2) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Gross sales minus customer discounts
                    </p>
                </div>

                {{-- Card 2: Completed Orders --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Completed Orders</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <x-heroicon-o-receipt-percent class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono tracking-tight mt-2">
                        {{ number_format($totalTransactions) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Total customer sales processed
                    </p>
                </div>

                {{-- Card 3: Discounts Given --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Discounts Given</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <x-heroicon-o-tag class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-rose-600 font-mono tracking-tight mt-2">
                        ETB {{ number_format($totalDiscount, 2) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Price reductions given to customers
                    </p>
                </div>

                {{-- Card 4: Average Order Value --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Average Order</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <x-heroicon-o-calculator class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono tracking-tight mt-2">
                        ETB {{ number_format($avgOrder, 2) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Average basket size per customer
                    </p>
                </div>
            </div>

            {{-- 4. Navigation Sub-Tabs: Daily Sales, Product Margins, Staff Performance --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                <div class="border-b border-gray-100 px-4 sm:px-5 flex items-center gap-2 overflow-x-auto no-scrollbar">
                    <button
                        type="button"
                        @click="activeTab = 'daily'"
                        class="py-3.5 px-3 border-b-2 font-bold text-xs sm:text-sm whitespace-nowrap transition-colors"
                        :class="activeTab === 'daily' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-900'"
                    >
                        Daily Sales Summary ({{ $dailySummary->count() }} days)
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'products'"
                        class="py-3.5 px-3 border-b-2 font-bold text-xs sm:text-sm whitespace-nowrap transition-colors"
                        :class="activeTab === 'products' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-900'"
                    >
                        Product Sales & Margins ({{ $productSummary->count() }} items)
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'staff'"
                        class="py-3.5 px-3 border-b-2 font-bold text-xs sm:text-sm whitespace-nowrap transition-colors"
                        :class="activeTab === 'staff' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-900'"
                    >
                        Sales by Staff / Cashier ({{ $staffSummary->count() }} members)
                    </button>
                </div>

                {{-- TAB 1: DAILY SALES SUMMARY --}}
                <div x-show="activeTab === 'daily'" class="p-4 sm:p-5 space-y-4">
                    {{-- Visual Day Bars --}}
                    @php
                        $maxDayRevenue = max(1, $dailySummary->max('net') ?? 1);
                    @endphp
                    @if($dailySummary->isNotEmpty())
                        <div class="bg-gray-50/70 rounded-xl p-4 border border-gray-100 space-y-2">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Daily Trend Visualizer</span>
                            <div class="space-y-2">
                                @foreach($dailySummary->take(10) as $day)
                                    @php
                                        $barPct = min(100, max(4, round(($day['net'] / $maxDayRevenue) * 100)));
                                    @endphp
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="w-28 font-semibold text-gray-700 shrink-0 truncate">
                                            {{ Carbon\Carbon::parse($day['date'])->format('D, M d') }}
                                        </span>
                                        <div class="flex-1 h-3 rounded-full bg-gray-200 overflow-hidden">
                                            <div class="h-full bg-brand rounded-full transition-all" style="width: {{ $barPct }}%;"></div>
                                        </div>
                                        <span class="w-24 text-right font-mono font-bold text-gray-900 shrink-0">
                                            ETB {{ number_format($day['net'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Table for Desktop, Cards for Mobile --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3 text-center">Completed Orders</th>
                                    <th class="px-4 py-3 text-right">Gross Sales</th>
                                    <th class="px-4 py-3 text-right">Discounts</th>
                                    <th class="px-4 py-3 text-right">Net Sales Revenue</th>
                                    <th class="px-4 py-3 text-right">Average Order</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($dailySummary as $day)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">
                                            {{ Carbon\Carbon::parse($day['date'])->format('D, M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-mono font-semibold text-gray-700">
                                            {{ $day['count'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-700 whitespace-nowrap">
                                            ETB {{ number_format($day['gross'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-rose-600 whitespace-nowrap">
                                            @if($day['discount'] > 0)
                                                - ETB {{ number_format($day['discount'], 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-brand whitespace-nowrap">
                                            ETB {{ number_format($day['net'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-600 whitespace-nowrap">
                                            ETB {{ number_format($day['avg_order'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                            No sales recorded during this date period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: PRODUCT SALES & GROSS MARGINS --}}
                <div x-show="activeTab === 'products'" class="p-4 sm:p-5 space-y-4" style="display: none;">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Product Profit Contribution</h3>
                            <p class="text-xs text-gray-500">Gross profit and margin earned per product line</p>
                        </div>
                        <div class="w-full sm:w-64">
                            <input
                                type="text"
                                x-model="productSearch"
                                placeholder="Filter products..."
                                class="w-full h-9 px-3 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand"
                            />
                        </div>
                    </div>

                    {{-- Desktop Table View (Clean, No Sticky overlap bugs) --}}
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Product Name</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3 text-center">Units Sold</th>
                                    <th class="px-4 py-3 text-right">Sales Revenue</th>
                                    <th class="px-4 py-3 text-right">Product Cost</th>
                                    <th class="px-4 py-3 text-right">Gross Profit</th>
                                    <th class="px-4 py-3 text-right">Profit Margin %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($productSummary as $item)
                                    @php
                                        $margin = $item['margin_percent'];
                                        $marginClass = match(true) {
                                            $margin >= 25 => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            $margin >= 10 => 'bg-blue-50 text-blue-800 border-blue-200',
                                            $margin >= 0 => 'bg-amber-50 text-amber-800 border-amber-200',
                                            default => 'bg-rose-50 text-rose-800 border-rose-200',
                                        };
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50/60 transition-colors"
                                        x-show="productSearch === '' || '{{ strtolower(addslashes($item['product_name'])) }}'.includes(productSearch.toLowerCase()) || '{{ strtolower($item['sku']) }}'.includes(productSearch.toLowerCase())"
                                    >
                                        <td class="px-4 py-3 font-bold text-gray-900">{{ $item['product_name'] }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-400 text-[11px]">{{ $item['sku'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $item['category'] }}</td>
                                        <td class="px-4 py-3 text-center font-mono font-bold whitespace-nowrap">
                                            {{ $item['quantity_sold'] }} {{ $item['unit'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                            ETB {{ number_format($item['revenue'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-500 whitespace-nowrap">
                                            ETB {{ number_format($item['cost'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-brand whitespace-nowrap">
                                            ETB {{ number_format($item['gross_profit'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold font-mono border {{ $marginClass }}">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                            No product sales recorded in this timeframe.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Product Cards View (< 1024px) --}}
                    <div class="block lg:hidden divide-y divide-gray-100 space-y-3">
                        @foreach($productSummary as $item)
                            @php
                                $margin = $item['margin_percent'];
                                $marginClass = match(true) {
                                    $margin >= 25 => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    $margin >= 10 => 'bg-blue-50 text-blue-800 border-blue-200',
                                    $margin >= 0 => 'bg-amber-50 text-amber-800 border-amber-200',
                                    default => 'bg-rose-50 text-rose-800 border-rose-200',
                                };
                            @endphp
                            <div
                                class="bg-gray-50/60 rounded-xl p-3.5 border border-gray-100 space-y-2.5"
                                x-show="productSearch === '' || '{{ strtolower(addslashes($item['product_name'])) }}'.includes(productSearch.toLowerCase()) || '{{ strtolower($item['sku']) }}'.includes(productSearch.toLowerCase())"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 truncate" title="{{ $item['product_name'] }}">{{ $item['product_name'] }}</h4>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $item['category'] }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold font-mono border {{ $marginClass }}">
                                        {{ number_format($margin, 1) }}% margin
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-gray-100">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block uppercase">Units Sold</span>
                                        <span class="font-mono font-bold text-gray-800">{{ $item['quantity_sold'] }} {{ $item['unit'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 block uppercase">Revenue</span>
                                        <span class="font-mono font-bold text-gray-900">ETB {{ number_format($item['revenue'], 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 block uppercase">Product Cost</span>
                                        <span class="font-mono text-gray-600">ETB {{ number_format($item['cost'], 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 block uppercase">Gross Profit</span>
                                        <span class="font-mono font-black text-brand">ETB {{ number_format($item['gross_profit'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- TAB 3: SALES BY STAFF MEMBER --}}
                <div x-show="activeTab === 'staff'" class="p-4 sm:p-5 space-y-4" style="display: none;">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Cashier / Staff Member</th>
                                    <th class="px-4 py-3">Username</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3 text-center">Orders Handled</th>
                                    <th class="px-4 py-3 text-right">Total Net Revenue</th>
                                    <th class="px-4 py-3 text-right">Average Order</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($staffSummary as $staff)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="px-4 py-3 font-bold text-gray-900 whitespace-nowrap">
                                            {{ $staff['staff_name'] }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-gray-400 text-[11px]">
                                            {{ $staff['username'] }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                                {{ $staff['role'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center font-mono font-bold text-gray-800">
                                            {{ $staff['transactions_count'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-brand whitespace-nowrap">
                                            ETB {{ number_format($staff['total_revenue'], 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-600 whitespace-nowrap">
                                            ETB {{ number_format($staff['avg_order_value'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                            No sales recorded by staff in this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
