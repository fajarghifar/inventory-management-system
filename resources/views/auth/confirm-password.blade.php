<x-guest-layout>

    <!-- Header Section -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Confirm access
        </h1>
        <p class="text-sm text-gray-500">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <!-- Confirm Password Form Container -->
    <div class="mt-6 bg-white p-6 md:p-8 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <!-- Password Field -->
            <x-form-input
                type="password"
                name="password"
                label="Password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />

            <!-- Submit Button -->
            <x-button class="w-full mt-2">
                Confirm
            </x-button>
        </form>
    </div>

</x-guest-layout>
