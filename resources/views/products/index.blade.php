<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <x-button x-data x-on:click="$dispatch('create-product')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Add Product
            </x-button>
        </div>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <livewire:products.product-table />
                </div>
            </div>
        </div>
    </div>
    <livewire:products.product-form />
    <livewire:products.product-detail />
</x-app-layout>
