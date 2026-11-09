<x-app-layout title="Management Reports">
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Reports & Business Intelligence') }}
            </h2>
            <p class="text-sm text-muted-foreground">
                Consolidated performance reports for inventory, sales, profitability, and staff operations.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- 1. Profit & Loss -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between p-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-emerald-100 text-emerald-800 rounded-lg">
                                <x-heroicon-o-scale class="w-6 h-6" />
                            </div>
                            <h3 class="font-bold text-lg text-foreground">Profit & Loss (P&L)</h3>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                            Detailed financial statement showing Sales Revenue, Cost of Goods Sold (COGS), Gross Profit, Operating Expenses, and Estimated Net Profit.
                        </p>
                    </div>
                    <a href="{{ route('reports.profit-loss') }}" class="inline-flex items-center justify-center font-medium rounded-md text-sm bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full">
                        {{ __('View P&L Statement') }} &rarr;
                    </a>
                </div>

                <!-- 2. Stock Valuation & Categories -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between p-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-blue-100 text-blue-800 rounded-lg">
                                <x-heroicon-o-archive-box class="w-6 h-6" />
                            </div>
                            <h3 class="font-bold text-lg text-foreground">Stock Valuation</h3>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                            Total inventory value by cost and selling price, expected gross retail margin, and category-level stock distribution.
                        </p>
                    </div>
                    <a href="{{ route('reports.stock-valuation') }}" class="inline-flex items-center justify-center font-medium rounded-md text-sm bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full">
                        {{ __('View Stock Valuation') }} &rarr;
                    </a>
                </div>

                <!-- 3. Slow-Moving & Dead Stock -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between p-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-amber-100 text-amber-800 rounded-lg">
                                <x-heroicon-o-clock class="w-6 h-6" />
                            </div>
                            <h3 class="font-bold text-lg text-foreground">Slow-Moving Stock</h3>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                            Monitor inventory with zero sales over 30, 60, or 90+ days. Identify tied-up capital and plan promotional discounts.
                        </p>
                    </div>
                    <a href="{{ route('reports.slow-moving') }}" class="inline-flex items-center justify-center font-medium rounded-md text-sm bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full">
                        {{ __('Monitor Inactive Stock') }} &rarr;
                    </a>
                </div>

                <!-- 4. Sales & Staff Performance -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between p-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-indigo-100 text-indigo-800 rounded-lg">
                                <x-heroicon-o-chart-bar class="w-6 h-6" />
                            </div>
                            <h3 class="font-bold text-lg text-foreground">Sales Performance</h3>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                            Daily sales trends, product-by-product volume and profit margins, and sales recorded by each staff member / cashier.
                        </p>
                    </div>
                    <a href="{{ route('reports.sales') }}" class="inline-flex items-center justify-center font-medium rounded-md text-sm bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full">
                        {{ __('View Sales Reports') }} &rarr;
                    </a>
                </div>

                <!-- 5. Stock Movement Audit Trail -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between p-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-purple-100 text-purple-800 rounded-lg">
                                <x-heroicon-o-arrow-path-rounded-square class="w-6 h-6" />
                            </div>
                            <h3 class="font-bold text-lg text-foreground">Movement Audit Trail</h3>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                            Complete immutable chronological ledger of all stock movements: sales, purchase receiving, opening balances, and adjustments.
                        </p>
                    </div>
                    <a href="{{ route('stock.movements') }}" class="inline-flex items-center justify-center font-medium rounded-md text-sm bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full">
                        {{ __('View Movement Ledger') }} &rarr;
                    </a>
                </div>

                <!-- 6. Low Stock Alert Report -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between p-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2.5 bg-rose-100 text-rose-800 rounded-lg">
                                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                            </div>
                            <h3 class="font-bold text-lg text-foreground">Low Stock Alerts</h3>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                            Review products where current stock is at or below the configured minimum stock threshold to prevent stockouts.
                        </p>
                    </div>
                    <a href="{{ route('low-stock.index') }}" class="inline-flex items-center justify-center font-medium rounded-md text-sm bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 w-full">
                        {{ __('View Low Stock Items') }} &rarr;
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

