<x-app-layout title="Units">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Units') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-unit')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Create Unit') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:units.unit-table />
        </div>
    </div>

    <livewire:units.unit-form />
    <livewire:units.unit-detail />
</x-app-layout>
