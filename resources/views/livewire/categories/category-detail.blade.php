<div>
    <x-modal name="category-detail-modal" title="Category Details" maxWidth="sm">
        @if($category)
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $category->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $category->slug }}</p>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-6">
                    <x-detail-item label="Name" :value="$category->name" />
                    <x-detail-item label="Slug" :value="$category->slug" />
                    <x-detail-item label="Description" :value="$category->description ?? '-'" />
                    <x-detail-item label="Created At" :value="$category->created_at->format('d M Y, H:i')" />
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', { name: 'category-detail-modal' })">
                        Close
                    </x-button>
                    <x-button type="button" wire:click="edit">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        Edit Category
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
