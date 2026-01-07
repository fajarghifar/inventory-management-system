<x-guest-layout>

    <!-- Header Section -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Welcome back
        </h1>
        <p class="text-sm text-gray-500">
            Enter your credentials to access your account
        </p>
    </div>

    <!-- Session Status Alert -->
    @if (session('status'))
        <div class="mt-4 p-4 rounded-md bg-green-50 text-green-700 text-sm font-medium border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <!-- Login Form Container -->
    <div class="mt-6 bg-white p-6 md:p-8 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Username Field -->
            <x-form-input
                name="username"
                label="Username"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your username"
            />

            <!-- Password Field -->
            <x-form-input
                type="password"
                name="password"
                label="Password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            >
                <x-slot name="cornerHint">
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 underline-offset-4 hover:underline transition-colors">
                        Forgot password?
                    </a>
                </x-slot>
            </x-form-input>

            <!-- Remember Me Checkbox -->
            <div class="flex items-center space-x-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 transition-colors"
                    name="remember"
                >
                <label for="remember_me" class="text-sm font-medium leading-none text-gray-500 cursor-pointer select-none">
                    Remember me
                </label>
            </div>

            <!-- Submit Button -->
            <x-button class="w-full mt-2">
                Sign In
            </x-button>
        </form>
    </div>

    <!-- Sign Up Link -->
    <div class="mt-6 text-center text-sm text-gray-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-gray-900 underline-offset-4 hover:underline">
            Sign up
        </a>
    </div>

</x-guest-layout>
