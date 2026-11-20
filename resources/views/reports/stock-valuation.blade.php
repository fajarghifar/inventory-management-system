<x-app-layout title="Stock Valuation Report">
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-5" x-data="{ searchQuery: '', expandedProduct: null }">

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
                        <span>Stock Valuation</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            Inventory Worth
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        How much money is invested in your stock and what you can earn selling it.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    {{-- CSV Export --}}
                    <a
                        href="{{ route('reports.export.stock-valuation-csv') }}"
                        class="h-10 inline-flex items-center gap-1.5 px-3 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs shadow-2xs transition-all active:scale-95"
                        title="Download CSV spreadsheet"
                    >
                        <x-heroicon-o-document-arrow-down class="w-4 h-4 text-gray-500" />
                        <span>Export CSV</span>
                    </a>

                    {{-- Print / PDF Report --}}
                    <a
                        href="{{ route('reports.print.stock-valuation') }}"
                        target="_blank"
                        class="h-10 inline-flex items-center gap-1.5 px-3.5 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-all active:scale-95"
                        title="Print official document or save as PDF"
                    >
                        <x-heroicon-o-printer class="w-4 h-4" />
                        <span>Print Report / PDF</span>
                    </a>
                </div>
            </div>

            {{-- 2. Friendly Explanation Notice --}}
            <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 flex items-start gap-3 text-xs text-blue-900">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                <div class="space-y-0.5">
                    <span class="font-bold">Understanding Stock Value & Potential Profit:</span>
                    <p class="text-blue-800 leading-relaxed">
                        <strong>Money Spent on Stock</strong> is the cash you already paid suppliers for current items.
                        <strong>Potential Profit</strong> is what you will earn once all items sell, before other store expenses like rent, power, and wages.
                    </p>
                </div>
            </div>

            {{-- 3. Four Core Metric Cards (Friendly Language) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Card 1: Physical Units in Stock --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Physical Units</span>
                        <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                            <x-heroicon-o-archive-box class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono tracking-tight mt-2">
                        {{ number_format($valuation['total_units']) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Across <strong class="text-gray-800">{{ $valuation['total_items'] }}</strong> active products
                    </p>
                </div>

                {{-- Card 2: Money Spent on Stock (Purchase Cost) --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Money Spent</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <x-heroicon-o-banknotes class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono tracking-tight mt-2">
                        ETB {{ number_format($valuation['total_cost_valuation'], 2) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Cash invested in current stock
                    </p>
                </div>

                {{-- Card 3: Potential Sales Value --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Potential Sales</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-brand flex items-center justify-center">
                            <x-heroicon-o-shopping-bag class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-brand font-mono tracking-tight mt-2">
                        ETB {{ number_format($valuation['total_retail_valuation'], 2) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        If all current stock is sold
                    </p>
                </div>

                {{-- Card 4: Potential Gross Profit --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Potential Profit</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-brand flex items-center justify-center">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-brand font-mono tracking-tight mt-2">
                        ETB {{ number_format($valuation['projected_profit'], 2) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <strong class="text-emerald-700">{{ number_format($valuation['projected_margin'], 1) }}%</strong> expected gross margin
                    </p>
                </div>
            </div>

            {{-- 4. Stock Breakdown by Category (Visual Share Bars) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5 text-gray-400" />
                            <span>Stock by Category</span>
                        </h2>
                        <p class="text-xs text-gray-500">Distribution of inventory capital across departments</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @php
                        $maxCatValue = max(1, $byCategory->max('cost_value') ?? 1);
                    @endphp
                    @foreach($byCategory as $cat)
                        @php
                            $sharePct = min(100, round(($cat['cost_value'] / max(1, $valuation['total_cost_valuation'])) * 100));
                        @endphp
                        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-100 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-bold text-gray-900 truncate" title="{{ $cat['name'] }}">
                                    {{ $cat['name'] }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white border border-gray-200 text-gray-600 shrink-0">
                                    {{ $cat['products_count'] }} products
                                </span>
                            </div>

                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between text-gray-500">
                                    <span>Units on Hand:</span>
                                    <span class="font-mono font-bold text-gray-800">{{ number_format($cat['total_units']) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Purchasing Cost:</span>
                                    <span class="font-mono font-bold text-gray-800">ETB {{ number_format($cat['cost_value'], 2) }}</span>
                                </div>
                                <div class="flex justify-between text-brand font-semibold">
                                    <span>Potential Sales:</span>
                                    <span class="font-mono font-bold">ETB {{ number_format($cat['retail_value'], 2) }}</span>
                                </div>
                            </div>

                            {{-- Visual Share Bar --}}
                            <div>
                                <div class="w-full h-2 rounded-full bg-gray-200 overflow-hidden">
                                    <div class="h-full bg-brand rounded-full" style="width: {{ $sharePct }}%;"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                                    <span>Portfolio share: {{ $sharePct }}%</span>
                                    <span class="text-emerald-700 font-bold">+ETB {{ number_format($cat['potential_profit'], 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 5. Product Valuation Details (Desktop Table + Mobile Cards) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <x-heroicon-o-cube class="w-5 h-5 text-gray-400" />
                            <span>Product Valuation Details</span>
                        </h2>
                        <p class="text-xs text-gray-500">Individual unit holding, costs, and profit projections</p>
                    </div>

                    {{-- Search Filter --}}
                    <div class="w-full sm:w-72">
                        <div class="relative">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                            <input
                                type="text"
                                x-model="searchQuery"
                                placeholder="Filter products..."
                                class="w-full h-9 pl-9 pr-3 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand focus:border-brand"
                            />
                        </div>
                    </div>
                </div>

                {{-- DESKTOP TABLE VIEW (>= 1024px) --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">On Hand</th>
                                <th class="px-4 py-3 text-right">Cost / Unit</th>
                                <th class="px-4 py-3 text-right">Selling / Unit</th>
                                <th class="px-4 py-3 text-right">Total Cost</th>
                                <th class="px-4 py-3 text-right">Potential Sales</th>
                                <th class="px-4 py-3 text-right">Projected Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($valuation['products'] as $product)
                                @php
                                    $costVal = $product->quantity * $product->purchase_price;
                                    $retailVal = $product->quantity * $product->selling_price;
                                    $profitVal = $retailVal - $costVal;
                                    $isLow = $product->quantity <= $product->min_stock;
                                @endphp
                                <tr
                                    class="hover:bg-gray-50/60 transition-colors"
                                    x-show="searchQuery === '' || '{{ strtolower(addslashes($product->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($product->sku) }}'.includes(searchQuery.toLowerCase())"
                                >
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-900">{{ $product->name }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-400 text-[11px]">
                                        {{ $product->sku ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $product->category?->name ?? 'Uncategorized' }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold font-mono {{ $isLow ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $product->quantity }} {{ $product->unit?->symbol }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-gray-700 whitespace-nowrap">
                                        ETB {{ number_format($product->purchase_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-gray-900 font-semibold whitespace-nowrap">
                                        ETB {{ number_format($product->selling_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-gray-700 whitespace-nowrap">
                                        ETB {{ number_format($costVal, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-brand whitespace-nowrap">
                                        ETB {{ number_format($retailVal, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700 whitespace-nowrap">
                                        ETB {{ number_format($profitVal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE EXPANDABLE CARDS (< 1024px) --}}
                <div class="block lg:hidden divide-y divide-gray-100 p-3 space-y-3">
                    @foreach($valuation['products'] as $product)
                        @php
                            $costVal = $product->quantity * $product->purchase_price;
                            $retailVal = $product->quantity * $product->selling_price;
                            $profitVal = $retailVal - $costVal;
                            $isLow = $product->quantity <= $product->min_stock;
                        @endphp
                        <div
                            class="bg-gray-50/60 rounded-xl p-3.5 border border-gray-100 space-y-2.5 transition-all"
                            x-show="searchQuery === '' || '{{ strtolower(addslashes($product->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($product->sku) }}'.includes(searchQuery.toLowerCase())"
                        >
                            {{-- Product Title & Quantity --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-gray-900 leading-snug truncate" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h3>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $product->category?->name ?? 'Uncategorized' }} · SKU: {{ $product->sku ?: '-' }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold font-mono shrink-0 {{ $isLow ? 'bg-amber-100 text-amber-800' : 'bg-white border border-gray-200 text-gray-700' }}">
                                    {{ $product->quantity }} {{ $product->unit?->symbol }}
                                </span>
                            </div>

                            {{-- Price & Values Grid --}}
                            <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-gray-100">
                                <div>
                                    <span class="text-[10px] text-gray-400 block uppercase">Cost / Unit</span>
                                    <span class="font-mono font-semibold text-gray-700">ETB {{ number_format($product->purchase_price, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-gray-400 block uppercase">Selling / Unit</span>
                                    <span class="font-mono font-bold text-gray-900">ETB {{ number_format($product->selling_price, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-gray-400 block uppercase">Total Cost</span>
                                    <span class="font-mono font-semibold text-gray-700">ETB {{ number_format($costVal, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-gray-400 block uppercase">Potential Sales</span>
                                    <span class="font-mono font-black text-brand">ETB {{ number_format($retailVal, 2) }}</span>
                                </div>
                            </div>

                            {{-- Potential Profit Footer --}}
                            <div class="pt-2 border-t border-dashed border-gray-200 flex justify-between items-center text-xs">
                                <span class="text-[11px] font-bold text-gray-500">Projected Profit:</span>
                                <span class="font-mono font-black text-emerald-700">
                                    ETB {{ number_format($profitVal, 2) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
