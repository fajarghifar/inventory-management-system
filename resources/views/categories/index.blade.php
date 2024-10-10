@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($categories->isEmpty())
        <x-empty
            title="No categories found"
            message="Try adjusting your search or filter to find what you're looking for."
            button_label="{{ __('Add your first Category') }}"
            button_route="{{ route('categories.create') }}"
        />
    @else
        <div class="container-xl">
            <x-alert/>

            @livewire('tables.category-table')
        </div>
    @endif
</div>
@endsection
