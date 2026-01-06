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
            <div class="space-y-2">
                <label for="username" class="text-sm font-medium leading-none text-gray-900">
                    Username
                </label>
                <input
                    id="username"
                    class="flex h-10 w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-all"
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your username"
                />
                @error('username')
                    <p class="text-sm font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-medium leading-none text-gray-900">
                        Password
                    </label>
                    <!-- Forgot Password Link -->
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 underline-offset-4 hover:underline transition-colors">
                        Forgot password?
                    </a>
                </div>
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
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-gray-50 hover:bg-gray-900/90 h-10 px-4 py-2 w-full shadow-sm mt-2">
                Sign In
            </button>
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
