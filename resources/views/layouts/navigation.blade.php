<section class="py-3 lg:py-4 bg-background border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ mobileMenuOpen: false }" @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen">
        <!-- Desktop Menu -->
        <nav class="hidden items-center justify-between lg:flex">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="w-8 h-8 fill-current text-foreground" />
                    <span class="text-md font-semibold tracking-tighter text-foreground">
                        {{ config('app.name', 'Laravel') }}
                    </span>
                </a>

                <!-- Navigation Menu -->
                <div class="flex items-center">
                    <div class="flex flex-row gap-1">
                        <!-- Dashboard Link -->
                        <a href="{{ route('dashboard') }}" class="group inline-flex h-10 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors hover:bg-muted hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50 {{ request()->routeIs('dashboard') ? 'bg-accent/50 text-accent-foreground' : 'bg-background' }}">
                            <x-heroicon-o-squares-2x2 class="mr-2 h-4 w-4" />
                            Dashboard
                        </a>

                        <!-- Sales Dropdown -->
                        <x-nav-dropdown active="{{ request()->routeIs(['sales.*', 'customers.*']) }}">
                            <x-slot name="icon">
                                <x-heroicon-o-banknotes class="mr-2 h-4 w-4" />
                            </x-slot>
                            <x-slot name="trigger">
                                Sales
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('sales.create')" :active="request()->routeIs('sales.create')">
                                    POS
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('sales.index')" :active="request()->routeIs(['sales.index', 'sales.show'])">
                                    Sales
                                </x-dropdown-link>
                                @can('admin')
                                    <x-dropdown-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                                        Customers
                                    </x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-nav-dropdown>

                        @can('admin')
                            <!-- Purchases Dropdown -->
                            <x-nav-dropdown active="{{ request()->routeIs(['purchases.*', 'suppliers.*']) }}">
                                <x-slot name="icon">
                                    <x-heroicon-o-shopping-cart class="mr-2 h-4 w-4" />
                                </x-slot>
                                <x-slot name="trigger">
                                    Purchases
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')">
                                        Purchases
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                        Suppliers
                                    </x-dropdown-link>
                                </x-slot>
                            </x-nav-dropdown>

                            <!-- Finance Dropdown -->
                            <x-nav-dropdown active="{{ request()->routeIs(['finance.*']) }}">
                                <x-slot name="icon">
                                    <x-heroicon-o-currency-dollar class="mr-2 h-4 w-4" />
                                </x-slot>
                                <x-slot name="trigger">
                                    Finance
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('finance.transactions.index')" :active="request()->routeIs('finance.transactions.index')">
                                        Transactions
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('finance.categories.index')" :active="request()->routeIs('finance.categories.index')">
                                        Categories
                                    </x-dropdown-link>
                                </x-slot>
                            </x-nav-dropdown>

                            <!-- Reports Dropdown -->
                            <x-nav-dropdown active="{{ request()->routeIs(['reports.*']) }}">
                                <x-slot name="icon">
                                    <x-heroicon-o-chart-bar class="mr-2 h-4 w-4" />
                                </x-slot>
                                <x-slot name="trigger">
                                    Reports
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                                        Reports Hub
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('reports.profit-loss')" :active="request()->routeIs('reports.profit-loss')">
                                        Profit & Loss (P&L)
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('reports.stock-valuation')" :active="request()->routeIs('reports.stock-valuation')">
                                        Stock Valuation
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('reports.slow-moving')" :active="request()->routeIs('reports.slow-moving')">
                                        Slow-Moving Stock
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('reports.sales')" :active="request()->routeIs('reports.sales')">
                                        Sales & Cashiers
                                    </x-dropdown-link>
                                </x-slot>
                            </x-nav-dropdown>

                            <!-- Users Link -->
                            <a href="{{ route('users.index') }}" class="group inline-flex h-10 w-max items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors hover:bg-muted hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50 {{ request()->routeIs('users.*') ? 'bg-accent/50 text-accent-foreground' : 'bg-background' }}">
                                <x-heroicon-o-users class="mr-2 h-4 w-4" />
                                Users
                            </a>
                        @endcan

                        <!-- Products Dropdown -->
                        <x-nav-dropdown active="{{ request()->routeIs(['products.*', 'categories.*', 'units.*', 'low-stock.*', 'stock.*']) }}">
                            <x-slot name="icon">
                                <x-heroicon-o-cube class="mr-2 h-4 w-4" />
                            </x-slot>
                            <x-slot name="trigger">
                                Inventory
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                    Products
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('low-stock.index')" :active="request()->routeIs('low-stock.*')">
                                    Low Stock Alerts
                                </x-dropdown-link>
                                @can('admin')
                                    <x-dropdown-link :href="route('stock.adjustments')" :active="request()->routeIs('stock.adjustments')">
                                        Stock Adjustments
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('stock.movements')" :active="request()->routeIs('stock.movements')">
                                        Movement Audit Log
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                        Categories
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('units.index')" :active="request()->routeIs('units.*')">
                                        Units
                                    </x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-nav-dropdown>
                    </div>
                </div>
            </div>

            <!-- User Auth Buttons -->
            <div class="flex gap-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-full text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2">
                            <span class="hidden md:inline-flex">{{ Auth::user()->name }}</span>
                            <x-avatar :name="Auth::user()->name" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')" :active="request()->routeIs('profile.*')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @can('admin')
                            <x-dropdown-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                        @endcan

                        <button type="button" @click="$dispatch('open-pwa-install')" class="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-accent hover:text-accent-foreground flex items-center gap-2 transition-colors">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            {{ __('Install App') }}
                        </button>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </nav>

        <!-- Mobile Top App Bar -->
        <div class="block lg:hidden">
            <div class="flex items-center justify-between">
                <!-- Brand Logo + Name -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="w-8 h-8 fill-current text-foreground" />
                    <span class="text-base font-bold tracking-tight text-foreground truncate max-w-[180px]">
                        {{ config('app.name', 'Inventory') }}
                    </span>
                </a>

                <!-- Right Actions: Notifications bell (future) + Avatar -->
                <div class="flex items-center gap-2">
                    <!-- Quick Profile Avatar (taps open drawer) -->
                    <button
                        type="button"
                        @click="mobileMenuOpen = true"
                        class="flex items-center gap-1.5 focus:outline-none"
                        title="Menu & Profile"
                    >
                        <x-avatar :name="Auth::user()->name" class="w-8 h-8 text-xs font-bold" />
                    </button>
                </div>
            </div>

            <!-- Mobile Sheet/Drawer Backdrop -->
            <div x-show="mobileMenuOpen"
                x-transition:enter="duration-300 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="duration-200 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"
                style="display: none;"
                @click="mobileMenuOpen = false">
            </div>

            <!-- Slide-over Drawer Panel -->
            <div x-show="mobileMenuOpen"
                x-transition:enter="duration-300 ease-out"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="duration-200 ease-in"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-50 h-full w-[85%] max-w-sm border-l border-border bg-card p-5 shadow-2xl flex flex-col justify-between overflow-y-auto"
                style="display: none;"
                @click.stop>

                <div>
                    <!-- Drawer Header & User Profile Card -->
                    <div class="flex items-start justify-between pb-4 border-b border-border">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="Auth::user()->name" class="w-10 h-10 text-sm font-bold" />
                            <div>
                                <h3 class="font-bold text-sm text-foreground leading-tight">{{ Auth::user()->name }}</h3>
                                <p class="text-xs text-muted-foreground">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ Auth::user()->isAdmin() ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ Auth::user()->role->value ?? 'User' }}
                                </span>
                            </div>
                        </div>
                        <button @click="mobileMenuOpen = false" class="p-1.5 text-muted-foreground hover:text-foreground rounded-md hover:bg-accent transition-colors">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Clean Grouped Menu Links -->
                    <div class="py-4 space-y-4">
                        <!-- Quick POS Sale Launch Card -->
                        <a href="{{ route('sales.create') }}" class="flex items-center justify-between p-3 rounded-lg bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <div>
                                    <div class="font-bold text-xs uppercase tracking-wider">Point of Sale</div>
                                    <div class="text-[11px] text-indigo-100">Open register & record sale</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold bg-white/20 px-2 py-1 rounded">Open &rarr;</span>
                        </a>

                        <!-- Daily Operations -->
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider px-2 mb-1">Operations</div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                <x-heroicon-o-squares-2x2 class="w-4 h-4 text-muted-foreground" />
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('products.index') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                <x-heroicon-o-cube class="w-4 h-4 text-muted-foreground" />
                                <span>Products Catalog</span>
                            </a>
                            <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('sales.index') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                <x-heroicon-o-receipt-percent class="w-4 h-4 text-muted-foreground" />
                                <span>Sales History</span>
                            </a>
                            <a href="{{ route('low-stock.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('low-stock.index') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-500" />
                                <span>Low Stock Alerts</span>
                            </a>
                        </div>

                        @can('admin')
                            <!-- Stock & Inventory -->
                            <div class="space-y-1 pt-2 border-t border-border">
                                <div class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider px-2 mb-1">Inventory Control</div>
                                <a href="{{ route('stock.adjustments') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('stock.adjustments') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-adjustments-horizontal class="w-4 h-4 text-muted-foreground" />
                                    <span>Stock Adjustments</span>
                                </a>
                                <a href="{{ route('stock.movements') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('stock.movements') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-muted-foreground" />
                                    <span>Movements Audit Log</span>
                                </a>
                                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('categories.index') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-tag class="w-4 h-4 text-muted-foreground" />
                                    <span>Categories</span>
                                </a>
                                <a href="{{ route('units.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('units.index') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-scale class="w-4 h-4 text-muted-foreground" />
                                    <span>Units</span>
                                </a>
                            </div>

                            <!-- Purchasing & Finance -->
                            <div class="space-y-1 pt-2 border-t border-border">
                                <div class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider px-2 mb-1">Purchasing & Finance</div>
                                <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('purchases.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-shopping-bag class="w-4 h-4 text-muted-foreground" />
                                    <span>Purchases</span>
                                </a>
                                <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-truck class="w-4 h-4 text-muted-foreground" />
                                    <span>Suppliers</span>
                                </a>
                                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('customers.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-users class="w-4 h-4 text-muted-foreground" />
                                    <span>Customers</span>
                                </a>
                                <a href="{{ route('finance.transactions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('finance.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-currency-dollar class="w-4 h-4 text-muted-foreground" />
                                    <span>Finance Transactions</span>
                                </a>
                            </div>

                            <!-- Reports & Insights -->
                            <div class="space-y-1 pt-2 border-t border-border">
                                <div class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider px-2 mb-1">Reports & Analytics</div>
                                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('reports.index') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-chart-pie class="w-4 h-4 text-muted-foreground" />
                                    <span>Reports Hub</span>
                                </a>
                                <a href="{{ route('reports.profit-loss') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('reports.profit-loss') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-chart-bar class="w-4 h-4 text-muted-foreground" />
                                    <span>Profit & Loss</span>
                                </a>
                                <a href="{{ route('reports.stock-valuation') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('reports.stock-valuation') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-archive-box class="w-4 h-4 text-muted-foreground" />
                                    <span>Stock Valuation</span>
                                </a>
                                <a href="{{ route('reports.slow-moving') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('reports.slow-moving') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-clock class="w-4 h-4 text-muted-foreground" />
                                    <span>Slow-Moving Monitor</span>
                                </a>
                            </div>

                            <!-- User Management -->
                            <div class="space-y-1 pt-2 border-t border-border">
                                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-user-group class="w-4 h-4 text-muted-foreground" />
                                    <span>Users & Permissions</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>

                <!-- Drawer Footer: Profile, Settings, Install & Logout -->
                <div class="pt-4 mt-2 border-t border-border space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('profile.index') }}" class="inline-flex items-center justify-center rounded-md text-xs font-semibold border border-input h-9 px-3 bg-background hover:bg-accent text-foreground transition-colors gap-1.5">
                            <x-heroicon-o-user class="w-3.5 h-3.5" />
                            Profile
                        </a>
                        @can('admin')
                            <a href="{{ route('settings.index') }}" class="inline-flex items-center justify-center rounded-md text-xs font-semibold border border-input h-9 px-3 bg-background hover:bg-accent text-foreground transition-colors gap-1.5">
                                <x-heroicon-o-cog-6-tooth class="w-3.5 h-3.5" />
                                Settings
                            </a>
                        @endcan
                    </div>

                    <button
                        type="button"
                        @click="$dispatch('open-pwa-install'); mobileMenuOpen = false"
                        class="w-full inline-flex items-center justify-center rounded-md text-xs font-semibold border border-input h-9 px-3 bg-background hover:bg-accent text-foreground transition-colors gap-2"
                    >
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Install Web App
                    </button>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-md text-xs font-semibold bg-red-600 hover:bg-red-700 text-white h-9 px-3 transition-colors gap-1.5">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
