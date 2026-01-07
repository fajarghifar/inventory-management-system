<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfile" class="mt-6 space-y-6">
        <x-form-input
            name="name"
            label="Name"
            wire:model="name"
            required
            class="max-w-xl"
        />

        <x-form-input
            type="email"
            name="email"
            label="Email"
            wire:model="email"
            required
            class="max-w-xl"
        />

        <div class="flex items-center gap-4">
            <x-button variant="secondary" href="{{ route('dashboard') }}">
                Cancel
            </x-button>

            <x-button type="submit">
                <span wire:loading.remove wire:target="updateProfile">Save</span>
                <span wire:loading wire:target="updateProfile">Saving...</span>
            </x-button>
        </div>
    </form>
</section>
