<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <x-form-input
            type="password"
            name="current_password"
            label="Current Password"
            wire:model="current_password"
            required
            class="max-w-xl"
        />

        <x-form-input
            type="password"
            name="password"
            label="New Password"
            wire:model="password"
            required
            class="max-w-xl"
        />

        <x-form-input
            type="password"
            name="password_confirmation"
            label="Confirm Password"
            wire:model="password_confirmation"
            required
            class="max-w-xl"
        />

        <div class="flex items-center gap-4">
            <x-button variant="secondary" href="{{ route('dashboard') }}">
                Cancel
            </x-button>

            <x-button type="submit">
                <span wire:loading.remove wire:target="updatePassword">Save</span>
                <span wire:loading wire:target="updatePassword">Saving...</span>
            </x-button>
        </div>
    </form>
</section>
