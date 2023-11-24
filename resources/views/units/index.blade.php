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
                        <a href="{{ route('units.create') }}" class="btn btn-icon btn-outline-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        </a>
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
