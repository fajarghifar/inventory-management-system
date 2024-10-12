@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($purchases->isEmpty())
    <x-empty
        title="No purchases found"
        message="Try adjusting your search or filter to find what you're looking for."
        button_label="{{ __('Add your first Purchase') }}"
        button_route="{{ route('purchases.create') }}"
    />
    @else
    <div class="container-xl">
        <x-alert/>

        @livewire('tables.purchase-table')
    </div>
    @endif
</div>
@endsection
