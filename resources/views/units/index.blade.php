<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Units') }}
            </h2>
            <x-button x-data x-on:click="$dispatch('create-unit')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Add Unit
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <livewire:units.unit-table />
            </div>
        </div>
    </div>

    <livewire:units.unit-form />
    <livewire:units.unit-detail />
</x-app-layout>
