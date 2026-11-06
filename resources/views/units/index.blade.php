<x-app-layout title="Units">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-foreground leading-tight">
                {{ __('Units') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-unit')" class="text-xs sm:text-sm py-2 px-3 sm:px-4">
                <x-heroicon-o-plus class="w-4 h-4 mr-1.5 sm:mr-2" />
                <span>{{ __('Create Unit') }}</span>
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <livewire:units.unit-table />
        </div>
    </div>

    <livewire:units.unit-form />
    <livewire:units.unit-detail />
</x-app-layout>
