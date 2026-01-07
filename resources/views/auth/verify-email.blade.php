<x-guest-layout>

    <!-- Header Section -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Verify your email
        </h1>
        <p class="text-sm text-gray-500">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        </p>
    </div>

    <!-- Session Status Alert -->
    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 p-4 rounded-md bg-green-50 text-green-700 text-sm font-medium border border-green-200">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <!-- Actions Container -->
    <div class="mt-6 bg-white p-6 md:p-8 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl space-y-4">

        <!-- Resend Verification Email Form -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-button class="w-full">
                Resend Verification Email
            </x-button>
        </form>

        <!-- Log Out Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button variant="secondary" class="w-full">
                Log Out
            </x-button>
        </form>
    </div>

</x-guest-layout>
