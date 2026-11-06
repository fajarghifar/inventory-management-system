<x-app-layout title="Dashboard">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <livewire:dashboard.dashboard />
        </div>
    </div>

    @can('admin')
        <livewire:stock-adjustments.stock-adjustment-form />
    @endcan

    <livewire:sales.quick-sale-modal />
</x-app-layout>
