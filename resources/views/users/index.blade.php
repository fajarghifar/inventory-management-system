<x-app-layout title="Users">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-foreground leading-tight">
                {{ __('Users') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('open-modal', { name: 'user-form-modal' })" class="text-xs sm:text-sm py-2 px-3 sm:px-4">
                <x-heroicon-o-plus class="w-4 h-4 mr-1.5 sm:mr-2" />
                <span>{{ __('Create User') }}</span>
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <livewire:users.user-table />
        </div>
    </div>

    <livewire:users.user-form />
    <livewire:users.user-detail />
</x-app-layout>
