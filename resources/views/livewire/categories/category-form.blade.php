<div>
    <x-modal name="category-modal" :title="$isEditing ? 'Edit Category' : 'Add New Category'" maxWidth="md">
        <form wire:submit="save" class="space-y-6">
            <!-- Name -->
            <x-form-input
                name="name"
                label="Category Name"
                placeholder="e.g. Electronics"
                required
                wire:model.live="name"
            />

            <!-- Slug -->
            <div>
                <x-form-input
                    name="slug"
                    label="Slug"
                    placeholder="e.g. electronics"
                    wire:model="slug"
                    class="bg-gray-50"
                />
                <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate from name.</p>
            </div>

            <!-- Description -->
            <x-form-textarea
                name="description"
                label="Description"
                placeholder="Optional description..."
                wire:model="description"
            />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'category-modal' })">
                    Cancel
                </x-button>
                <x-button type="submit">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditing ? 'Update Category' : 'Save Category' }}
                    </span>
                    <span wire:loading wire:target="save">
                        Saving...
                    </span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
