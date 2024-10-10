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
        <div class="container container-xl">
            <x-alert/>

            @livewire('tables.product-table')
        </div>
    @endif
</div>
@endsection
