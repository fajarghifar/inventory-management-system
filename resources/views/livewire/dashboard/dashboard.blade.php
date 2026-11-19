<div>
    <div class="space-y-5">

        <!-- Clean Store Overview Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 bg-card rounded-xl border border-border shadow-sm print:hidden">
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-foreground flex items-center gap-2">
                    <span>🛒 Market Overview</span>
                </h1>
                <p class="text-xs text-muted-foreground mt-0.5">Quick summary of today's sales, cash flow, and stock alerts.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <!-- Date Period Filter -->
                <select wire:model.live="dateFilter" class="h-9 rounded-lg border border-input bg-background px-3 text-xs font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach(\App\Enums\DatePeriod::cases() as $period)
                        <option value="{{ $period->value }}">{{ $period->label() }}</option>
                    @endforeach
                </select>

                <!-- Big Primary POS Button -->
                <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center rounded-lg text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white h-9 px-4 py-1.5 gap-1.5 shadow-sm transition-all hover:shadow-indigo-200">
                    <x-heroicon-o-computer-desktop class="w-4 h-4 text-indigo-200" />
                    <span>Open POS Register [F1]</span>
                </a>

                <!-- Quick Direct Sell -->
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-lg text-xs font-semibold border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 h-9 px-3 py-1.5 gap-1 shadow-sm">
                    <x-heroicon-o-bolt class="w-3.5 h-3.5 text-emerald-600" />
                    <span class="hidden sm:inline">⚡ Quick Sell</span>
                </a>

                @can('admin')
                    <!-- Seed Demo Data Button -->
                    <button wire:click="seedSampleData" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-lg text-xs font-semibold border border-purple-300 bg-purple-50 hover:bg-purple-100 text-purple-800 h-9 px-3 py-1.5 gap-1 shadow-sm" title="Populate demo products, barcodes, sales & expenses">
                        <x-heroicon-o-sparkles class="w-3.5 h-3.5 text-purple-600" />
                        <span wire:loading.remove wire:target="seedSampleData">🌱 Seed Data</span>
                        <span wire:loading wire:target="seedSampleData">Seeding...</span>
                    </button>
                @endcan
            </div>
        </div>

        <!-- 4 Key Market Vitals -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- 1. Total Sales -->
            <div class="rounded-xl border bg-card p-4 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-muted-foreground">
                    <span class="text-xs font-semibold uppercase tracking-wider">Sales Revenue</span>
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black text-foreground">
                        @money($stats['total_sales'] ?? 0)
                    </div>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        {{ $stats['sales_count'] ?? 0 }} completed sales
                    </p>
                </div>
            </div>

            <!-- 2. Profit / Earnings -->
            @can('admin')
                <div class="rounded-xl border bg-card p-4 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-muted-foreground">
                        <span class="text-xs font-semibold uppercase tracking-wider">Estimated Net Profit</span>
                        <div class="p-2 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50">
                            <x-heroicon-o-scale class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="text-xl sm:text-2xl font-black {{ ($stats['estimated_net_profit'] ?? 0) >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            @money($stats['estimated_net_profit'] ?? 0)
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Gross Profit: @money($stats['gross_profit'] ?? 0)
                        </p>
                    </div>
                </div>
            @else
                <div class="rounded-xl border bg-card p-4 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-muted-foreground">
                        <span class="text-xs font-semibold uppercase tracking-wider">Cashier Status</span>
                        <div class="p-2 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50">
                            <x-heroicon-o-user class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="text-xl sm:text-2xl font-black text-foreground">
                            {{ $stats['sales_count'] ?? 0 }} Sales
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">Shift active</p>
                    </div>
                </div>
            @endcan

            <!-- 3. Low Stock Warning -->
            <a href="{{ route('low-stock.index') }}" class="rounded-xl border bg-card p-4 shadow-sm flex flex-col justify-between transition-colors hover:border-rose-400">
                <div class="flex items-center justify-between text-muted-foreground">
                    <span class="text-xs font-semibold uppercase tracking-wider">Low Stock Alert</span>
                    <div class="p-2 rounded-lg {{ count($lowStockProducts) > 0 ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/50' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50' }}">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black {{ count($lowStockProducts) > 0 ? 'text-rose-600' : 'text-foreground' }}">
                        {{ count($lowStockProducts) }} {{ count($lowStockProducts) === 1 ? 'item' : 'items' }}
                    </div>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        {{ count($lowStockProducts) > 0 ? 'Needs reorder →' : 'All products well stocked' }}
                    </p>
                </div>
            </a>

            <!-- 4. Products in Catalog -->
            <a href="{{ route('products.index') }}" class="rounded-xl border bg-card p-4 shadow-sm flex flex-col justify-between transition-colors hover:border-indigo-400">
                <div class="flex items-center justify-between text-muted-foreground">
                    <span class="text-xs font-semibold uppercase tracking-wider">Products On Shelf</span>
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50">
                        <x-heroicon-o-cube class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-2">
                    <div class="text-xl sm:text-2xl font-black text-foreground">
                        {{ number_format($inventoryCounts['total_products'] ?? 0) }} Items
                    </div>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        {{ number_format($inventoryCounts['total_units'] ?? 0) }} physical units →
                    </p>
                </div>
            </a>
        </div>

        <!-- Daily Action Grid: Recent Sales & Low Stock Watch -->
        <div class="grid gap-5 lg:grid-cols-3">
            <!-- Recent Sales (2 Columns Wide) -->
            <div class="lg:col-span-2 rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 border-b flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-foreground">Recent Sales Register</h3>
                        <p class="text-xs text-muted-foreground">Latest register checkout transactions</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        View All Sales &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-muted-foreground uppercase bg-muted/40 border-b">
                            <tr>
                                <th class="px-4 py-2.5">Invoice</th>
                                <th class="px-4 py-2.5">Time</th>
                                <th class="px-4 py-2.5">Payment</th>
                                <th class="px-4 py-2.5 text-right">Amount</th>
                                <th class="px-4 py-2.5 text-center">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-muted/30 transition-colors">
                                    <td class="px-4 py-3 font-mono font-medium text-xs text-foreground">
                                        {{ $sale['invoice_number'] }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-muted-foreground">
                                        <div class="font-medium text-foreground">{{ \Carbon\Carbon::parse($sale['sale_date'] ?? $sale['created_at'])->format('h:i A') }}</div>
                                        <div class="text-[10px]">{{ \Carbon\Carbon::parse($sale['sale_date'] ?? $sale['created_at'])->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $sale['payment_method']->value ?? 'cash' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-600 text-sm">
                                        @money($sale['total'])
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('sales.receipt', $sale['id']) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 rounded-md hover:bg-accent text-indigo-600 hover:text-indigo-800 transition-colors" title="Print Receipt">
                                            <x-heroicon-o-printer class="w-4 h-4" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-xs text-muted-foreground">
                                        No sales recorded in this period yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Restock Watch & Top Sellers (1 Column Wide) -->
            <div class="space-y-4">
                <!-- Low Stock Needs Attention -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4">
                    <div class="flex items-center justify-between pb-3 border-b mb-3">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-rose-500" />
                            <h3 class="font-bold text-sm text-foreground">Low Stock Watch</h3>
                        </div>
                        <a href="{{ route('low-stock.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                            See all
                        </a>
                    </div>
                    <div class="space-y-2.5">
                        @forelse(array_slice($lowStockProducts, 0, 4) as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="truncate pr-2">
                                    <div class="font-medium text-foreground truncate">{{ $item['name'] }}</div>
                                    <div class="text-[10px] text-muted-foreground font-mono">{{ $item['sku'] }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 whitespace-nowrap">
                                    {{ $item['quantity'] }} left
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-muted-foreground text-center py-2">All items have healthy stock levels.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Top Selling Items -->
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-4">
                    <div class="flex items-center justify-between pb-3 border-b mb-3">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-fire class="w-4 h-4 text-amber-500" />
                            <h3 class="font-bold text-sm text-foreground">Top Sellers</h3>
                        </div>
                    </div>
                    <div class="space-y-2.5">
                        @forelse(array_slice($topProducts, 0, 4) as $prod)
                            <div class="flex items-center justify-between text-xs">
                                <div class="truncate pr-2">
                                    <div class="font-medium text-foreground truncate">{{ $prod['product_name'] }}</div>
                                    <div class="text-[10px] text-muted-foreground font-mono">{{ $prod['sku'] }}</div>
                                </div>
                                <span class="font-semibold text-xs text-foreground bg-muted px-2 py-0.5 rounded">
                                    {{ $prod['total_sold'] }} sold
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-muted-foreground text-center py-2">No sales data for this period.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @can('admin')
            <!-- Collapsible Analytics & Trends (Keeps page lightweight, opens when needed) -->
            <details class="group bg-card rounded-xl border border-border shadow-sm p-4 print:block">
                <summary class="cursor-pointer text-sm font-semibold text-foreground flex items-center justify-between select-none">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-chart-bar class="w-4 h-4 text-indigo-500" />
                        <span>📊 Detailed Sales & Expense Charts (Click to expand)</span>
                    </div>
                    <span class="text-xs text-muted-foreground group-open:rotate-180 transition-transform">▼</span>
                </summary>

                <div class="pt-4 mt-3 border-t grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Sales Trend -->
                    <div class="col-span-1 lg:col-span-2 rounded-xl border bg-background p-4 shadow-sm">
                        <div class="flex flex-col space-y-1 pb-2">
                            <h4 class="font-semibold text-xs leading-none tracking-tight">Daily Sales Trend</h4>
                            <p class="text-[11px] text-muted-foreground">Revenue timeline.</p>
                        </div>
                        <div class="pt-0" wire:ignore>
                            <div id="salesChart" class="w-full h-[220px]"></div>
                        </div>
                    </div>

                    <!-- Cash Flow -->
                    <div class="col-span-1 rounded-xl border bg-background p-4 shadow-sm">
                        <div class="flex flex-col space-y-1 pb-2">
                            <h4 class="font-semibold text-xs leading-none tracking-tight">Income vs Expense</h4>
                            <p class="text-[11px] text-muted-foreground">Net cash balance.</p>
                        </div>
                        <div class="pt-0" wire:ignore>
                            <div id="cashFlowChart" class="w-full h-[220px]"></div>
                        </div>
                    </div>
                </div>
            </details>
        @endcan
    </div>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 1cm; }
        body { background-color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .print\:hidden { display: none !important; }
        .bg-card { border: 1px solid #e2e8f0; box-shadow: none !important; }
        .grid { gap: 1rem !important; }
        .break-inside-avoid { break-inside: avoid; page-break-inside: avoid; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let salesChart = null;
        let cashFlowChart = null;
        
        const currencySymbol = "{{ \App\Models\Setting::get('currency_symbol', 'ETB') }}";
        const currencyPosition = "{{ \App\Models\Setting::get('currency_position', 'left') }}";

        const formatMoney = (val) => {
            let num = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val);
            return currencyPosition === 'left' ? currencySymbol + ' ' + num : num + ' ' + currencySymbol;
        };

        const initCharts = (data) => {
            // Sales Chart
            const salesOptions = {
                series: [{
                    name: 'Sales',
                    data: data.sales.data
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: data.sales.labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#0ea5e9'], // Sky 500
                tooltip: {
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                }
            };

            // Cash Flow Chart
            const cashFlowOptions = {
                series: [{
                    name: 'Income',
                    data: data.cashFlow.income
                }, {
                    name: 'Expense',
                    data: data.cashFlow.expense
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: data.cashFlow.labels,
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' },
                        formatter: (val) => {
                             // Shorten detailed numbers for y-axis
                             if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                             if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                             return val;
                        }
                    }
                },
                colors: ['#10b981', '#ef4444'], // Emerald 500, Red 500
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                }
            };

            // Expense Breakdown Chart
            const hasExpenseData = data.expense.series && data.expense.series.length > 0;
            const expenseOptions = {
                series: hasExpenseData ? data.expense.series.map(Number) : [1],
                labels: hasExpenseData ? data.expense.labels : ['No Data'],
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                dataLabels: { enabled: false },
                colors: hasExpenseData ? ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#06b6d4', '#6366f1'] : ['#e5e7eb'],
                tooltip: {
                    enabled: hasExpenseData,
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    offsetY: 0,
                    height: 60,
                }
            };

            if (salesChart) salesChart.destroy();
            if (cashFlowChart) cashFlowChart.destroy();
            if (window.expenseChartInst) window.expenseChartInst.destroy();

            salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
            salesChart.render();

            cashFlowChart = new ApexCharts(document.querySelector("#cashFlowChart"), cashFlowOptions);
            cashFlowChart.render();
            
            window.expenseChartInst = new ApexCharts(document.querySelector("#expenseChart"), expenseOptions);
            window.expenseChartInst.render();
        };

        // Initial Load
        initCharts({
            sales: @json($salesChart),
            cashFlow: @json($cashFlowChart),
            expense: @json($expenseChart)
        });



        // Listen for server-side updates
        Livewire.on('stats-updated', (data) => {
             initCharts(data[0]); // data is array of args
        });
    });
</script>
</div>
