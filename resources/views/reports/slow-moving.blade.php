<x-app-layout title="Slow-Moving & Dead Stock Report">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="text-muted-foreground hover:text-foreground text-sm flex items-center gap-1">
                        &larr; Reports Hub
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-foreground leading-tight mt-1">
                    {{ __('Slow-Moving & Dead Stock Monitoring') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    Products with zero sales or inventory movement over a defined period.
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

            <!-- Filter Controls -->
            <div class="p-4 bg-card rounded-xl border shadow-sm print:hidden">
                <form method="GET" action="{{ route('reports.slow-moving') }}" class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="text-sm font-semibold text-foreground">Inactivity Threshold:</span>
                        <p class="text-xs text-muted-foreground">Show items with no recorded sales in at least:</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('reports.slow-moving', ['days' => 30]) }}" class="px-2.5 sm:px-3 py-1.5 rounded-md text-xs font-medium border transition-colors {{ $days == 30 ? 'bg-primary text-primary-foreground border-primary' : 'bg-background hover:bg-accent' }}">
                                30 Days
                            </a>
                            <a href="{{ route('reports.slow-moving', ['days' => 60]) }}" class="px-2.5 sm:px-3 py-1.5 rounded-md text-xs font-medium border transition-colors {{ $days == 60 ? 'bg-primary text-primary-foreground border-primary' : 'bg-background hover:bg-accent' }}">
                                60 Days
                            </a>
                            <a href="{{ route('reports.slow-moving', ['days' => 90]) }}" class="px-2.5 sm:px-3 py-1.5 rounded-md text-xs font-medium border transition-colors {{ $days == 90 ? 'bg-primary text-primary-foreground border-primary' : 'bg-background hover:bg-accent' }}">
                                90 Days
                            </a>
                            <a href="{{ route('reports.slow-moving', ['days' => 180]) }}" class="px-2.5 sm:px-3 py-1.5 rounded-md text-xs font-medium border transition-colors {{ $days == 180 ? 'bg-primary text-primary-foreground border-primary' : 'bg-background hover:bg-accent' }}">
                                180 Days
                            </a>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="number" name="days" value="{{ $days }}" min="1" max="1000" class="h-8 w-16 sm:w-20 rounded-md border border-input text-xs text-center" placeholder="Custom" />
                            <button type="submit" class="h-8 px-2.5 rounded-md bg-muted text-xs font-medium hover:bg-accent border">Go</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Key Insight Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Slow-Moving Items ({{ $days }}+ Days)</span>
                    <div class="text-2xl font-bold mt-1 text-amber-600">{{ $totalCount }} products</div>
                    <span class="text-xs text-muted-foreground">Items with zero sales since {{ now()->subDays($days)->format('d M Y') }}</span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Tied-Up Working Capital</span>
                    <div class="text-2xl font-bold mt-1 text-rose-600">@money($totalCapital)</div>
                    <span class="text-xs text-muted-foreground">Cash trapped in non-performing inventory</span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Recommended Action</span>
                    <div class="text-sm font-semibold text-foreground mt-2">Promotions & Clearance</div>
                    <p class="text-xs text-muted-foreground mt-1">Review purchasing orders, apply POS discounts, or bundle with fast movers.</p>
                </div>
            </div>

            <!-- Slow Moving Products Table -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30">
                    <h3 class="font-bold text-base text-foreground">Inactive Inventory Detail (Threshold: {{ $days }} Days)</h3>
                    <p class="text-xs text-muted-foreground">Sorted by days since last recorded sale</p>
                </div>
                <div class="overflow-x-auto max-h-[600px]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-muted/50 border-b text-xs uppercase text-muted-foreground sticky top-0 bg-background z-10">
                            <tr>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">Stock on Hand</th>
                                <th class="px-4 py-3 text-right">Cost / Unit</th>
                                <th class="px-4 py-3 text-right">Tied-Up Capital</th>
                                <th class="px-4 py-3 text-center">Last Sold / Created</th>
                                <th class="px-4 py-3 text-center">Days Inactive</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($slowMoving as $item)
                                <tr class="hover:bg-muted/20">
                                    <td class="px-4 py-2 font-medium text-foreground">
                                        {{ $item['name'] }}
                                        @if($item['never_sold'])
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-rose-100 text-rose-800 ml-1">Never Sold</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-xs font-mono text-muted-foreground">{{ $item['sku'] }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $item['category'] }}</td>
                                    <td class="px-4 py-2 text-center font-bold text-foreground">{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                                    <td class="px-4 py-2 text-right">@money($item['purchase_price'])</td>
                                    <td class="px-4 py-2 text-right font-bold text-rose-600">@money($item['tied_up_capital'])</td>
                                    <td class="px-4 py-2 text-center text-xs text-muted-foreground">
                                        {{ $item['last_activity_date']->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $item['days_inactive'] >= 90 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $item['days_inactive'] }} days
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-muted-foreground">
                                        <x-heroicon-o-check-circle class="w-10 h-10 text-emerald-500 mx-auto mb-2" />
                                        <p class="font-medium text-foreground">Excellent! No slow-moving items found exceeding {{ $days }} days.</p>
                                        <p class="text-xs text-muted-foreground mt-1">All products in inventory have recorded sales within this timeframe.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

