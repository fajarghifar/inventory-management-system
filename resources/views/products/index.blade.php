<x-app-layout title="Products">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-foreground leading-tight">
                {{ __('Products') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-product')" class="text-xs sm:text-sm py-2 px-3 sm:px-4">
                <x-heroicon-o-plus class="w-4 h-4 mr-1.5 sm:mr-2" />
                <span>{{ __('Create Product') }}</span>
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <livewire:products.product-table />
        </div>
    </div>

    <livewire:products.product-form />
    <livewire:products.product-detail />
    <livewire:sales.quick-sale-modal />
</x-app-layout>
