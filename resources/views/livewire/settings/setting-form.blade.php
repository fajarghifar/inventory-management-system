<x-modal name="setting-form-modal" :title="''" maxWidth="2xl">
    <div class="p-6">
        <!-- Custom Header -->
        <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ __('Edit Setting') }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ __('Update the value of this setting.') }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <!-- Label (Readonly) -->
            <div class="space-y-2">
                <x-input-label for="key" :value="__('Setting Name')" />
                <div class="px-3 py-2 text-sm font-medium border rounded-md border-input bg-muted/50 text-foreground">
                    {{ $label }}
                </div>
            </div>

            <!-- Value -->
            <div class="space-y-2">
                <x-input-label for="value" :value="__('Value')" />
                <textarea
                    id="value"
                    wire:model="value"
                    rows="4"
                    class="block w-full rounded-md border-input bg-background shadow-sm focus:border-ring focus:ring-ring sm:text-sm"
                    placeholder="Enter value..."
                ></textarea>
                <x-input-error :messages="$errors->get('value')" />
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'setting-form-modal' })">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" />
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>
