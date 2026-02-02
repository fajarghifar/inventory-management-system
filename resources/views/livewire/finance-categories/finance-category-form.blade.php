<x-modal name="finance-category-form-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Custom Header -->
        <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Edit Finance Category' : 'Create Finance Category' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Make changes to your category here. Click save when you\'re done.' : 'Add a new category to your finance records.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <!-- Name -->
            <x-form-input
                name="name"
                label="Name"
                type="text"
                wire:model="name"
                placeholder="e.g. Electricity, Employee Salary, Asset Sales"
                required
            />

            <div class="space-y-3">
                <x-input-label for="type" :value="__('Type')" />
                <div class="grid grid-cols-2 gap-4">
                    <!-- Income Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" wire:model="type" class="peer sr-only">
                        <div class="relative flex items-center justify-center gap-2 rounded-lg border border-input bg-background px-4 py-2.5 text-center transition-all hover:bg-accent hover:text-accent-foreground peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:ring-1 peer-checked:ring-emerald-500">
                            <x-heroicon-s-arrow-trending-up class="h-4 w-4" />
                            <span class="text-sm font-medium">Income</span>
                        </div>
                    </label>

                    <!-- Expense Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" wire:model="type" class="peer sr-only">
                        <div class="relative flex items-center justify-center gap-2 rounded-lg border border-input bg-background px-4 py-2.5 text-center transition-all hover:bg-accent hover:text-accent-foreground peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:ring-1 peer-checked:ring-red-500">
                             <x-heroicon-s-arrow-trending-down class="h-4 w-4" />
                            <span class="text-sm font-medium">Expense</span>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('type')" />
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <x-input-label for="description" :value="__('Description')" />
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    class="block w-full rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm"
                    placeholder="Optional description..."
                ></textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'finance-category-form-modal' })">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" />
                    {{ $isEditing ? __('Save Changes') : __('Create Category') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
