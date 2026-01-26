<div>
    <x-modal name="category-detail-modal" :title="''" maxWidth="lg">
        @if($category)
            <div class="p-6">
                <!-- Custom Header -->
                <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                            Category Details
                        </h3>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Detailed information about the category {{ $category->name }}.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Name -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">
                            Name
                        </label>
                        <p class="text-sm text-foreground font-medium">{{ $category->name }}</p>
                    </div>

                    <!-- Slug -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">
                            Slug
                        </label>
                        <p class="text-sm text-foreground font-medium">{{ $category->slug }}</p>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">
                            Description
                        </label>
                        <p class="text-sm text-foreground leading-relaxed">
                            {{ $category->description ?? '-' }}
                        </p>
                    </div>

                    <div class="text-xs text-muted-foreground pt-4 border-t">
                        Last Updated: {{ $category->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 flex items-center justify-end gap-x-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'category-detail-modal' })">
                        {{ __('Close') }}
                    </x-secondary-button>
                    <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'category-detail-modal' }); $dispatch('edit-category', { category: {{ $category->id }} })">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        {{ __('Edit Category') }}
                    </x-primary-button>
                </div>
            </div>
        @else
            <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <span class="text-sm text-muted-foreground">{{ __('Loading details...') }}</span>
            </div>
        @endif
    </x-modal>
</div>
