<x-modal name="product-form-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 leading-tight">
                    {{ $isEditing ? 'Edit Product' : 'Create New Product' }}
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $isEditing ? 'Update product pricing, stock thresholds, and attributes.' : 'Add a new retail item to your shopping center catalog.' }}
                </p>
            </div>
            <button
                type="button"
                x-on:click="$dispatch('close-modal', { name: 'product-form-modal' })"
                class="text-gray-400 hover:text-gray-600 p-1"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <form wire:submit="save" class="space-y-6">

            <!-- SECTION 1: Basic Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-1.5">
                    <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand flex items-center justify-center text-xs font-bold">1</span>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Basic Information</h4>
                </div>

                <!-- Product Photo Upload & Preview -->
                <div class="bg-gray-50/60 p-3.5 rounded-2xl border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Product Photo (Optional)</label>
                    <div class="flex items-center gap-4">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-brand shadow-xs">
                        @elseif ($existingImageUrl)
                            <img src="{{ $existingImageUrl }}" class="w-16 h-16 rounded-2xl object-cover border border-gray-200 shadow-xs">
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-white border-2 border-dashed border-gray-200 flex items-center justify-center text-gray-400 shadow-2xs">
                                <x-heroicon-o-photo class="w-7 h-7 text-gray-300" />
                            </div>
                        @endif

                        <div class="flex-1">
                            <input type="file" wire:model="photo" id="product_photo_input" class="hidden" accept="image/*">
                            <label for="product_photo_input" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 shadow-2xs transition-colors">
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-brand" />
                                <span>{{ $photo || $existingImageUrl ? 'Change Photo' : 'Upload Photo' }}</span>
                            </label>
                            <span wire:loading wire:target="photo" class="text-xs text-brand font-semibold ml-2">Uploading preview...</span>
                            <p class="text-[11px] text-gray-400 mt-1">Recommended: 1:1 square photo, PNG/JPG up to 2MB.</p>
                            <x-input-error :messages="$errors->get('photo')" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <x-form-input
                        name="name"
                        label="Product Name"
                        placeholder="e.g. Ethiopian Red Pepper Berbere 1kg"
                        type="text"
                        wire:model="name"
                        required
                    />

                    <!-- SKU / Barcode -->
                    <x-form-input
                        name="sku"
                        label="Barcode / SKU"
                        type="text"
                        wire:model="sku"
                        placeholder="Scan barcode or leave blank to auto-generate"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Category -->
                    <div class="space-y-1.5">
                        <x-input-label for="category_id" :value="__('Category')" required />
                        <div wire:ignore>
                            <x-tom-select
                                id="category_id"
                                name="category_id"
                                wire:model="category_id"
                                :url="route('ajax.categories.search')"
                                method="POST"
                                placeholder="Select Category"
                                data-initial-label="{{ $categoryName }}"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('category_id')" />
                    </div>

                    <!-- Unit -->
                    <div class="space-y-1.5">
                        <x-input-label for="unit_id" :value="__('Unit of Measure')" required />
                        <div wire:ignore>
                            <x-tom-select
                                id="unit_id"
                                name="unit_id"
                                wire:model="unit_id"
                                :url="route('ajax.units.search')"
                                method="POST"
                                placeholder="Select Unit (e.g. kg, pcs, pack)"
                                data-initial-label="{{ $unitName }}"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('unit_id')" />
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Pricing -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-1.5">
                    <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand flex items-center justify-center text-xs font-bold">2</span>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Pricing (Ethiopian Birr - ETB)</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Purchase Price -->
                    <div class="space-y-1.5">
                        <x-input-label for="purchase_price" :value="__('Cost / Buying Price (ETB)')" required />
                        <x-currency-input
                            id="purchase_price"
                            wire:model.live.debounce.400ms="purchase_price"
                            placeholder="0.00"
                            required
                        />
                        <x-input-error :messages="$errors->get('purchase_price')" />
                    </div>

                    <!-- Selling Price -->
                    <div class="space-y-1.5">
                        <x-input-label for="selling_price" :value="__('Selling / Retail Price (ETB)')" required />
                        <x-currency-input
                            id="selling_price"
                            wire:model.live.debounce.400ms="selling_price"
                            placeholder="0.00"
                            required
                        />
                        <x-input-error :messages="$errors->get('selling_price')" />
                    </div>
                </div>

                <!-- Margin preview badge -->
                @php
                    $calcMargin = (float)$selling_price - (float)$purchase_price;
                    $calcMarginPct = (float)$purchase_price > 0 ? ($calcMargin / (float)$purchase_price) * 100 : 0;
                @endphp
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                    <span class="text-gray-500 font-medium">Estimated Gross Profit Margin:</span>
                    <span class="font-bold font-mono {{ $calcMargin >= 0 ? 'text-brand' : 'text-red-600' }}">
                        @money($calcMargin) ({{ round($calcMarginPct, 1) }}%)
                    </span>
                </div>
            </div>

            <!-- SECTION 3: Inventory & Stock Controls -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-1.5">
                    <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand flex items-center justify-center text-xs font-bold">3</span>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Inventory & Thresholds</h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Quantity -->
                    <x-form-input
                        name="quantity"
                        label="{{ $isEditing ? 'Current Stock Quantity' : 'Opening Stock Quantity' }}"
                        type="number"
                        wire:model="quantity"
                        min="0"
                        placeholder="0"
                        required
                    />

                    <!-- Min Stock Threshold -->
                    <x-form-input
                        name="min_stock"
                        label="Min Stock Alert Threshold"
                        type="number"
                        wire:model="min_stock"
                        min="0"
                        placeholder="5"
                        required
                    />

                    <!-- Active Status Toggle -->
                    <div class="flex flex-col justify-end pb-2">
                        <label class="inline-flex items-center cursor-pointer gap-2.5">
                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="w-5 h-5 rounded-lg border-2 border-brand text-brand focus:ring-brand/20"
                            >
                            <div>
                                <div class="text-xs font-bold text-gray-800">Active for Sale</div>
                                <div class="text-[10px] text-gray-400">Available in POS register</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Description & Notes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div class="space-y-1.5">
                        <x-input-label for="description" value="Public Description (Optional)" />
                        <textarea
                            id="description"
                            wire:model="description"
                            rows="2"
                            class="w-full text-xs rounded-xl border border-gray-200 bg-white p-2.5 text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand placeholder:text-gray-400"
                            placeholder="Optional product description..."
                        ></textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>

                    <div class="space-y-1.5">
                        <x-input-label for="notes" value="Internal Shop Notes (Confidential)" />
                        <textarea
                            id="notes"
                            wire:model="notes"
                            rows="2"
                            class="w-full text-xs rounded-xl border border-gray-200 bg-white p-2.5 text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand placeholder:text-gray-400"
                            placeholder="Internal supplier notes, lot tracking, etc..."
                        ></textarea>
                        <x-input-error :messages="$errors->get('notes')" />
                    </div>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-2.5 border-t border-gray-100 pt-4">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { name: 'product-form-modal' })"
                    class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                >
                    Cancel
                </button>

                <x-primary-button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 text-xs font-bold bg-brand hover:bg-brand-700 text-white rounded-xl shadow-xs transition-all active:scale-95 gap-1.5"
                >
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4" />
                    <span>{{ $isEditing ? 'Save Product Changes' : 'Save Product' }}</span>
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
