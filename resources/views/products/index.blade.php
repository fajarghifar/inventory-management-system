@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        @if($products->isEmpty())
            <x-empty
                title="No products found"
                message="Try adjusting your search or filter to find what you're looking for."
                button_label="{{ __('Add your first Product') }}"
                button_route="{{ route('products.create') }}"
            />
        @else
            <div class="container-xl">
                {{---
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">
                                {{ __('Products') }}
                            </h3>
                        </div>

                        <div class="card-actions">
                            <x-action.create route="{{ route('products.create') }}"/>
                        </div>
                    </div>
                    <div class="card-body">
                        <livewire:power-grid.products-table/>
                    </div>
                </div>
                ---}}
                @livewire('tables.product-table')
            </div>
        @endif
    </div>
@endsection
