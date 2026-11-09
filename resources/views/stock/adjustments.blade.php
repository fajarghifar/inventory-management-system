<x-app-layout title="Stock Adjustments">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-foreground leading-tight">
                    {{ __('Stock Adjustments') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    Record authorized corrections, damage, shrinkage, and inventory counts.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('stock.movements') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs sm:text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 sm:px-4 py-2">
                    <x-heroicon-o-arrow-path-rounded-square class="w-4 h-4 mr-1.5 sm:mr-2" />
                    {{ __('Audit Log') }}
                </a>
                <x-primary-button x-data x-on:click="$dispatch('open-stock-adjustment-modal')" class="text-xs sm:text-sm py-2 px-3 sm:px-4">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5 sm:mr-2" />
                    {{ __('New Adjustment') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6">
            <livewire:stock-adjustments.stock-adjustment-table />
        </div>
    </div>

    <livewire:stock-adjustments.stock-adjustment-form />
</x-app-layout>

