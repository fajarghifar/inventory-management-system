<x-app-layout title="Customers">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Customers') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-customer')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Create Customer') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:customers.customer-table />
        </div>
    </div>

    <livewire:customers.customer-form />
    <livewire:customers.customer-detail />
</x-app-layout>
