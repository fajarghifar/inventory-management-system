    <form wire:submit="updatePassword" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="md:col-span-2">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Update Password') }}</h3>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
            </div>

            <div class="space-y-2 md:col-span-2">
                <x-input-label for="current_password" :value="__('Current Password')" />
                <x-text-input wire:model="current_password" id="current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="password" :value="__('New Password')" />
                <x-text-input wire:model="password" id="password" name="password" type="password" class="block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>

            <div class="flex items-center justify-end gap-x-4 md:col-span-2 pt-6 border-t border-gray-200 mt-2">
                <x-primary-button wire:loading.attr="disabled">
                    <svg wire:loading wire:target="updatePassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
        </div>
    </form>
