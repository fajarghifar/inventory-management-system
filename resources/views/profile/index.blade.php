<x-app-layout title="Profile">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-foreground leading-tight">
                {{ __('Profile') }}
            </h2>
            <x-secondary-button href="{{ route('dashboard') }}" wire:navigate class="text-xs sm:text-sm py-2 px-3 sm:px-4">
                &larr; {{ __('Dashboard') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6">
            <livewire:profile.edit-profile />

            <livewire:profile.update-password />
        </div>
    </div>
</x-app-layout>
