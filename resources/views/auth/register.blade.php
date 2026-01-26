<x-guest-layout title="Register">
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Create an account</h1>
            <p class="text-sm text-muted-foreground">Enter your details below to create your account</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <x-form-input
                name="name"
                label="Name"
                type="text"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                :messages="$errors->get('name')"
            />

            <!-- Username -->
            <div class="mt-4">
                <x-form-input
                    name="username"
                    label="Username"
                    type="text"
                    :value="old('username')"
                    required
                    autocomplete="username"
                    :messages="$errors->get('username')"
                />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-form-input
                    name="email"
                    label="Email"
                    type="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    :messages="$errors->get('email')"
                />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-form-input
                    name="password"
                    label="Password"
                    type="password"
                    required
                    autocomplete="new-password"
                    :messages="$errors->get('password')"
                />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-form-input
                    name="password_confirmation"
                    label="Confirm Password"
                    type="password"
                    required
                    autocomplete="new-password"
                    :messages="$errors->get('password_confirmation')"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="w-full">
                    {{ __('Register') }}
                </x-primary-button>
            </div>

            <div class="mt-4 text-center text-sm">
                Already have an account?
                <a href="{{ route('login') }}" class="underline text-primary">
                    Log in
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
