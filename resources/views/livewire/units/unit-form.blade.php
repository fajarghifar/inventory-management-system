<div>
    <x-modal name="unit-modal" :title="$isEditing ? 'Edit Unit' : 'Add New Unit'" maxWidth="md">
        <form wire:submit="save" class="space-y-6">
            <!-- Name -->
            <x-form-input
                name="name"
                label="Unit Name"
                placeholder="e.g. Kilogram"
                required
                wire:model="name"
            />

            <!-- Symbol -->
            <x-form-input
                name="symbol"
                label="Symbol"
                placeholder="e.g. kg"
                required
                wire:model="symbol"
            />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'unit-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Update Unit' : 'Save Unit' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
