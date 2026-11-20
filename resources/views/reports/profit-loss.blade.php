<x-app-layout title="Profit & Loss Statement">
    <div class="py-4 sm:py-6">
        <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 space-y-5">

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
                        <span>Profit & Loss</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $pnl['estimated_net_profit'] >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $pnl['estimated_net_profit'] >= 0 ? 'Profitable' : 'Net Loss' }}
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Statement from <strong class="text-gray-800">{{ $startDate->format('d M Y') }}</strong> to <strong class="text-gray-800">{{ $endDate->format('d M Y') }}</strong>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    {{-- Print / PDF Statement --}}
                    <a
                        href="{{ route('reports.print.profit-loss', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                        target="_blank"
                        class="h-10 inline-flex items-center gap-1.5 px-4 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-all active:scale-95"
                        title="Print statement or save as official PDF"
                    >
                        <x-heroicon-o-printer class="w-4 h-4" />
                        <span>Print Statement / PDF</span>
                    </a>
                </div>
            </div>

            {{-- 2. Period Filter Bar --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-2xs space-y-3">
                <form method="GET" action="{{ route('reports.profit-loss') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    {{-- Quick Period Presets --}}
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-bold text-gray-500 mr-1">Period:</span>
                        @php
                            $todayStr = now()->toDateString();
                            $thisMonthStart = now()->startOfMonth()->toDateString();
                            $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
                            $lastMonthEnd = now()->subMonth()->endOfMonth()->toDateString();
                            $thisYearStart = now()->startOfYear()->toDateString();

                            $currStart = $startDate->toDateString();
                            $currEnd = $endDate->toDateString();
                        @endphp
                        <a
                            href="{{ route('reports.profit-loss', ['start_date' => $thisMonthStart, 'end_date' => $todayStr]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $currStart === $thisMonthStart && $currEnd === $todayStr ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            This Month
                        </a>
                        <a
                            href="{{ route('reports.profit-loss', ['start_date' => $lastMonthStart, 'end_date' => $lastMonthEnd]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $currStart === $lastMonthStart && $currEnd === $lastMonthEnd ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            Last Month
                        </a>
                        <a
                            href="{{ route('reports.profit-loss', ['start_date' => $thisYearStart, 'end_date' => $todayStr]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $currStart === $thisYearStart && $currEnd === $todayStr ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            This Year
                        </a>
                    </div>

                    {{-- Custom Date Pickers --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <input
                                type="date"
                                name="start_date"
                                value="{{ $startDate->toDateString() }}"
                                class="h-9 px-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-700 bg-gray-50 focus:bg-white focus:ring-1 focus:ring-brand focus:border-brand"
                            />
                            <span class="text-xs text-gray-400">to</span>
                            <input
                                type="date"
                                name="end_date"
                                value="{{ $endDate->toDateString() }}"
                                class="h-9 px-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-700 bg-gray-50 focus:bg-white focus:ring-1 focus:ring-brand focus:border-brand"
                            />
                        </div>
                        <button
                            type="submit"
                            class="h-9 px-3.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition-colors active:scale-95"
                        >
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- 3. Four Core Financial Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Card 1: Net Sales Revenue --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Net Sales</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <x-heroicon-o-banknotes class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-emerald-700 font-mono tracking-tight mt-2">
                        @money($pnl['sales']['net_revenue'])
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        From <strong class="text-gray-800">{{ number_format($pnl['sales']['count']) }}</strong> sales
                    </p>
                </div>

                {{-- Card 2: Cost of Sold Items (COGS) --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Product Costs</span>
                        <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                            <x-heroicon-o-shopping-bag class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-gray-800 font-mono tracking-tight mt-2">
                        @money($pnl['cogs'])
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Supplier cost of items sold
                    </p>
                </div>

                {{-- Card 3: Gross Store Profit --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gross Profit</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">
                            {{ number_format($pnl['gross_margin'], 1) }}% margin
                        </span>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-gray-900 font-mono tracking-tight mt-2">
                        @money($pnl['gross_profit'])
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Revenue minus wholesale costs
                    </p>
                </div>

                {{-- Card 4: Estimated Net Business Profit --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Net Profit</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $pnl['estimated_net_profit'] >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ number_format($pnl['net_margin'], 1) }}% net
                        </span>
                    </div>
                    <div class="text-xl sm:text-2xl font-black {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }} font-mono tracking-tight mt-2">
                        @money($pnl['estimated_net_profit'])
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        After all expenses & bills
                    </p>
                </div>
            </div>

            {{-- 4. Shopkeeper Financial Breakdown (Clear 5-Step Statement) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">How Your Profit Was Calculated</h2>
                        <p class="text-xs text-gray-500">Step-by-step financial statement for {{ $storeName }}</p>
                    </div>
                    <div class="text-xs text-gray-400 font-medium">
                        Currency: <span class="font-bold text-gray-700">ETB (Ethiopian Birr)</span>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-6">

                    {{-- Step 1: Sales Income --}}
                    <div class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-brand/10 text-brand font-bold text-xs flex items-center justify-center">1</span>
                                <h3 class="text-sm font-bold text-gray-900">Sales Income (Revenue)</h3>
                            </div>
                            <span class="text-sm font-mono font-bold text-emerald-700">@money($pnl['sales']['net_revenue'])</span>
                        </div>
                        <p class="text-xs text-gray-500 pl-8">Total money brought in by customer sales during this period.</p>

                        <div class="pl-8 pt-2 space-y-1.5 text-xs">
                            <div class="flex justify-between py-1 text-gray-600 border-b border-gray-100/80">
                                <span>Gross Sales Subtotal ({{ $pnl['sales']['count'] }} transactions)</span>
                                <span class="font-mono text-gray-900">@money($pnl['sales']['gross_sales'])</span>
                            </div>
                            <div class="flex justify-between py-1 text-rose-600 border-b border-gray-100/80">
                                <span>Less: Customer Discounts & Reductions</span>
                                <span class="font-mono font-medium">- @money($pnl['sales']['discounts'])</span>
                            </div>
                            <div class="flex justify-between py-1.5 font-bold text-gray-900 bg-white px-2.5 rounded-lg border border-gray-100">
                                <span>Net Sales Revenue</span>
                                <span class="font-mono text-emerald-700">@money($pnl['sales']['net_revenue'])</span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Cost of Products Sold --}}
                    <div class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-brand/10 text-brand font-bold text-xs flex items-center justify-center">2</span>
                                <h3 class="text-sm font-bold text-gray-900">Less: Cost of Products Sold (Wholesale Cost)</h3>
                            </div>
                            <span class="text-sm font-mono font-bold text-gray-800">- @money($pnl['cogs'])</span>
                        </div>
                        <p class="text-xs text-gray-500 pl-8">
                            The exact wholesale amount you paid suppliers to purchase the items that customers bought.
                        </p>

                        <div class="pl-8 pt-2 space-y-1.5 text-xs">
                            <div class="flex justify-between py-1.5 font-bold text-gray-900 bg-white px-2.5 rounded-lg border border-gray-100">
                                <span>Total Product Cost of Goods Sold</span>
                                <span class="font-mono text-gray-800">@money($pnl['cogs'])</span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Gross Business Profit --}}
                    <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50/40 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-o-chart-bar class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm sm:text-base font-extrabold text-emerald-950 uppercase tracking-wide">Gross Store Profit</h3>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-600 text-white">
                                        {{ number_format($pnl['gross_margin'], 1) }}% Margin
                                    </span>
                                </div>
                                <p class="text-xs text-emerald-800 mt-0.5">
                                    Net Sales Revenue minus Wholesale Product Costs. This is what pays your store overhead.
                                </p>
                            </div>
                        </div>
                        <div class="text-right pl-11 sm:pl-0">
                            <span class="text-xl sm:text-2xl font-black font-mono text-emerald-800">@money($pnl['gross_profit'])</span>
                        </div>
                    </div>

                    {{-- Step 4: Operating Expenses --}}
                    <div class="rounded-xl border border-gray-100 bg-gray-50/30 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-brand/10 text-brand font-bold text-xs flex items-center justify-center">3</span>
                                <h3 class="text-sm font-bold text-gray-900">Less: Operating Expenses (Bills & Overhead)</h3>
                            </div>
                            <span class="text-sm font-mono font-bold text-rose-600">- @money($pnl['operating_expenses'])</span>
                        </div>
                        <p class="text-xs text-gray-500 pl-8">
                            Everyday business expenses like shop rent, employee wages, electricity, packaging, and repairs.
                        </p>

                        <div class="pl-8 pt-1 space-y-1.5 text-xs">
                            @forelse($pnl['expenses_by_category'] as $exp)
                                @if($exp['category'] !== 'Product Purchases')
                                    <div class="flex justify-between py-1.5 text-gray-700 border-b border-gray-100">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            <span>{{ $exp['category'] }}</span>
                                            <span class="text-[10px] text-gray-400">({{ $exp['count'] }} payment{{ $exp['count'] > 1 ? 's' : '' }})</span>
                                        </div>
                                        <span class="font-mono font-medium text-gray-900">@money($exp['total'])</span>
                                    </div>
                                @endif
                            @empty
                                <div class="py-2 text-xs text-gray-400 italic">
                                    No operational expenses recorded during this period.
                                </div>
                            @endforelse

                            <div class="flex justify-between py-1.5 font-bold text-gray-900 bg-white px-2.5 rounded-lg border border-gray-100 mt-2">
                                <span>Total Store Operating Expenses</span>
                                <span class="font-mono text-rose-600">@money($pnl['operating_expenses'])</span>
                            </div>
                        </div>

                        {{-- Reassurance Note --}}
                        <div class="ml-8 mt-2 p-2.5 rounded-lg bg-amber-50/60 border border-amber-200/80 text-[11px] text-amber-900 flex items-start gap-2">
                            <x-heroicon-o-shield-check class="w-4 h-4 text-amber-700 shrink-0 mt-0.5" />
                            <div>
                                <strong>Fair Accounting Guard:</strong> Supplier stock purchases are excluded from operating expenses above to prevent double-counting. Stock costs are accounted for in Step 2 when items sell.
                            </div>
                        </div>
                    </div>

                    {{-- Step 5: Final Net Business Profit Banner --}}
                    <div class="rounded-2xl border-2 {{ $pnl['estimated_net_profit'] >= 0 ? 'border-emerald-500 bg-emerald-50/60' : 'border-rose-500 bg-rose-50/60' }} p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl {{ $pnl['estimated_net_profit'] >= 0 ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }} flex items-center justify-center shrink-0">
                                @if($pnl['estimated_net_profit'] >= 0)
                                    <x-heroicon-o-check-circle class="w-6 h-6" />
                                @else
                                    <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base sm:text-lg font-black {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-950' : 'text-rose-950' }} uppercase tracking-wider">
                                        ESTIMATED NET BUSINESS PROFIT
                                    </h3>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $pnl['estimated_net_profit'] >= 0 ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                                        {{ number_format($pnl['net_margin'], 1) }}% Net Margin
                                    </span>
                                </div>
                                <p class="text-xs {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-800' : 'text-rose-800' }} mt-1">
                                    @if($pnl['estimated_net_profit'] >= 0)
                                        Gross Store Profit minus all store expenses. This is the real money you made!
                                    @else
                                        Operating expenses and product costs exceeded sales revenue by @money(abs($pnl['estimated_net_profit'])).
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-right pl-13 sm:pl-0">
                            <div class="text-2xl sm:text-3xl font-black font-mono {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">
                                @money($pnl['estimated_net_profit'])
                            </div>
                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Net Clean Earnings</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 5. Helpful Business Advice Card for Shop Owners --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-200/80 p-4 sm:p-5 flex flex-col sm:flex-row items-start gap-4">
                <div class="w-9 h-9 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                    <x-heroicon-o-light-bulb class="w-5 h-5" />
                </div>
                <div class="space-y-1 text-xs text-gray-600">
                    <span class="font-bold text-sm text-gray-900 block">Helpful Shopkeeper Tips:</span>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li><strong>Gross Margin ({{ number_format($pnl['gross_margin'], 1) }}%):</strong> Shows your markup on products. If low, negotiate better bulk supplier rates or adjust retail prices.</li>
                        <li><strong>Operating Expenses (@money($pnl['operating_expenses'])):</strong> Make sure fixed expenses (rent, electricity, transport) stay within a healthy percentage of your gross profit.</li>
                        <li><strong>Slow-Moving Inventory:</strong> Check the <a href="{{ route('reports.slow-moving') }}" class="font-bold text-brand hover:underline">Slow-Moving Report</a> to free up tied-up capital from stagnant stock.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
