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
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Units') }}
                        </h3>
                    </div>
                    <div class="card-actions">
                        <x-action.create route="{{ route('units.create') }}"/>
                    </div>
                </div>
                <div class="card-body">
                    <livewire:power-grid.units-table/>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
