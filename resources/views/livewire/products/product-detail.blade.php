<x-modal name="product-detail-modal" focusable maxWidth="xl">
    @if($product)
        <div class="p-6 space-y-5">
            <!-- Header with Product Thumbnail -->
            <div class="flex items-start gap-4 border-b border-gray-100 pb-4">
                @if($product->image_url)
                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="w-16 h-16 rounded-2xl object-cover border border-gray-200 flex-shrink-0 shadow-xs"
                    />
                @else
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand flex items-center justify-center font-bold text-lg flex-shrink-0 border border-brand-100 shadow-xs">
                        {{ strtoupper(substr($product->name, 0, 2)) }}
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight leading-snug truncate">{{ $product->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400 font-mono">{{ $product->sku ?: 'No Barcode' }}</span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-700">
                            {{ $product->category->name ?? 'General' }}
                        </span>
                    </div>
                </div>

                <div>
                    @if(!$product->is_active)
                        <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/20">
                            Inactive
                        </span>
                    @elseif($product->quantity <= 0)
                        <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 ring-1 ring-inset ring-red-600/20">
                            Out of Stock
                        </span>
                    @elseif($product->quantity <= $product->min_stock)
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                            Low Stock
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            In Stock
                        </span>
                    @endif
                </div>
            </div>

            <!-- Key Figures Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <!-- Retail Selling Price -->
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider block">Retail Price</span>
                    <span class="text-base font-black text-gray-900 font-mono">@money($product->selling_price)</span>
                </div>

                <!-- Available Stock -->
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider block">Current Stock</span>
                    <span class="text-base font-bold font-mono {{ $product->quantity <= $product->min_stock ? 'text-amber-600' : 'text-emerald-700' }}">
                        {{ $product->quantity }} {{ $product->unit->symbol ?? '' }}
                    </span>
                </div>

                <!-- Unit of Measure -->
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider block">Unit of Measure</span>
                    <span class="text-sm font-bold text-gray-800">
                        {{ $product->unit->name ?? 'Standard' }} ({{ $product->unit->symbol ?? 'pcs' }})
                    </span>
                </div>

                @can('admin')
                    <!-- Cost Price (Admin confidential) -->
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider block">Cost / Buying Price</span>
                        <span class="text-sm font-semibold text-gray-700 font-mono">@money($product->purchase_price)</span>
                    </div>

                    <!-- Gross Margin (Admin confidential) -->
                    @php
                        $m = $product->selling_price - $product->purchase_price;
                        $mPct = $product->purchase_price > 0 ? ($m / $product->purchase_price) * 100 : 0;
                    @endphp
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider block">Profit Margin</span>
                        <span class="text-sm font-bold font-mono {{ $m >= 0 ? 'text-brand' : 'text-red-600' }}">
                            @money($m) ({{ round($mPct, 1) }}%)
                        </span>
                    </div>

                    <!-- Min Alert Threshold -->
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider block">Min Alert Level</span>
                        <span class="text-sm font-bold text-gray-800 font-mono">{{ $product->min_stock }} {{ $product->unit->symbol ?? '' }}</span>
                    </div>
                @endcan
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-gray-400">Description</label>
                    <p class="text-xs text-gray-700 leading-relaxed bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                        {{ $product->description }}
                    </p>
                </div>
            @endif

            <!-- Internal Notes (Admin only) -->
            @can('admin')
                @if($product->notes)
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-400">Internal Shop Notes</label>
                        <p class="text-xs text-gray-700 font-mono leading-relaxed bg-amber-50/50 p-2.5 rounded-xl border border-amber-100/60">
                            {{ $product->notes }}
                        </p>
                    </div>
                @endif
            @endcan

            <!-- Actions Bar -->
            <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100">
                <button
                    type="button"
                    wire:click="$dispatch('close-modal', { name: 'product-detail-modal' }); $dispatch('open-quick-sale-modal', { productId: {{ $product->id }} })"
                    class="px-4 py-2 text-xs font-bold text-white bg-brand hover:bg-brand-700 rounded-xl transition-all active:scale-95 flex items-center gap-1.5 shadow-xs"
                >
                    <x-heroicon-o-bolt class="w-4 h-4" />
                    <span>Quick Direct Sale</span>
                </button>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' })"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                    >
                        Close
                    </button>

                    @can('admin')
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' }); $dispatch('edit-product', { product: {{ $product->id }} })"
                            class="px-4 py-2 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors flex items-center gap-1"
                        >
                            <x-heroicon-o-pencil-square class="w-4 h-4 text-amber-600" />
                            <span>Edit</span>
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    @else
        <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand"></div>
            <span class="text-xs text-gray-400">Loading product details...</span>
        </div>
    @endif
</x-modal>
