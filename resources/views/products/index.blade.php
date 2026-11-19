<x-app-layout title="Products">
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <livewire:products.product-catalog />
        </div>
    </div>

    <livewire:products.product-form />
    <livewire:products.product-detail />
    <livewire:sales.quick-sale-modal />
</x-app-layout>
