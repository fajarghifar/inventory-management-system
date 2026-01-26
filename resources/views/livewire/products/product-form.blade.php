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
            <!-- Row 1: SKU & Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU -->
                @if($isEditing)
                    <x-form-input
                        name="sku"
                        label="SKU (Stock Keeping Unit)"
                        type="text"
                        wire:model="sku"
                        readonly
                        class="bg-muted text-muted-foreground cursor-not-allowed"
                    />
                @else
                    <!-- SKU Auto Generated or Optional Input if needed, but backup hides it -->
                    <!-- Keeping it hidden/auto-generated as per backup logic -->
                     <div class="hidden">
                        <input type="hidden" wire:model="sku">
                    </div>
                    <!-- If we want to show strict alignment, maybe just show Name in full width if SKU is hidden?
                         But backup shows SKU grid col only if editing?
                         Backup logic:
                         @if($isEditing) ...sku input... @else ...hidden... @endif
                         Then Name next to it.
                    -->

                    <!-- Actually backup layout:
                        <div class="grid ...">
                            @if($isEditing) ... @else ... @endif
                             <x-form-input name="name" ... />
                        </div>
                        If create, SKU is hidden, so Name takes up its slot? No, grid gap applies.
                        Let's check backup again.
                        It puts them in the SAME grid container.
                        If SKU is hidden, the grid cell is empty? Or hidden element doesn't take space?
                        If hidden element doesn't take space, Name will be 1st cell.
                        This might look weird if 2 cols and only 1 visible.
                        However, I will follow the intent:
                        If Creating: Just display Name (maybe full width or just 1st col).
                        Let's make Name full-width if Creating, or just stick to the backup structure exactly.
                    -->
                     @if(!$isEditing)
                        <!-- Placeholder to keep grid alignment or just nothing? -->
                        <!-- Let's put Name first if creating? No, let's Stick to explicit structure -->
                     @endif
                @endif

                <!-- Name -->
                <x-form-input
                    name="name"
                    label="Product Name"
                    type="text"
                    wire:model="name"
                    placeholder="e.g. Wireless Mouse"
                    required
                    class="{{ !$isEditing ? 'col-span-2' : '' }}"
                />
                <!-- I added col-span-2 logic to make Name full width if SKU is hidden, which looks better -->
            </div>

            <!-- Row 2: Category & Unit -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <x-input-label for="category_id" value="Category" required />
                    <x-searchable-select
                        id="category_id"
                        name="category_id"
                        wire:model="category_id"
                        :options="$categoryOptions"
                        placeholder="Select Category"
                    />
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="unit_id" value="Unit" required />
                    <x-searchable-select
                        id="unit_id"
                        name="unit_id"
                        wire:model="unit_id"
                        :options="$unitOptions"
                        placeholder="Select Unit"
                    />
                    <x-input-error :messages="$errors->get('unit_id')" />
                </div>
            </div>

            <!-- Row 3: Description -->
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

            <!-- Row 4: Prices -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form-input
                    name="purchase_price"
                    label="Purchase Price (Rp)"
                    type="number"
                    wire:model="purchase_price"
                    min="0"
                    placeholder="0"
                    required
                />

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

            <!-- Row 5: Qty, Min Stock, Active -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-form-input
                    name="quantity"
                    label="Quantity"
                    type="number"
                    wire:model="quantity"
                    min="0"
                    placeholder="0"
                    required
                />

                <x-form-input
                    name="min_stock"
                    label="Min Stock Alert"
                    type="number"
                    wire:model="min_stock"
                    min="0"
                    placeholder="0"
                    required
                />

                <div class="flex items-center h-full pt-8">
                     <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
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
