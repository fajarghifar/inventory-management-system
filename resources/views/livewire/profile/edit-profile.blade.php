    <form wire:submit="updateProfile" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="md:col-span-2">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Profile Information') }}</h3>
                <p class="mt-1 text-sm text-muted-foreground">{{ __("Update your account's profile information and email address.") }}</p>
            </div>

            <div class="space-y-2">
                <x-input-label for="name" :value="__('Name')" required />
                <x-text-input
                    wire:model="name"
                    id="name"
                    name="name"
                    type="text"
                    class="block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="username" :value="__('Username')" required />
                <x-text-input
                    wire:model="username"
                    id="username"
                    name="username"
                    type="text"
                    class="block w-full"
                    required
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('username')" />
            </div>

            <div class="space-y-2 md:col-span-2">
                <x-input-label for="email" :value="__('Email')" required />
                <x-text-input
                    wire:model="email"
                    id="email"
                    name="email"
                    type="email"
                    class="block w-full"
                    required
                    autocomplete="email"
                />
                <x-input-error :messages="$errors->get('email')" />
            </div>

            <div class="flex items-center justify-end gap-x-4 md:col-span-2 pt-6 border-t border-gray-200 mt-2">
                <x-primary-button wire:loading.attr="disabled">
                    <svg wire:loading wire:target="updateProfile" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
        </div>
    </form>
