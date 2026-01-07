<x-guest-layout>

    <!-- Header Section -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Reset password
        </h1>
        <p class="text-sm text-gray-500">
            Enter your new password below
        </p>
    </div>

    <!-- Form Container -->
    <div class="mt-6 bg-white p-6 md:p-8 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Field -->
            <x-form-input
                type="email"
                name="email"
                label="Email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="email"
                placeholder="m@example.com"
            />

            <!-- New Password Field -->
            <x-form-input
                type="password"
                name="password"
                label="Password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            />

            <!-- Confirm Password Field -->
            <x-form-input
                type="password"
                name="password_confirmation"
                label="Confirm Password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            />

            <!-- Submit Button -->
            <x-button class="w-full mt-2">
                Reset Password
            </x-button>
        </form>
    </div>

</x-guest-layout>
