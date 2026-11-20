@props(['title' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover, interactive-widget=resizes-content">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}{{ !empty($title) ? ' | ' . $title : '' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <!-- PWA Configuration -->
        <x-pwa-meta />
    </head>
    <body class="font-sans text-foreground antialiased bg-background">
        <!-- Connectivity Offline/Online Banner -->
        <x-connectivity-banner />

        <div class="min-h-screen flex flex-col sm:justify-center items-center py-10 px-4 sm:px-6">
            <div class="w-full sm:max-w-md bg-card text-foreground rounded-2xl border border-border/80 shadow-2xl p-6 sm:p-8 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
        <x-pwa-install-prompt />
    </body>
</html>
