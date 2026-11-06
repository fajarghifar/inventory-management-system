<x-app-layout title="Stock Valuation Report">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="text-muted-foreground hover:text-foreground text-sm flex items-center gap-1">
                        &larr; Reports Hub
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-foreground leading-tight mt-1">
                    {{ __('Stock Valuation & Category Report') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    Valuation of on-hand inventory across cost and retail pricing.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <button onclick="window.print()" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                    <x-heroicon-o-printer class="h-4 w-4" />
                    Print Report
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Total Physical Units</span>
                    <div class="text-2xl font-bold mt-1">{{ number_format($valuation['total_units']) }}</div>
                    <span class="text-xs text-muted-foreground">{{ $valuation['total_items'] }} active products</span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Total Cost Valuation (COGS)</span>
                    <div class="text-2xl font-bold mt-1 text-slate-800">@money($valuation['total_cost_valuation'])</div>
                    <span class="text-xs text-muted-foreground">Total capital tied in stock</span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Total Retail Valuation</span>
                    <div class="text-2xl font-bold mt-1 text-blue-700">@money($valuation['total_retail_valuation'])</div>
                    <span class="text-xs text-muted-foreground">Expected revenue at selling price</span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Potential Profit & Margin</span>
                    <div class="text-2xl font-bold mt-1 text-emerald-600">@money($valuation['projected_profit'])</div>
                    <span class="text-xs font-semibold text-emerald-600">{{ number_format($valuation['projected_margin'], 1) }}% projected gross margin</span>
                </div>
            </div>

            <!-- Stock by Category -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-base text-foreground">Stock Breakdown by Category</h3>
                        <p class="text-xs text-muted-foreground">Aggregated inventory holdings and values per department</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-muted/50 border-b text-xs uppercase text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">Products</th>
                                <th class="px-4 py-3 text-center">Units on Hand</th>
                                <th class="px-4 py-3 text-right">Cost Value</th>
                                <th class="px-4 py-3 text-right">Retail Value</th>
                                <th class="px-4 py-3 text-right">Projected Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($byCategory as $cat)
                                <tr class="hover:bg-muted/20">
                                    <td class="px-4 py-3 font-semibold text-foreground">{{ $cat['name'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $cat['products_count'] }}</td>
                                    <td class="px-4 py-3 text-center font-medium">{{ number_format($cat['total_units']) }}</td>
                                    <td class="px-4 py-3 text-right">@money($cat['cost_value'])</td>
                                    <td class="px-4 py-3 text-right text-blue-600 font-medium">@money($cat['retail_value'])</td>
                                    <td class="px-4 py-3 text-right text-emerald-600 font-bold">@money($cat['potential_profit'])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-muted-foreground">No category data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Products Table -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30">
                    <h3 class="font-bold text-base text-foreground">Product Valuation Detail</h3>
                    <p class="text-xs text-muted-foreground">Individual item quantities, margins, and valuation</p>
                </div>
                <div class="overflow-x-auto max-h-[500px]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-muted/50 border-b text-xs uppercase text-muted-foreground sticky top-0 bg-background z-10">
                            <tr>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">On Hand</th>
                                <th class="px-4 py-3 text-right">Cost / Unit</th>
                                <th class="px-4 py-3 text-right">Selling / Unit</th>
                                <th class="px-4 py-3 text-right">Total Cost</th>
                                <th class="px-4 py-3 text-right">Total Retail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($valuation['products'] as $product)
                                <tr class="hover:bg-muted/20">
                                    <td class="px-4 py-2 font-medium">{{ $product->name }}</td>
                                    <td class="px-4 py-2 text-xs font-mono text-muted-foreground">{{ $product->sku }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-center font-bold {{ $product->quantity <= $product->min_stock ? 'text-red-600' : '' }}">
                                        {{ $product->quantity }} {{ $product->unit?->symbol }}
                                    </td>
                                    <td class="px-4 py-2 text-right">@money($product->purchase_price)</td>
                                    <td class="px-4 py-2 text-right">@money($product->selling_price)</td>
                                    <td class="px-4 py-2 text-right font-medium">@money($product->quantity * $product->purchase_price)</td>
                                    <td class="px-4 py-2 text-right font-medium text-blue-600">@money($product->quantity * $product->selling_price)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

