<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <x-button x-data x-on:click="$dispatch('create-category')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Add Category
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <livewire:categories.category-table />
            </div>
        </div>
    </div>

    <livewire:categories.category-form />
    <livewire:categories.category-detail />
</x-app-layout>
