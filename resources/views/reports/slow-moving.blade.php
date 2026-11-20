<x-app-layout title="Slow-Moving Stock Report">
    <div class="py-4 sm:py-6" x-data="{ searchQuery: '' }">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-5">

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
                        <span>Slow-Moving Stock</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Tied-Up Capital
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Items with zero customer sales for <strong class="text-gray-800">{{ $days }}+ days</strong> locking up cash.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                    {{-- CSV Export --}}
                    <a
                        href="{{ route('reports.export.slow-moving-csv', ['days' => $days]) }}"
                        class="h-10 inline-flex items-center gap-1.5 px-3 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs shadow-2xs transition-all active:scale-95"
                        title="Download CSV spreadsheet"
                    >
                        <x-heroicon-o-document-arrow-down class="w-4 h-4 text-gray-500" />
                        <span>Export CSV</span>
                    </a>

                    {{-- Print / PDF Report --}}
                    <a
                        href="{{ route('reports.print.slow-moving', ['days' => $days]) }}"
                        target="_blank"
                        class="h-10 inline-flex items-center gap-1.5 px-3.5 rounded-xl bg-brand hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-all active:scale-95"
                        title="Print report or save as official PDF"
                    >
                        <x-heroicon-o-printer class="w-4 h-4" />
                        <span>Print Report / PDF</span>
                    </a>
                </div>
            </div>

            {{-- 2. Friendly Notice --}}
            <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 text-xs text-amber-900">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div class="space-y-0.5">
                    <span class="font-bold">Why this matters for your shop:</span>
                    <p class="text-amber-800 leading-relaxed">
                        When products sit on shelves without selling, the money you spent to buy them is trapped. Clearing these items—even at a small discount—puts cash back in your hand to buy fast-moving, high-demand products.
                    </p>
                </div>
            </div>

            {{-- 3. Inactivity Threshold Filter & Search Bar --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-2xs space-y-3">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    {{-- Inactivity Presets --}}
                    <form method="GET" action="{{ route('reports.slow-moving') }}" class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-gray-500 mr-1">No sales in:</span>
                        <a
                            href="{{ route('reports.slow-moving', ['days' => 30]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $days == 30 ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            30 Days
                        </a>
                        <a
                            href="{{ route('reports.slow-moving', ['days' => 60]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $days == 60 ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            60 Days
                        </a>
                        <a
                            href="{{ route('reports.slow-moving', ['days' => 90]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $days == 90 ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            90 Days
                        </a>
                        <a
                            href="{{ route('reports.slow-moving', ['days' => 180]) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $days == 180 ? 'bg-brand text-white shadow-2xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        >
                            180 Days
                        </a>

                        <div class="flex items-center gap-1 ml-1">
                            <input
                                type="number"
                                name="days"
                                value="{{ $days }}"
                                min="1"
                                max="1000"
                                class="h-8 w-16 text-center rounded-lg border border-gray-200 text-xs font-medium bg-gray-50 focus:bg-white focus:ring-1 focus:ring-brand focus:border-brand"
                                placeholder="Custom"
                            />
                            <button
                                type="submit"
                                class="h-8 px-2.5 rounded-lg bg-gray-900 hover:bg-black text-white text-xs font-bold transition-colors"
                            >
                                Go
                            </button>
                        </div>
                    </form>

                    {{-- Search Input --}}
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        </div>
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Search slow-moving item..."
                            class="w-full h-9 pl-9 pr-3 rounded-xl border border-gray-200 text-xs bg-gray-50 focus:bg-white focus:ring-1 focus:ring-brand focus:border-brand text-gray-900 placeholder:text-gray-400"
                        />
                    </div>
                </div>
            </div>

            {{-- 4. Three Core Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                {{-- Card 1: Tied-Up Cash --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tied-Up Working Capital</span>
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                            <x-heroicon-o-lock-closed class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-rose-600 font-mono tracking-tight mt-2">
                        @money($totalCapital)
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Cash trapped in non-selling products
                    </p>
                </div>

                {{-- Card 2: Slow-Moving Products --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Stagnant Products</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <x-heroicon-o-archive-box-x-mark class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-amber-700 font-mono tracking-tight mt-2">
                        {{ number_format($totalCount) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Different products with no sales in {{ $days }}+ days
                    </p>
                </div>

                {{-- Card 3: Total Dormant Units --}}
                @php
                    $dormantUnits = $slowMoving->sum('quantity');
                @endphp
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Dormant Physical Units</span>
                        <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                            <x-heroicon-o-cube class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 font-mono tracking-tight mt-2">
                        {{ number_format($dormantUnits) }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Total items sitting in your warehouse & shelves
                    </p>
                </div>
            </div>

            {{-- 5. Recommended Actions for Shopkeeper --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-4 sm:p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <x-heroicon-o-sparkles class="w-4 h-4" />
                    </div>
                    <h2 class="text-sm font-bold text-gray-900">Recommended Steps to Unlock Cash:</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="font-bold text-gray-900 block mb-1">1. Discount or Clearance</span>
                        <p class="text-gray-500">Offer a 10%–20% discount on these products to liquidate stock quickly and recover your cash.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="font-bold text-gray-900 block mb-1">2. Bundle with Best Sellers</span>
                        <p class="text-gray-500">Pair these slow items with your most popular fast-selling products at an attractive combo price.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="font-bold text-gray-900 block mb-1">3. Stop Re-Ordering</span>
                        <p class="text-gray-500">Do not reorder these items from suppliers until customer demand picks up and stock clears.</p>
                    </div>
                </div>
            </div>

            {{-- 6. Products Listing (Responsive Desktop Table + Mobile Cards) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Stagnant Inventory Detail</h2>
                        <p class="text-xs text-gray-500">Items sorted by days inactive</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                        {{ count($slowMoving) }} items
                    </span>
                </div>

                @if($slowMoving->isEmpty())
                    <div class="p-8 sm:p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <x-heroicon-o-check-circle class="w-7 h-7" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Great news! No slow-moving stock found</h3>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto mt-1">
                            All products in your catalog have recorded sales within the last {{ $days }} days. Your inventory is turning over healthily.
                        </p>
                    </div>
                @else
                    {{-- Desktop View: Clean Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50/75 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-3 py-3">Category</th>
                                    <th class="px-3 py-3 text-center">Stock on Hand</th>
                                    <th class="px-3 py-3 text-right">Cost / Unit</th>
                                    <th class="px-4 py-3 text-right">Tied-Up Cash</th>
                                    <th class="px-3 py-3 text-center">Last Sold / Created</th>
                                    <th class="px-4 py-3 text-center">Inactivity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($slowMoving as $item)
                                    @php
                                        $searchHaystack = strtolower($item['name'] . ' ' . $item['sku'] . ' ' . $item['category']);
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50/60 transition-colors"
                                        x-show="!searchQuery || '{{ addslashes($searchHaystack) }}'.includes(searchQuery.toLowerCase())"
                                    >
                                        {{-- Product Name & SKU --}}
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900 text-sm">
                                                {{ $item['name'] }}
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                @if($item['sku'])
                                                    <span class="font-mono text-[10px] text-gray-400">{{ $item['sku'] }}</span>
                                                @endif
                                                @if($item['never_sold'])
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                        Never Sold
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Category --}}
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-medium text-[11px]">
                                                {{ $item['category'] }}
                                            </span>
                                        </td>

                                        {{-- Stock on Hand --}}
                                        <td class="px-3 py-3 text-center">
                                            <span class="font-mono font-bold text-gray-900 text-xs">
                                                {{ number_format($item['quantity']) }}
                                            </span>
                                            <span class="text-gray-400 text-[10px] block">{{ $item['unit'] }}</span>
                                        </td>

                                        {{-- Wholesale Cost / Unit --}}
                                        <td class="px-3 py-3 text-right font-mono text-gray-600">
                                            @money($item['purchase_price'])
                                        </td>

                                        {{-- Tied-Up Capital --}}
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-mono font-black text-rose-600 text-sm">
                                                @money($item['tied_up_capital'])
                                            </span>
                                        </td>

                                        {{-- Last Activity --}}
                                        <td class="px-3 py-3 text-center text-gray-500 font-mono text-[11px]">
                                            {{ $item['last_activity_date'] ? $item['last_activity_date']->format('d M Y') : '—' }}
                                        </td>

                                        {{-- Inactivity Badge --}}
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $item['days_inactive'] >= 90 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $item['days_inactive'] }} days
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile View: Touch-Friendly Card Stack --}}
                    <div class="block md:hidden divide-y divide-gray-100">
                        @foreach($slowMoving as $item)
                            @php
                                $searchHaystack = strtolower($item['name'] . ' ' . $item['sku'] . ' ' . $item['category']);
                            @endphp
                            <div
                                class="p-4 space-y-3 hover:bg-gray-50/50 transition-colors"
                                x-show="!searchQuery || '{{ addslashes($searchHaystack) }}'.includes(searchQuery.toLowerCase())"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-0.5">
                                        <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $item['name'] }}</h3>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-[10px] text-gray-400 font-mono">{{ $item['sku'] ?? 'No SKU' }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-[10px] text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">{{ $item['category'] }}</span>
                                            @if($item['never_sold'])
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                                                    Never Sold
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full {{ $item['days_inactive'] >= 90 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $item['days_inactive'] }}d inactive
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-gray-50 text-xs">
                                    <div>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">In Stock</span>
                                        <span class="font-mono font-bold text-gray-900 text-xs">{{ $item['quantity'] }} {{ $item['unit'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">Cost/Unit</span>
                                        <span class="font-mono text-gray-700 text-xs">@money($item['purchase_price'])</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-rose-500 uppercase tracking-wider block font-bold">Tied Cash</span>
                                        <span class="font-mono font-black text-rose-600 text-xs">@money($item['tied_up_capital'])</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-gray-400 pt-0.5">
                                    <span>Last Activity: <strong class="text-gray-600">{{ $item['last_activity_date'] ? $item['last_activity_date']->format('d M Y') : '—' }}</strong></span>
                                    <a
                                        href="{{ route('sales.create') }}"
                                        class="text-brand font-bold hover:underline"
                                    >
                                        Create Discount Sale &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
