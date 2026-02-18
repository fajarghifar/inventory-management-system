<x-app-layout title="Create Purchase">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Create Purchase') }}
            </h2>
            <x-secondary-button href="{{ route('purchases.index') }}">
                &larr; {{ __('Back to List') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data"
                    x-data="purchaseForm({
                        items: {{ Js::from(old('items', [])) }},
                        supplier_id: {{ Js::from(old('supplier_id')) }},
                        status: {{ Js::from(old('status', 'draft')) }},
                        errors: {{ Js::from($errors->any() ? $errors->toArray() : []) }}
                    })"
                    @submit.prevent="submitForm">
                @csrf

                @include('purchases.form')

            </form>
        </div>
    </div>
</x-app-layout>
