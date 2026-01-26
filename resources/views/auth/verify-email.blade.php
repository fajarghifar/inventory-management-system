<x-guest-layout title="Verify Email">
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Verify Email</h1>
            <div class="text-sm text-muted-foreground">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between flex-col space-y-4">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf

                <div>
                    <x-primary-button class="w-full">
                        {{ __('Resend Verification Email') }}
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full text-center">
                @csrf

                <button type="submit" class="underline text-sm text-muted-foreground hover:text-primary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
