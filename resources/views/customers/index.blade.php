<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Customers') }}
            </h2>
            <x-button href="{{ route('customers.create') }}">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Add Customer
            </x-button>
        </div>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <livewire:customer-table />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
