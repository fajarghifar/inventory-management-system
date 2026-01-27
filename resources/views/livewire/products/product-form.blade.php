<x-modal name="product-form-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Custom Header -->
        <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Edit Product' : 'Create Product' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Make changes to your product here. Click save when you\'re done.' : 'Add a new product to your inventory.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU -->
                @if($isEditing)
                    <x-form-input
                        name="sku"
                        label="SKU (Stock Keeping Unit)"
                        type="text"
                        wire:model="sku"
                        readonly
                        placeholder="e.g. SKU-1234-ABCD"
                        class="bg-muted text-muted-foreground cursor-not-allowed"
                    />
                @else
                    <!-- SKU Auto Generated -->
                    <div class="hidden">
                        <input type="hidden" wire:model="sku">
                    </div>
                @endif

                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Product Name"
                    placeholder="e.g. Wireless Mouse"
                    type="text"
                    wire:model="name"
                    required
                    class="{{ !$isEditing ? 'col-span-2' : '' }}"
                />
            </div>

            <!-- Row 2: Category & Unit -->
            <div class="flex flex-col sm:flex-row gap-6">
                <!-- Category -->
                <div class="w-full sm:w-1/2">
                    <x-searchable-select
                        id="category_id"
                        name="category_id"
                        label="Category"
                        wire:model="category_id"
                        :options="$categoryOptions"
                        placeholder="Select Category"
                        required
                    />
                </div>

                <!-- Unit -->
                <div class="w-full sm:w-1/2">
                    <x-searchable-select
                        id="unit_id"
                        name="unit_id"
                        label="Unit"
                        wire:model="unit_id"
                        :options="$unitOptions"
                        placeholder="Select Unit"
                        required
                    />
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <x-input-label for="description" value="Description" />
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    class="block w-full rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm"
                    placeholder="Optional description..."
                ></textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <!-- Prices (Forced Inline) -->
            <div class="flex flex-col sm:flex-row gap-6">
                <!-- Purchase Price -->
                <div class="w-full sm:w-1/2">
                    <x-form-input
                        name="purchase_price"
                        label="Purchase Price (Rp)"
                        type="number"
                        wire:model="purchase_price"
                        min="0"
                        placeholder="0"
                        required
                    />
                </div>

                <!-- Selling Price -->
                <div class="w-full sm:w-1/2">
                    <x-form-input
                        name="selling_price"
                        label="Selling Price (Rp)"
                        type="number"
                        wire:model="selling_price"
                        min="0"
                        placeholder="0"
                        required
                    />
                </div>
            </div>

            <!-- Row 5: Qty, Min Stock, Active -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Quantity -->
                <x-form-input
                    name="quantity"
                    label="Quantity"
                    type="number"
                    wire:model="quantity"
                    min="0"
                    placeholder="0"
                    required
                />

                <!-- Min Stock -->
                <x-form-input
                    name="min_stock"
                    label="Min Stock Alert"
                    type="number"
                    wire:model="min_stock"
                    min="0"
                    placeholder="0"
                    required
                />

                <!-- Is Active -->
                <div class="flex items-center h-full pt-8">
                    <label class="inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="w-6 h-6 rounded-full border-2 border-primary text-primary focus:ring-primary/20"
                        >
                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Active') }}
                        </span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t pt-4 border-gray-200">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-form-modal' })">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" />
                    {{ $isEditing ? __('Save Changes') : __('Create Product') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
