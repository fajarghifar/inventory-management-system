<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Management') }}
            </h2>
            <x-button href="{{ route('sales.create') }}">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                New Sale (POS)
            </x-button>
        </div>
    </x-slot>

    <div>
        <div class="max-w-full mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <livewire:sales.sales-table />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
