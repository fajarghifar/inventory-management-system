<x-app-layout title="Sales & Cashier Performance Reports">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="text-muted-foreground hover:text-foreground text-sm flex items-center gap-1">
                        &larr; Reports Hub
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-foreground leading-tight mt-1">
                    {{ __('Sales Performance & Cashier Summaries') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    Comprehensive sales breakdown by day, product line, and staff member.
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

            <!-- Date Range Selector -->
            <div class="p-4 bg-card rounded-xl border shadow-sm print:hidden">
                <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-foreground">Date Range:</span>
                        <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="h-9 rounded-md border border-input text-sm px-3" />
                        <span class="text-muted-foreground">to</span>
                        <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="h-9 rounded-md border border-input text-sm px-3" />
                        <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90">Filter</button>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        Showing sales from <strong class="text-foreground">{{ $startDate->format('d M Y') }}</strong> to <strong class="text-foreground">{{ $endDate->format('d M Y') }}</strong>
                    </div>
                </form>
            </div>

            <!-- Top Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Total Sales Revenue</span>
                    <div class="text-2xl font-bold mt-1 text-emerald-600">@money($totalRevenue)</div>
                    <span class="text-xs text-muted-foreground">Net received across completed sales</span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Completed Transactions</span>
                    <div class="text-2xl font-bold mt-1 text-foreground">{{ number_format($totalTransactions) }}</div>
                    <span class="text-xs text-muted-foreground">
                        Avg Ticket: @money($totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0)
                    </span>
                </div>

                <div class="p-4 rounded-xl border bg-card shadow-sm">
                    <span class="text-xs font-medium text-muted-foreground">Products Sold</span>
                    <div class="text-2xl font-bold mt-1 text-blue-600">{{ $productSummary->count() }} items</div>
                    <span class="text-xs text-muted-foreground">{{ number_format($productSummary->sum('quantity_sold')) }} total units sold</span>
                </div>
            </div>

            <!-- Staff / Cashier Sales Table -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30">
                    <h3 class="font-bold text-base text-foreground">Sales Performance by Staff / Cashier</h3>
                    <p class="text-xs text-muted-foreground">Activity attributed to each individual account</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-muted/50 border-b text-xs uppercase text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3">Staff Member</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3 text-center">Transactions</th>
                                <th class="px-4 py-3 text-right">Total Revenue</th>
                                <th class="px-4 py-3 text-right">Avg Transaction</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($staffSummary as $staff)
                                <tr class="hover:bg-muted/20">
                                    <td class="px-4 py-3 font-semibold text-foreground">{{ $staff['staff_name'] }}</td>
                                    <td class="px-4 py-3 text-xs font-mono text-muted-foreground">{{ $staff['username'] }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted">
                                            {{ $staff['role'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold">{{ $staff['transactions_count'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-600">@money($staff['total_revenue'])</td>
                                    <td class="px-4 py-3 text-right text-muted-foreground">@money($staff['avg_order_value'])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-muted-foreground">No sales recorded for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Sales Summary Table -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30">
                    <h3 class="font-bold text-base text-foreground">Daily Sales Summary</h3>
                    <p class="text-xs text-muted-foreground">Day-by-day transactional totals and discounts</p>
                </div>
                <div class="overflow-x-auto max-h-[400px]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-muted/50 border-b text-xs uppercase text-muted-foreground sticky top-0 bg-background z-10">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3 text-center">Transactions</th>
                                <th class="px-4 py-3 text-right">Gross Sales</th>
                                <th class="px-4 py-3 text-right">Discounts</th>
                                <th class="px-4 py-3 text-right">Net Revenue</th>
                                <th class="px-4 py-3 text-right">Avg Order</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($dailySummary as $day)
                                <tr class="hover:bg-muted/20">
                                    <td class="px-4 py-2 font-medium">{{ Carbon\Carbon::parse($day['date'])->format('D, d M Y') }}</td>
                                    <td class="px-4 py-2 text-center">{{ $day['count'] }}</td>
                                    <td class="px-4 py-2 text-right">@money($day['gross'])</td>
                                    <td class="px-4 py-2 text-right text-rose-600">@money($day['discount'])</td>
                                    <td class="px-4 py-2 text-right font-bold text-emerald-600">@money($day['net'])</td>
                                    <td class="px-4 py-2 text-right text-muted-foreground">@money($day['avg_order'])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-muted-foreground">No daily sales for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Product Sales Summary Table -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30">
                    <h3 class="font-bold text-base text-foreground">Product Sales & Gross Margins</h3>
                    <p class="text-xs text-muted-foreground">Profit contribution per product line</p>
                </div>
                <div class="overflow-x-auto max-h-[450px]">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-muted/50 border-b text-xs uppercase text-muted-foreground sticky top-0 bg-background z-10">
                            <tr>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">Qty Sold</th>
                                <th class="px-4 py-3 text-right">Revenue</th>
                                <th class="px-4 py-3 text-right">Cost (COGS)</th>
                                <th class="px-4 py-3 text-right">Gross Profit</th>
                                <th class="px-4 py-3 text-right">Margin %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($productSummary as $item)
                                <tr class="hover:bg-muted/20">
                                    <td class="px-4 py-2 font-medium">{{ $item['product_name'] }}</td>
                                    <td class="px-4 py-2 text-xs font-mono text-muted-foreground">{{ $item['sku'] }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $item['category'] }}</td>
                                    <td class="px-4 py-2 text-center font-bold">{{ $item['quantity_sold'] }} {{ $item['unit'] }}</td>
                                    <td class="px-4 py-2 text-right">@money($item['revenue'])</td>
                                    <td class="px-4 py-2 text-right text-muted-foreground">@money($item['cost'])</td>
                                    <td class="px-4 py-2 text-right font-bold text-emerald-600">@money($item['gross_profit'])</td>
                                    <td class="px-4 py-2 text-right font-semibold {{ $item['margin_percent'] < 15 ? 'text-amber-600' : 'text-emerald-600' }}">
                                        {{ number_format($item['margin_percent'], 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-4 text-center text-muted-foreground">No product sales recorded in this timeframe.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

