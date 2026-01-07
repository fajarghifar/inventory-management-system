<div>
    <x-modal name="product-detail-modal" title="Product Details" maxWidth="lg">
        @if($product)
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 font-mono">{{ $product->sku }}</p>
                    </div>
                    <div>
                        @if($product->is_active)
                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <x-detail-item label="Category" :value="$product->category->name ?? '-'" />
                    <x-detail-item label="Unit" :value="$product->unit->name ?? '-'" />

                    <x-detail-item label="Selling Price" :value="'Rp ' . number_format($product->selling_price, 0, ',', '.')" />
                    <x-detail-item label="Purchase Price" :value="'Rp ' . number_format($product->purchase_price, 0, ',', '.')" />

                    <x-detail-item label="Stock" :value="$product->quantity . ' ' . ($product->unit->symbol ?? '')" />
                    <x-detail-item label="Min Stock Alert" :value="$product->min_stock" />
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-6">
                    <x-detail-item label="Description" :value="$product->description ?: 'No description provided.'" />

                    <div class="text-xs text-gray-400">
                        Last Updated: {{ $product->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' })">
                        Close
                    </x-button>
                    <x-button type="button" wire:click="edit">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        Edit Product
                    </x-button>
                </div>
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                Loading details...
            </div>
        @endif
    </x-modal>
</div>
