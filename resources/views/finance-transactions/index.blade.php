<x-app-layout title="Finance Transactions">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Finance Transactions') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-finance-transaction')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Create Transaction') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:finance-transactions.finance-transaction-table />
        </div>
    </div>

    <livewire:finance-transactions.finance-transaction-form />
    <livewire:finance-transactions.finance-transaction-detail />
</x-app-layout>
