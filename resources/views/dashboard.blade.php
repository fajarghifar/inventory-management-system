<x-guest-layout>

    <!-- Welcome Header -->
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
            Dashboard
        </h1>
        <p class="text-sm text-gray-500">
            You're logged in as <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span>!
        </p>
    </div>

    <!-- Actions Container -->
    <div class="mt-6 flex justify-center">

        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-gray-900 text-gray-50 hover:bg-gray-900/90 h-10 px-4 py-2 shadow-sm">
                Log Out
            </button>
        </form>

    </div>

</x-guest-layout>
