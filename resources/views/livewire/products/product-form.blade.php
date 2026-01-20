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
                    class="bg-gray-100 cursor-not-allowed text-gray-500"
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
                     <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
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
