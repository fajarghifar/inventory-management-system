<div>
    <div class="space-y-5">

        <!-- 1. Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-xs">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900">Products</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Manage your retail products, inventory stock, and pricing in Ethiopian Birr.</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                @can('admin')
                    <button
                        type="button"
                        wire:click="exportCsv"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors shadow-2xs"
                        title="Export products to CSV"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-gray-500" />
                        <span class="hidden sm:inline">Export CSV</span>
                    </button>

                    <x-primary-button
                        type="button"
                        x-data
                        x-on:click="$dispatch('create-product')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs sm:text-sm font-bold bg-brand hover:bg-brand-700 text-white rounded-xl shadow-sm transition-all active:scale-95"
                    >
                        <x-heroicon-o-plus class="w-4 h-4" />
                        <span>Create Product</span>
                    </x-primary-button>
                @endcan
            </div>
        </div>

        <!-- 2. Inventory Summary Cards (Calculated from real data) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Products -->
            <button
                type="button"
                wire:click="$set('stockFilter', 'all')"
                class="text-left p-4 rounded-2xl border transition-all {{ $stockFilter === 'all' ? 'border-brand bg-brand-50/50 ring-2 ring-brand/20' : 'border-gray-100 bg-white hover:border-gray-200' }} shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500">Total Products</span>
                    <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                        <x-heroicon-o-cube class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2 font-mono">{{ number_format($stats['total']) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">Catalog items</div>
            </button>

            <!-- In Stock / Available -->
            <button
                type="button"
                wire:click="$set('stockFilter', 'in_stock')"
                class="text-left p-4 rounded-2xl border transition-all {{ $stockFilter === 'in_stock' ? 'border-brand bg-brand-50/50 ring-2 ring-brand/20' : 'border-gray-100 bg-white hover:border-gray-200' }} shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-700">In Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-700 mt-2 font-mono">{{ number_format($stats['in_stock']) }}</div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Optimal inventory</div>
            </button>

            <!-- Low Stock Alert -->
            <button
                type="button"
                wire:click="$set('stockFilter', 'low_stock')"
                class="text-left p-4 rounded-2xl border transition-all {{ $stockFilter === 'low_stock' ? 'border-amber-400 bg-amber-50/50 ring-2 ring-amber-400/20' : 'border-gray-100 bg-white hover:border-gray-200' }} shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-700">Low Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-amber-700 mt-2 font-mono">{{ number_format($stats['low_stock']) }}</div>
                <div class="text-[11px] text-amber-600 mt-0.5">Needs restock</div>
            </button>

            <!-- Out of Stock -->
            <button
                type="button"
                wire:click="$set('stockFilter', 'out_of_stock')"
                class="text-left p-4 rounded-2xl border transition-all {{ $stockFilter === 'out_of_stock' ? 'border-red-400 bg-red-50/50 ring-2 ring-red-400/20' : 'border-gray-100 bg-white hover:border-gray-200' }} shadow-xs"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-red-700">Out of Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <x-heroicon-o-x-circle class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-red-700 mt-2 font-mono">{{ number_format($stats['out_of_stock']) }}</div>
                <div class="text-[11px] text-red-600 mt-0.5">Depleted inventory</div>
            </button>
        </div>

        <!-- 3. Toolbar (Search, Category Dropdown, Stock Filters, Sorting) -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by product name, SKU, or barcode..."
                        class="w-full h-11 pl-11 pr-10 text-sm bg-gray-50/60 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-all"
                    />
                    @if($search !== '')
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1"
                        >
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    @endif
                </div>

                <!-- Filters & Controls -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Category Dropdown -->
                    <div class="flex-1 sm:flex-none min-w-[170px]">
                        <select
                            wire:model.live="selectedCategory"
                            class="w-full h-11 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl px-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand cursor-pointer"
                        >
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Status Dropdown -->
                    <div class="flex-1 sm:flex-none min-w-[145px]">
                        <select
                            wire:model.live="stockFilter"
                            class="w-full h-11 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl px-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand cursor-pointer"
                        >
                            <option value="all">All Inventory</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock Alert</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="inactive">Discontinued</option>
                        </select>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="flex-1 sm:flex-none min-w-[145px]">
                        <select
                            wire:model.live="sortBy"
                            class="w-full h-11 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl px-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand cursor-pointer"
                        >
                            <option value="latest">Newest First</option>
                            <option value="name_asc">Name (A &rarr; Z)</option>
                            <option value="name_desc">Name (Z &rarr; A)</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="qty_desc">Stock: High to Low</option>
                            <option value="qty_asc">Stock: Low to High</option>
                        </select>
                    </div>

                    <!-- Items Per Page -->
                    <select
                        wire:model.live="perPage"
                        class="h-11 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl px-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand hidden sm:block cursor-pointer"
                        title="Items per page"
                    >
                        <option value="12">12 / page</option>
                        <option value="24">24 / page</option>
                        <option value="48">48 / page</option>
                    </select>

                    <!-- Reset Filters Button if any active -->
                    @if($search !== '' || !empty($selectedCategory) || $stockFilter !== 'all' || $sortBy !== 'latest')
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="h-11 px-3 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-xl transition-colors flex items-center gap-1"
                            title="Clear all filters"
                        >
                            <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Active Filter Badges (Shown when category or search is selected) -->
            @if(!empty($selectedCategory) || $search !== '' || $stockFilter !== 'all')
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100 flex-wrap text-xs">
                    <span class="text-gray-400 font-medium text-[11px] uppercase tracking-wider">Active:</span>

                    @if(!empty($selectedCategory))
                        @php $activeCat = $categories->firstWhere('id', (int)$selectedCategory); @endphp
                        @if($activeCat)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-50 text-brand border border-brand-100">
                                <span>Category: {{ $activeCat->name }}</span>
                                <button type="button" wire:click="$set('selectedCategory', null)" class="text-brand hover:text-brand-900 p-0.5">
                                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                </button>
                            </span>
                        @endif
                    @endif

                    @if($stockFilter !== 'all')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700">
                            <span>Status: {{ ucwords(str_replace('_', ' ', $stockFilter)) }}</span>
                            <button type="button" wire:click="$set('stockFilter', 'all')" class="text-gray-500 hover:text-gray-900 p-0.5">
                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                            </button>
                        </span>
                    @endif

                    @if($search !== '')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700">
                            <span>"{{ $search }}"</span>
                            <button type="button" wire:click="$set('search', '')" class="text-gray-500 hover:text-gray-900 p-0.5">
                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- 4. Product Catalog Content -->

        <!-- DESKTOP DATA TABLE (Visible on Desktop >= 1024px) -->
        <div class="hidden lg:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/70 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-5 py-3.5">Product</th>
                            <th class="px-4 py-3.5">Category</th>
                            <th class="px-3 py-3.5">Unit</th>
                            <th class="px-4 py-3.5">Stock Status</th>
                            @can('admin')
                                <th class="px-4 py-3.5 text-right">Buying Price</th>
                            @endcan
                            <th class="px-4 py-3.5 text-right">Selling Price</th>
                            @can('admin')
                                <th class="px-4 py-3.5 text-right">Margin</th>
                            @endcan
                            <th class="px-5 py-3.5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($products as $product)
                            @php
                                $margin = $product->selling_price - $product->purchase_price;
                                $marginPct = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <!-- Product & Thumbnail -->
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if($product->image_url)
                                            <img
                                                src="{{ $product->image_url }}"
                                                alt="{{ $product->name }}"
                                                class="w-11 h-11 rounded-xl object-cover border border-gray-200 flex-shrink-0 shadow-2xs"
                                                loading="lazy"
                                            />
                                        @else
                                            <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand flex items-center justify-center font-bold text-xs flex-shrink-0 border border-brand-100/50 shadow-2xs">
                                                {{ strtoupper(substr($product->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-brand transition-colors">
                                                {{ $product->name }}
                                                @if(!$product->is_active)
                                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">DISCONTINUED</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $product->sku ?: 'No SKU' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-brand-50 text-brand">
                                        {{ $product->category->name ?? '-' }}
                                    </span>
                                </td>

                                <!-- Unit -->
                                <td class="px-3 py-3.5 text-xs text-gray-600 font-medium">
                                    {{ $product->unit->symbol ?? ($product->unit->name ?? '-') }}
                                </td>

                                <!-- Stock Status -->
                                <td class="px-4 py-3.5">
                                    @if(!$product->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @elseif($product->quantity <= 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            Out of Stock (0)
                                        </span>
                                    @elseif($product->quantity <= $product->min_stock)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Low: {{ $product->quantity }} {{ $product->unit->symbol ?? '' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $product->quantity }} {{ $product->unit->symbol ?? '' }} in stock
                                        </span>
                                    @endif
                                </td>

                                <!-- Buying Price (Admin only) -->
                                @can('admin')
                                    <td class="px-4 py-3.5 text-right text-xs font-medium text-gray-500 font-mono">
                                        @money($product->purchase_price)
                                    </td>
                                @endcan

                                <!-- Selling Price -->
                                <td class="px-4 py-3.5 text-right font-bold text-gray-900 font-mono text-sm">
                                    @money($product->selling_price)
                                </td>

                                <!-- Margin (Admin only) -->
                                @can('admin')
                                    <td class="px-4 py-3.5 text-right">
                                        <span class="font-bold text-xs font-mono {{ $margin >= 0 ? 'text-brand' : 'text-red-600' }}">
                                            @money($margin)
                                        </span>
                                        <span class="block text-[10px] text-gray-400">({{ round($marginPct, 1) }}%)</span>
                                    </td>
                                @endcan

                                <!-- Row Actions -->
                                <td class="px-5 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-1.5 justify-center">
                                        <!-- Quick Express Sale (Lightning Bolt) -->
                                        <button
                                            type="button"
                                            wire:click="$dispatch('open-quick-sale-modal', { productId: {{ $product->id }} })"
                                            class="p-2 rounded-xl bg-brand text-white hover:bg-brand-700 transition-all active:scale-90 shadow-2xs"
                                            title="Quick Direct Sale"
                                        >
                                            <x-heroicon-o-bolt class="w-4 h-4" />
                                        </button>

                                        <!-- View Details (Eye) -->
                                        <button
                                            type="button"
                                            wire:click="$dispatch('show-product', { product: {{ $product->id }} })"
                                            class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors"
                                            title="View Product Details"
                                        >
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </button>

                                        <!-- Edit Product (Admin only) -->
                                        @can('admin')
                                            <button
                                                type="button"
                                                wire:click="$dispatch('edit-product', { product: {{ $product->id }} })"
                                                class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 transition-colors"
                                                title="Edit Product"
                                            >
                                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                            </button>

                                            <!-- Delete Product (Admin only) -->
                                            <button
                                                type="button"
                                                wire:click="$dispatch('open-delete-modal', {
                                                    component: 'products.product-catalog',
                                                    method: 'deleteProduct',
                                                    params: { id: {{ $product->id }} },
                                                    title: 'Delete Product?',
                                                    description: 'Are you sure you want to delete {{ addslashes($product->name) }}? This cannot be undone.'
                                                })"
                                                class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                                                title="Delete Product"
                                            >
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400">
                                    <x-heroicon-o-cube class="w-12 h-12 mx-auto text-gray-300 stroke-1" />
                                    <div class="mt-2 text-sm font-semibold text-gray-700">No products found</div>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your search query or filters.</p>
                                    <button
                                        type="button"
                                        wire:click="resetFilters"
                                        class="mt-3 px-3.5 py-1.5 text-xs font-bold text-brand bg-brand-50 hover:bg-brand-100 rounded-xl transition-colors"
                                    >
                                        Clear All Filters
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE & TABLET VISUAL PRODUCT CARDS (Visible on < 1024px) -->
        <div class="block lg:hidden">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @forelse($products as $product)
                    @php
                        $margin = $product->selling_price - $product->purchase_price;
                        $marginPct = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex flex-col justify-between space-y-3 relative group">
                        <!-- Top Card Header: Thumbnail + Name + SKU + 3-Dot Menu -->
                        <div class="flex items-start gap-3">
                            <!-- Square 1:1 Thumbnail -->
                            @if($product->image_url)
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="w-16 h-16 rounded-2xl object-cover border border-gray-100 flex-shrink-0 shadow-2xs"
                                    loading="lazy"
                                />
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand flex items-center justify-center font-bold text-base flex-shrink-0 border border-brand-100/50 shadow-2xs">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </div>
                            @endif

                            <div class="flex-1 min-w-0 pr-6">
                                <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                <div class="text-[11px] text-gray-400 font-mono mt-0.5 truncate">
                                    {{ $product->sku ?: 'No SKU' }}
                                </div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600">
                                    {{ $product->category->name ?? 'General' }}
                                </span>
                            </div>

                            <!-- 3-Dot Action Dropdown Menu -->
                            <div class="absolute top-3.5 right-3" x-data="{ open: false }">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                                >
                                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                </button>

                                <div
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-1 w-44 rounded-xl bg-white shadow-xl border border-gray-100 py-1.5 z-20 text-xs"
                                    style="display: none;"
                                >
                                    <button
                                        type="button"
                                        wire:click="$dispatch('show-product', { product: {{ $product->id }} })"
                                        @click="open = false"
                                        class="w-full text-left px-3.5 py-2 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
                                    >
                                        <x-heroicon-o-eye class="w-4 h-4 text-gray-400" />
                                        <span>View Details</span>
                                    </button>

                                    @can('admin')
                                        <button
                                            type="button"
                                            wire:click="$dispatch('edit-product', { product: {{ $product->id }} })"
                                            @click="open = false"
                                            class="w-full text-left px-3.5 py-2 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
                                        >
                                            <x-heroicon-o-pencil-square class="w-4 h-4 text-amber-500" />
                                            <span>Edit Product</span>
                                        </button>

                                        <div class="border-t border-gray-100 my-1"></div>

                                        <button
                                            type="button"
                                            wire:click="$dispatch('open-delete-modal', {
                                                component: 'products.product-catalog',
                                                method: 'deleteProduct',
                                                params: { id: {{ $product->id }} },
                                                title: 'Delete Product?',
                                                description: 'Are you sure you want to delete {{ addslashes($product->name) }}?'
                                            })"
                                            @click="open = false"
                                            class="w-full text-left px-3.5 py-2 text-red-600 hover:bg-red-50 flex items-center gap-2 font-semibold"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4 text-red-500" />
                                            <span>Delete Product</span>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Stock Metrics -->
                        <div class="flex items-center justify-between pt-1 border-t border-gray-50">
                            <div>
                                <div class="text-xs text-gray-400 font-medium leading-none">Price</div>
                                <div class="text-base font-black text-gray-900 font-mono mt-0.5">
                                    @money($product->selling_price)
                                </div>
                                @can('admin')
                                    <div class="text-[10px] text-gray-400 font-mono">
                                        Cost: @money($product->purchase_price)
                                    </div>
                                @endcan
                            </div>

                            <!-- Stock Status Pill -->
                            <div class="text-right">
                                @if(!$product->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">
                                        Inactive
                                    </span>
                                @elseif($product->quantity <= 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Out of stock
                                    </span>
                                @elseif($product->quantity <= $product->min_stock)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Low: {{ $product->quantity }} {{ $product->unit->symbol ?? '' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $product->quantity }} {{ $product->unit->symbol ?? '' }} in stock
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Action Buttons (Large touch-friendly targets min 44px) -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <button
                                type="button"
                                wire:click="$dispatch('show-product', { product: {{ $product->id }} })"
                                class="h-11 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-xs font-bold flex items-center justify-center gap-1.5 transition-colors active:scale-95"
                            >
                                <x-heroicon-o-eye class="w-4 h-4 text-gray-500" />
                                <span>Details</span>
                            </button>

                            <button
                                type="button"
                                wire:click="$dispatch('open-quick-sale-modal', { productId: {{ $product->id }} })"
                                class="h-11 rounded-xl bg-brand hover:bg-brand-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-xs active:scale-95"
                            >
                                <x-heroicon-o-bolt class="w-4 h-4" />
                                <span>Quick Sell</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-100 p-6">
                        <x-heroicon-o-cube class="w-12 h-12 mx-auto text-gray-300 stroke-1" />
                        <div class="mt-2 text-sm font-semibold text-gray-700">No products found</div>
                        <p class="text-xs text-gray-400 mt-1">Try adjusting your search query or filters.</p>
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="mt-3 px-4 py-2 text-xs font-bold text-brand bg-brand-50 hover:bg-brand-100 rounded-xl transition-colors"
                        >
                            Clear All Filters
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 5. Pagination & Result Count -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <div>
                Showing <span class="font-bold text-gray-800">{{ $products->firstItem() ?? 0 }}</span>
                to <span class="font-bold text-gray-800">{{ $products->lastItem() ?? 0 }}</span>
                of <span class="font-bold text-gray-800">{{ $products->total() }}</span> products
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>

    </div>
</div>
