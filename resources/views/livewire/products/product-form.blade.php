<div>
    <x-modal name="product-modal" :title="$isEditing ? 'Edit Product' : 'Add New Product'" maxWidth="2xl">
        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU -->
                @if($isEditing)
                <x-form-input
                    name="sku"
                    label="SKU (Stock Keeping Unit)"
                    placeholder="e.g. SKU-1234-ABCD"
                    required
                    wire:model="sku"
                    readonly
                    class="bg-gray-100 cursor-not-allowed"
                />
                @else
                    <!-- SKU Auto Generated -->
                    <input type="hidden" wire:model="sku">
                @endif

                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Product Name"
                    placeholder="e.g. Wireless Mouse"
                    required
                    wire:model="name"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <x-form-searchable-select
                        name="category_id"
                        label="Category"
                        :options="$categories"
                        wire:model="category_id"
                        placeholder="Select Category"
                        required
                    />
                </div>

                <!-- Unit -->
                <div>
                    <x-form-searchable-select
                        name="unit_id"
                        label="Unit"
                        :options="$units"
                        wire:model="unit_id"
                        placeholder="Select Unit"
                        required
                        optionLabel="name"
                        optionValue="id"
                    />
                </div>
            </div>

            <!-- Description -->
            <x-form-textarea
                name="description"
                label="Description"
                placeholder="Optional description..."
                wire:model="description"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Purchase Price -->
                <x-form-input
                    type="number"
                    name="purchase_price"
                    label="Purchase Price (Rp)"
                    placeholder="0"
                    required
                    wire:model="purchase_price"
                />

                <!-- Selling Price -->
                <x-form-input
                    type="number"
                    name="selling_price"
                    label="Selling Price (Rp)"
                    placeholder="0"
                    required
                    wire:model="selling_price"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Quantity -->
                <x-form-input
                    type="number"
                    name="quantity"
                    label="Quantity"
                    placeholder="0"
                    required
                    wire:model="quantity"
                />

                <!-- Min Stock -->
                <x-form-input
                    type="number"
                    name="min_stock"
                    label="Min Stock Alert"
                    placeholder="0"
                    required
                    wire:model="min_stock"
                />

                <!-- Is Active -->
                <div class="flex items-center h-full pt-6">
                    <label for="is_active" class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" id="is_active" wire:model="is_active" class="sr-only">
                            <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner"></div>
                            <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition"></div>
                        </div>
                        <div class="ml-3 text-gray-700 font-medium dark:text-gray-300">
                            Active Product
                        </div>
                    </label>
                    <style>
                        input:checked ~ .dot {
                            transform: translateX(100%);
                            background-color: #4ade80;
                        }
                    </style>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'product-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Update Product' : 'Save Product' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
