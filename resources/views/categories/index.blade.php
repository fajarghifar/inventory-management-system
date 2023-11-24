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
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Product Categories') }}
                        </h3>
                    </div>
                    <div class="card-actions">
                        <x-actions.create route="{{ route('categories.create') }}"/>
                    </div>
                </div>
                <div class="card-body">
                    <livewire:power-grid.categories-table/>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
