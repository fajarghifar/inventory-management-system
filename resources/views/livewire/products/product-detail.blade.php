<div>
    <x-modal name="product-detail-modal" :title="''" maxWidth="lg">
        @if($product)
            <div class="p-6 space-y-6">
                <!-- Header Info -->
                <div class="flex items-center justify-between border-b border-border pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-foreground tracking-tight">{{ $product->name }}</h3>
                        <p class="text-sm text-muted-foreground font-mono">{{ $product->sku }}</p>
                    </div>
                    <div>
                        @if($product->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Category</label>
                        <p class="text-sm text-foreground font-medium">{{ $product->category->name ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Unit</label>
                        <p class="text-sm text-foreground font-medium">{{ $product->unit->name ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Selling Price</label>
                        <p class="text-sm text-foreground font-medium">{{ 'Rp ' . number_format($product->selling_price, 0, ',', '.') }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Purchase Price</label>
                        <p class="text-sm text-foreground font-medium">{{ 'Rp ' . number_format($product->purchase_price, 0, ',', '.') }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Stock</label>
                        <p class="text-sm text-foreground font-medium {{ $product->quantity <= $product->min_stock ? 'text-red-500' : '' }}">
                            {{ $product->quantity . ' ' . ($product->unit->symbol ?? '') }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Min Stock Alert</label>
                        <p class="text-sm text-foreground font-medium">{{ $product->min_stock }}</p>
                    </div>
                </div>

                <div class="border-t border-border pt-6 space-y-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Description</label>
                        <p class="text-sm text-foreground leading-relaxed">
                            {{ $product->description ?: 'No description provided.' }}
                        </p>
                    </div>

                    <div class="text-xs text-muted-foreground">
                        Last Updated: {{ $product->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-x-2 pt-4 border-t border-border">
                    <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' })">
                        {{ __('Close') }}
                    </x-secondary-button>
                    <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' }); $dispatch('edit-product', { product: {{ $product->id }} })">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        {{ __('Edit Product') }}
                    </x-primary-button>
                </div>
            </div>
        @else
            <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <span class="text-sm text-muted-foreground">{{ __('Loading details...') }}</span>
            </div>
        @endif
    </x-modal>
</div>
