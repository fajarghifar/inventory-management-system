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
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium leading-none text-gray-900">
                    Password
                </label>
                <input
                    id="password"
                    class="flex h-10 w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-all"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                @error('password')
                    <p class="text-sm font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-gray-50 hover:bg-gray-900/90 h-10 px-4 py-2 w-full shadow-sm mt-2">
                Confirm
            </button>
        </form>
    </div>

</x-guest-layout>
