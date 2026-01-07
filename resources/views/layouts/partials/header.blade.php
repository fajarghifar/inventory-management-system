<header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white/80 backdrop-blur-md px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 transition-all duration-300">

    <!-- Mobile Sidebar Toggle -->
    <button @click="sidebarOpen = true" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden hover:text-gray-900 transition-colors focus:outline-none">
        <span class="sr-only">Open sidebar</span>
        <x-heroicon-o-bars-3 class="h-6 w-6" />
    </button>

    <!-- Separator (Mobile) -->
    <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

    <!-- Search Bar (Flexible Spacer) -->
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <form class="relative flex flex-1" action="#" method="GET">
            <label for="search-field" class="sr-only">Search</label>
            <x-heroicon-o-magnifying-glass class="absolute inset-y-0 left-0 h-full w-5 text-gray-400 pointer-events-none" />
            <input id="search-field"
                   class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 bg-transparent sm:text-sm"
                   placeholder="Search globally..."
                   type="search"
                   name="search">
        </form>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-x-4 lg:gap-x-6">

        <!-- Notification Bell -->
        <button type="button" class="-m-2.5 p-2.5 text-slate-500 hover:text-slate-800 relative group transition-colors focus:outline-none">
            <span class="sr-only">View notifications</span>
            <x-heroicon-o-bell class="h-6 w-6 transition-transform group-hover:scale-110" aria-hidden="true" />

            <!-- Unread Badge with Ping Animation -->
            <span class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-red-600 ring-2 ring-white">
                <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 animate-ping"></span>
            </span>
        </button>

        <!-- Vertical Separator -->
        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
            <button @click="open = ! open" type="button" class="-m-1.5 flex items-center p-1.5 transition-opacity hover:opacity-80 focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <span class="sr-only">Open user menu</span>

                <!-- Avatar with Gradient -->
                <div class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-tr from-slate-200 to-slate-100 border border-slate-200 text-slate-600 font-semibold shadow-sm ring-1 ring-slate-950/5">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                </div>

                <!-- Name & Meta Info -->
                <span class="hidden lg:flex lg:items-center">
                    <div class="ml-4 flex flex-col items-start text-sm">
                        <span class="font-semibold leading-none text-slate-900">{{ Auth::user()->name }}</span>
                    </div>
                    <x-heroicon-o-chevron-down class="ml-2 h-4 w-4 text-slate-400 transition-transform duration-200" ::class="open ? 'rotate-180' : ''" aria-hidden="true" />
                </span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                 class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-xl bg-white params-y-1 shadow-xl ring-1 ring-gray-900/5 focus:outline-none overflow-hidden"
                 role="menu"
                 aria-orientation="vertical"
                 aria-labelledby="user-menu-button"
                 tabindex="-1"
                 style="display: none;">

                <!-- Menu Header -->
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900">Signed in as</p>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ Auth::user()->username }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.index') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors" role="menuitem" tabindex="-1">
                        <x-heroicon-o-user class="mr-3 h-4 w-4 text-gray-400 group-hover:text-indigo-600" />
                        {{ __('Profile') }}
                    </a>

                    <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors" role="menuitem" tabindex="-1">
                        <x-heroicon-o-cog-6-tooth class="mr-3 h-4 w-4 text-gray-400 group-hover:text-indigo-600" />
                        Settings
                    </a>
                </div>

                <div class="h-px bg-gray-100"></div>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}" class="py-1">
                    @csrf
                    <button type="submit" class="group flex w-full items-center px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 transition-colors" role="menuitem" tabindex="-1">
                        <x-heroicon-o-arrow-right-on-rectangle class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-600" />
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
