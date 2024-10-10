@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($suppliers->isEmpty())
        <x-empty
            title="No suppliers found"
            message="Try adjusting your search or filter to find what you're looking for."
            button_label="{{ __('Add your first Supplier') }}"
            button_route="{{ route('suppliers.create') }}"
        />
    @else
        <div class="container-xl">
            <x-alert/>

            @livewire('tables.supplier-table')
        </div>
    @endif
</div>
@endsection
