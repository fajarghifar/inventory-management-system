<section class="py-2.5 lg:py-3 bg-white border-b border-gray-200/80 shadow-2xs sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8" x-data="{ mobileMenuOpen: false }" @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen">
        <!-- Desktop Menu -->
        <nav class="hidden items-center justify-between lg:flex w-full gap-3 xl:gap-5">
            <!-- Left: Brand Store Identity -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center shadow-xs group-hover:bg-brand-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-sm font-black tracking-tight text-gray-900 group-hover:text-brand transition-colors whitespace-nowrap">
                        {{ \App\Models\Setting::get('store_name', 'Addis Store') }}
                    </span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5 whitespace-nowrap">
                        Retail POS
                    </span>
                </div>
            </a>

            <!-- Center: Navigation Menu Links -->
            <div class="flex items-center gap-1.5 xl:gap-2 flex-wrap xl:flex-nowrap justify-center">
                <!-- Primary Action: POS Register [F1] -->
                <a href="{{ route('sales.create') }}" class="inline-flex h-9 items-center justify-center rounded-xl px-3.5 py-1.5 text-xs font-bold transition-all shadow-xs active:scale-95 whitespace-nowrap flex-shrink-0 {{ request()->routeIs('sales.create') ? 'bg-brand text-white shadow-emerald-900/15' : 'bg-brand-50 text-brand hover:bg-brand-100 border border-brand-200/70' }}">
                    <x-heroicon-o-computer-desktop class="mr-1.5 h-4 w-4" />
                    <span>POS Register</span>
                    <span class="ml-1.5 text-[10px] font-mono px-1.5 py-0.5 rounded font-bold {{ request()->routeIs('sales.create') ? 'bg-white/20 text-white' : 'bg-brand-200/80 text-brand-900' }}">F1</span>
                </a>

                @can('admin')
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="inline-flex h-9 items-center justify-center rounded-xl px-3 py-1.5 text-xs transition-colors whitespace-nowrap flex-shrink-0 {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand font-bold border border-brand-200/60 shadow-2xs' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-semibold border border-transparent' }}">
                        <x-heroicon-o-squares-2x2 class="mr-1.5 h-4 w-4 text-gray-400" />
                        <span>Dashboard</span>
                    </a>
                @endcan

                <!-- Products & Stock Hub -->
                <x-nav-dropdown active="{{ request()->routeIs(['products.*', 'low-stock.*', 'categories.*', 'units.*', 'stock.*']) }}">
                    <x-slot name="icon">
                        <x-heroicon-o-cube class="mr-1.5 h-4 w-4 text-gray-400" />
                    </x-slot>
                    <x-slot name="trigger">
                        Products & Stock
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            All Products Catalog
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('low-stock.index')" :active="request()->routeIs('low-stock.*')">
                            Low Stock Alerts
                        </x-dropdown-link>
                        @can('admin')
                            <div class="border-t border-gray-100 my-1"></div>
                            <div class="px-4 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">Inventory Tools</div>
                            <x-dropdown-link :href="route('stock.adjustments')" :active="request()->routeIs('stock.adjustments')">
                                Stock Adjustments (Damage/Loss)
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('stock.movements')" :active="request()->routeIs('stock.movements')">
                                Movement Audit Log
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                                Product Categories
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('units.index')" :active="request()->routeIs('units.*')">
                                Units of Measure
                            </x-dropdown-link>
                        @endcan
                    </x-slot>
                </x-nav-dropdown>

                <!-- Sales History -->
                <a href="{{ route('sales.index') }}" class="inline-flex h-9 items-center justify-center rounded-xl px-3 py-1.5 text-xs transition-colors whitespace-nowrap flex-shrink-0 {{ request()->routeIs(['sales.index', 'sales.show']) ? 'bg-brand-50 text-brand font-bold border border-brand-200/60 shadow-2xs' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-semibold border border-transparent' }}">
                    <x-heroicon-o-receipt-percent class="mr-1.5 h-4 w-4 text-gray-400" />
                    <span>Sales History</span>
                </a>

                @can('admin')
                    <!-- Reports & Money -->
                    <x-nav-dropdown active="{{ request()->routeIs(['reports.*', 'finance.*']) }}">
                        <x-slot name="icon">
                            <x-heroicon-o-chart-bar class="mr-1.5 h-4 w-4 text-gray-400" />
                        </x-slot>
                        <x-slot name="trigger">
                            Reports & Money
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('reports.sales')" :active="request()->routeIs('reports.sales')">
                                Daily Sales & Cashiers
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
                            <div class="border-t border-gray-100 my-1"></div>
                            <div class="px-4 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">Expenses</div>
                            <x-dropdown-link :href="route('finance.transactions.index')" :active="request()->routeIs('finance.transactions.index')">
                                Record Expense
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('finance.categories.index')" :active="request()->routeIs('finance.categories.index')">
                                Expense Categories
                            </x-dropdown-link>
                        </x-slot>
                    </x-nav-dropdown>

                    <!-- Staff & Users -->
                    <a href="{{ route('users.index') }}" class="inline-flex h-9 items-center justify-center rounded-xl px-3 py-1.5 text-xs transition-colors whitespace-nowrap flex-shrink-0 {{ request()->routeIs('users.*') ? 'bg-brand-50 text-brand font-bold border border-brand-200/60 shadow-2xs' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 font-semibold border border-transparent' }}">
                        <x-heroicon-o-user-group class="mr-1.5 h-4 w-4 text-gray-400" />
                        <span>Staff & Users</span>
                    </a>
                @endcan
            </div>

            <!-- Right: User Auth Profile Button -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 h-9 px-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-brand whitespace-nowrap flex-shrink-0 shadow-2xs cursor-pointer">
                            <x-avatar :name="Auth::user()->name" class="w-6 h-6 text-[10px] font-bold" />
                            <span class="text-xs font-bold text-gray-800 hidden xl:inline-block max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-gray-400" />
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
                    <div class="w-7 h-7 rounded-lg bg-brand text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <span class="text-sm font-bold tracking-tight text-gray-900 truncate max-w-[200px]">
                        {{ \App\Models\Setting::get('store_name', 'Addis Store') }}
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
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ Auth::user()->isAdmin() ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-800' }}">
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
                        <a href="{{ route('sales.create') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-brand text-white shadow-sm hover:bg-brand-700 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-5 h-5 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <div>
                                    <div class="font-bold text-xs uppercase tracking-wider">Point of Sale</div>
                                    <div class="text-[11px] text-brand-100">Open register & record sale</div>
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

                            <!-- Expenses & Finance -->
                            <div class="space-y-1 pt-2 border-t border-border">
                                <div class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider px-2 mb-1">Expenses & Finance</div>
                                <a href="{{ route('finance.transactions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('finance.transactions.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-currency-dollar class="w-4 h-4 text-muted-foreground" />
                                    <span>Record Expense</span>
                                </a>
                                <a href="{{ route('finance.categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('finance.categories.*') ? 'bg-accent text-accent-foreground font-semibold' : 'hover:bg-accent/50 text-foreground' }}">
                                    <x-heroicon-o-tag class="w-4 h-4 text-muted-foreground" />
                                    <span>Expense Categories</span>
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
                        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
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
