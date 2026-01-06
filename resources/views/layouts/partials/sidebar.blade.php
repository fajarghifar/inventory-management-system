<!-- ==============================================
     MOBILE SIDEBAR (DRAWER)
     ============================================== -->
<!-- Backdrop -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden"
     @click="sidebarOpen = false"></div>

<!-- Off-canvas Menu -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 lg:hidden shadow-xl flex flex-col"
     x-cloak>

    <!-- Mobile Header -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <span class="text-lg font-bold tracking-tight text-gray-900">Inventory<span class="text-indigo-600">App</span></span>
        <button @click="sidebarOpen = false" class="text-gray-500 hover:text-gray-700 focus:outline-none">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- Mobile Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
            Dashboard
        </x-nav-link>

        <!-- Inventory Group -->
        <x-nav-group label="Inventory" icon="cube" :active="request()->routeIs('products.*') || request()->routeIs('categories.*')">
            <x-nav-link href="#" :active="false">
                -> Product List
            </x-nav-link>
            <x-nav-link href="#" :active="false">
                -> Categories
            </x-nav-link>
            <x-nav-link href="#" :active="false">
                -> Units
            </x-nav-link>
        </x-nav-group>

        <x-nav-link href="#" :active="false" icon="users">
            Users
        </x-nav-link>

        <x-nav-link href="#" :active="false" icon="cog">
            Settings
        </x-nav-link>
    </nav>
</div>

<!-- ==============================================
     DESKTOP SIDEBAR (STATIC)
     ============================================== -->
<aside class="hidden lg:flex lg:flex-col lg:border-r lg:border-gray-200 lg:bg-white transition-all duration-300 ease-in-out flex-shrink-0"
       :class="sidebarCollapsed ? 'w-20' : 'w-72'">

    <!-- Sidebar Header (Logo + Toggle) -->
    <div class="flex items-center h-16 border-b border-gray-200 transition-all duration-300 bg-white"
         :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-4'">

        <!-- Logo (Visible only when Expanded) -->
        <div class="flex items-center overflow-hidden" x-show="!sidebarCollapsed">
            <div class="flex-shrink-0 bg-indigo-600 rounded-lg p-1">
                <x-heroicon-o-cube-transparent class="w-6 h-6 text-white" />
            </div>
            <span class="ml-3 text-xl font-bold tracking-tight text-gray-900 truncate"
                  x-transition:enter="transition ease-out duration-100 delay-100"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100">
                Inventory
            </span>
        </div>

        <!-- Toggle Button -->
        <button @click="toggleSidebar()"
                class="p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none transition-colors">

            <!-- Chevron Left (Standard Toggle) -->
            <x-heroicon-o-chevron-left class="w-5 h-5" x-show="!sidebarCollapsed" />

            <!-- Hamburger Icon (Centered when Collapsed) -->
            <div x-show="sidebarCollapsed" style="display: none;" class="text-indigo-600">
                <x-heroicon-o-bars-3 class="w-6 h-6" />
            </div>
        </button>
    </div>

    <!-- Sidebar Navigation (Scrollable) -->
    <div class="flex-1 flex flex-col overflow-y-auto px-3 py-4 scrollbar-hide hover:overflow-y-auto">
        <nav class="flex-1 space-y-1">

            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home" title="Dashboard">
                Dashboard
            </x-nav-link>

            <!-- Inventory Group -->
            <x-nav-group label="Inventory" icon="cube" :active="request()->routeIs('products.*') || request()->routeIs('categories.*')">
                <x-nav-link href="#" :active="false">
                    -> Product List
                </x-nav-link>
                <x-nav-link href="#" :active="false">
                    -> Categories
                </x-nav-link>
                <x-nav-link href="#" :active="false">
                    -> Units
                </x-nav-link>
            </x-nav-group>

            <x-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.*')" icon="users" title="Customers">
                Customers
            </x-nav-link>

            <x-nav-link href="#" :active="false" icon="cog" title="Settings">
                Settings
            </x-nav-link>

        </nav>
    </div>
</aside>
