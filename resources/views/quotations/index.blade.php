@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        @if($quotations->isEmpty())
            <x-empty
                title="No quotations found"
                message="Try adjusting your search or filter to find what you're looking for."
                button_label="{{ __('Add your first Quotation') }}"
                button_route="{{ route('quotations.create') }}"
            />
        @else
            <div class="container-xl">
                <x-alert/>

                @livewire('tables.quotation-table')
            </div>
        @endif
    </div>
@endsection
