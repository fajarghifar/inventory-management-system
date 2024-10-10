@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($units->isEmpty())
        <x-empty
            title="No units found"
            message="Try adjusting your search or filter to find what you're looking for."
            button_label="{{ __('Add your first Unit') }}"
            button_route="{{ route('units.create') }}"
        />
    @else
        <div class="container-xl">
            <x-alert/>

            @livewire('tables.unit-table')
        </div>
    @endif
</div>
@endsection
