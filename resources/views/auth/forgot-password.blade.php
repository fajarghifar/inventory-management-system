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
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium leading-none text-gray-900">
                    Email
                </label>
                <input
                    id="email"
                    class="flex h-10 w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-all"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="m@example.com"
                />
                @error('email')
                    <p class="text-sm font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-gray-50 hover:bg-gray-900/90 h-10 px-4 py-2 w-full shadow-sm mt-2">
                Send Reset Link
            </button>
        </form>
    </div>

    <!-- Back to Login Link -->
    <div class="mt-6 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="font-semibold text-gray-900 underline-offset-4 hover:underline">
            Back to Login
        </a>
    </div>

</x-guest-layout>
