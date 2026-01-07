<x-guest-layout>

    <!-- Header Section -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Create an account
        </h1>
        <p class="text-sm text-gray-500">
            Enter your details below to create your account
        </p>
    </div>

    <!-- Register Form Container -->
    <div class="mt-6 bg-white p-6 md:p-8 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name Field -->
            <x-form-input
                name="name"
                label="Name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Enter your name"
            />

            <!-- Username Field -->
            <x-form-input
                name="username"
                label="Username"
                :value="old('username')"
                required
                autocomplete="username"
                placeholder="Enter a username"
            />

            <!-- Email Field -->
            <x-form-input
                type="email"
                name="email"
                label="Email"
                :value="old('email')"
                required
                autocomplete="email"
                placeholder="m@example.com"
            />

            <!-- Password Field -->
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
                Create account
            </x-button>
        </form>
    </div>

    <!-- Sign In Link -->
    <div class="mt-6 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-gray-900 underline-offset-4 hover:underline">
            Sign in
        </a>
    </div>

</x-guest-layout>
