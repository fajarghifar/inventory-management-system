<x-app-layout title="Profit & Loss Statement">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="text-muted-foreground hover:text-foreground text-sm flex items-center gap-1">
                        &larr; Reports Hub
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-foreground leading-tight mt-1">
                    {{ __('Profit & Loss Statement (P&L)') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    Management statement evaluating gross margins, operational expenses, and net profit.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <button onclick="window.print()" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                    <x-heroicon-o-printer class="h-4 w-4" />
                    Print Statement
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Card -->
            <div class="p-4 bg-card rounded-xl border shadow-sm print:hidden">
                <form method="GET" action="{{ route('reports.profit-loss') }}" class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-foreground">Statement Period:</span>
                        <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="h-9 rounded-md border border-input text-sm px-3" />
                        <span class="text-muted-foreground">to</span>
                        <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="h-9 rounded-md border border-input text-sm px-3" />
                        <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90">Update Period</button>
                    </div>
                </form>
            </div>

            <!-- Printable Statement Sheet -->
            <div class="bg-card border rounded-xl shadow-md p-6 sm:p-10 space-y-8 print:border-none print:shadow-none print:p-0">

                <!-- Statement Header -->
                <div class="text-center border-b pb-6 space-y-1">
                    <h1 class="text-2xl font-extrabold tracking-tight text-foreground">{{ $storeName }}</h1>
                    @if($storeAddress)
                        <p class="text-xs text-muted-foreground">{{ $storeAddress }}</p>
                    @endif
                    <h2 class="text-lg font-bold text-foreground uppercase tracking-wider pt-2">Statement of Profit & Loss</h2>
                    <p class="text-xs text-muted-foreground">
                        For the period from <strong class="text-foreground">{{ $startDate->format('d F Y') }}</strong> to <strong class="text-foreground">{{ $endDate->format('d F Y') }}</strong>
                    </p>
                </div>

                <!-- P&L Table Structure -->
                <div class="space-y-6 text-sm">

                    <!-- 1. Revenue Section -->
                    <div>
                        <div class="font-bold text-sm text-foreground uppercase tracking-wide border-b pb-1 mb-2">
                            1. Operating Revenue
                        </div>
                        <div class="space-y-1 pl-4">
                            <div class="flex justify-between py-1 text-muted-foreground">
                                <span>Gross Sales ({{ $pnl['sales']['count'] }} completed transactions)</span>
                                <span>@money($pnl['sales']['gross_sales'])</span>
                            </div>
                            <div class="flex justify-between py-1 text-rose-600">
                                <span>Less: Sales Discounts & Allowances</span>
                                <span>- @money($pnl['sales']['discounts'])</span>
                            </div>
                            <div class="flex justify-between py-2 border-t font-semibold text-foreground text-base">
                                <span>Net Sales Revenue</span>
                                <span>@money($pnl['sales']['net_revenue'])</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Cost of Goods Sold Section -->
                    <div>
                        <div class="font-bold text-sm text-foreground uppercase tracking-wide border-b pb-1 mb-2">
                            2. Cost of Goods Sold (COGS)
                        </div>
                        <div class="space-y-1 pl-4">
                            <div class="flex justify-between py-1 text-muted-foreground">
                                <span>Direct Product Cost of Sold Items</span>
                                <span>@money($pnl['cogs'])</span>
                            </div>
                            <div class="flex justify-between py-2 border-t font-semibold text-foreground text-base">
                                <span>Total Cost of Goods Sold</span>
                                <span>@money($pnl['cogs'])</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Gross Profit Summary Row -->
                    <div class="bg-muted/40 p-4 rounded-lg border flex justify-between items-center text-base">
                        <div>
                            <span class="font-bold text-foreground">GROSS PROFIT</span>
                            <div class="text-xs text-muted-foreground">Sales Revenue minus Cost of Goods Sold</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-extrabold text-foreground">@money($pnl['gross_profit'])</div>
                            <div class="text-xs font-semibold text-emerald-600">{{ number_format($pnl['gross_margin'], 1) }}% gross margin</div>
                        </div>
                    </div>

                    <!-- 4. Operating Expenses Section -->
                    <div>
                        <div class="font-bold text-sm text-foreground uppercase tracking-wide border-b pb-1 mb-2">
                            3. Operating Expenses
                        </div>
                        <div class="space-y-1 pl-4">
                            @forelse($pnl['expenses_by_category'] as $exp)
                                @if($exp['category'] !== 'Product Purchases')
                                    <div class="flex justify-between py-1 text-muted-foreground">
                                        <span>{{ $exp['category'] }} ({{ $exp['count'] }} entries)</span>
                                        <span>@money($exp['total'])</span>
                                    </div>
                                @endif
                            @empty
                                <div class="py-1 text-xs text-muted-foreground italic">
                                    No recorded operating expenses in this period.
                                </div>
                            @endforelse

                            <div class="flex justify-between py-2 border-t font-semibold text-foreground text-base">
                                <span>Total Operating Expenses</span>
                                <span class="text-rose-600">@money($pnl['operating_expenses'])</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Net Profit Summary Row -->
                    <div class="p-5 rounded-lg border-2 {{ $pnl['estimated_net_profit'] >= 0 ? 'bg-emerald-50/50 border-emerald-500' : 'bg-rose-50/50 border-rose-500' }} flex justify-between items-center text-lg">
                        <div>
                            <span class="font-extrabold {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-900' : 'text-rose-900' }}">
                                ESTIMATED NET PROFIT
                            </span>
                            <div class="text-xs text-muted-foreground">Gross Profit minus Operating Expenses</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                @money($pnl['estimated_net_profit'])
                            </div>
                            <div class="text-xs font-bold {{ $pnl['estimated_net_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format($pnl['net_margin'], 1) }}% net margin
                            </div>
                        </div>
                    </div>

                    <!-- Explanatory Accounting Note -->
                    <div class="text-xs text-muted-foreground bg-muted/20 p-3 rounded-md border border-dashed">
                        <strong>Accounting Disclosure:</strong> This statement separates product inventory purchases from general operating expenses. Product purchase outlays are accounted for under Cost of Goods Sold (COGS) upon sale to reflect accurate operational profitability.
                    </div>

                </div>

                <!-- Sign-off Section for Printed Reports -->
                <div class="hidden print:grid grid-cols-2 pt-12 text-xs border-t gap-8">
                    <div class="text-center">
                        <div class="h-16"></div>
                        <p class="border-t pt-1 font-semibold">Prepared by / Staff</p>
                    </div>
                    <div class="text-center">
                        <div class="h-16"></div>
                        <p class="border-t pt-1 font-semibold">Approved by / Management</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

