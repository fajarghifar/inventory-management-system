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
            <x-form-input
                name="slug"
                label="Slug"
                placeholder="e.g. electronics"
                required
                wire:model="slug"
                class="bg-gray-50"
            />
            <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate from name.</p>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                    placeholder="Optional description..."
                ></textarea>
                @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
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
