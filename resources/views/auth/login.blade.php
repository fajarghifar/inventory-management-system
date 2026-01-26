<x-guest-layout title="Login">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Login</h1>
            <p class="text-sm text-muted-foreground">Enter your username below to login to your account</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username -->
            <x-form-input
                name="username"
                label="Username"
                type="text"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
                :messages="$errors->get('username')"
            />

            <!-- Password -->
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" :required="true" />
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-primary hover:underline" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-ring" name="remember">
                    <span class="ms-2 text-sm text-muted-foreground">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="w-full">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>

            <div class="mt-4 text-center text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" class="underline text-primary">
                    Sign up
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
