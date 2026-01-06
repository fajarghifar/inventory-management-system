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

            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-gray-50 hover:bg-gray-900/90 h-10 px-4 py-2 w-full shadow-sm">
                Resend Verification Email
            </button>
        </form>

        <!-- Log Out Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-gray-200 bg-white hover:bg-gray-100 hover:text-gray-900 h-10 px-4 py-2 w-full text-gray-900">
                Log Out
            </button>
        </form>
    </div>

</x-guest-layout>
