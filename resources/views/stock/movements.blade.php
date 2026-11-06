<x-app-layout title="Stock Movement Audit Trail">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-foreground leading-tight">
                    {{ __('Stock Movement Audit Trail') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    Complete immutable chronological ledger of all stock inputs, sales, deductions, and adjustments.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock.adjustments') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    <x-heroicon-o-adjustments-vertical class="w-4 h-4 mr-2" />
                    {{ __('Stock Adjustments') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6">
            <livewire:stock-movements.stock-movement-table />
        </div>
    </div>
</x-app-layout>

