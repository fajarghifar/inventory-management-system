<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Vite Resources -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            body { font-family: 'Inter', sans-serif; }
            [x-cloak] { display: none !important; }

            /* Hide Default Scrollbar */
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    </head>

    <!-- Body: Flex Layout with Sidebar and Main Content -->
    <body class="h-full antialiased text-gray-900 bg-gray-50 flex overflow-hidden"
          x-data="{
              sidebarOpen: false,
              sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
              toggleSidebar() {
                  this.sidebarCollapsed = !this.sidebarCollapsed;
                  localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
              }
          }">

        <!-- Sidebar (Mobile + Desktop) -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Global Header -->
            @include('layouts.partials.header')

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 flex flex-col">
                <div class="py-6 flex-1">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <!-- Page Header -->
                        @if (isset($header))
                            <div class="mb-4">
                                {{ $header }}
                            </div>
                        @endif

                        <!-- Page Content -->
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer -->
                @include('layouts.partials.footer')
            </main>

        </div>

    @livewireScripts
    <x-toast />
    </body>
</html>
