<x-guest-layout>

    <!-- Header Section -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Forgot password?
        </h1>
        <p class="text-sm text-gray-500">
            No problem. Just let us know your email address and we will email you a password reset link.
        </p>
    </div>

    <!-- Session Status Alert -->
    @if (session('status'))
        <div class="mt-4 p-4 rounded-md bg-green-50 text-green-700 text-sm font-medium border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <!-- Form Container -->
    <div class="mt-6 bg-white p-6 md:p-8 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email Field -->
            <x-form-input
                type="email"
                name="email"
                label="Email"
                :value="old('email')"
                required
                autofocus
                placeholder="m@example.com"
            />

            <!-- Submit Button -->
            <x-button class="w-full mt-2">
                Send Reset Link
            </x-button>
        </form>
    </div>

    <!-- Back to Login Link -->
    <div class="mt-6 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="font-semibold text-gray-900 underline-offset-4 hover:underline">
            Back to Login
        </a>
    </div>

</x-guest-layout>
