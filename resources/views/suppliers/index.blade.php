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
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Suppliers') }}
                        </h3>
                    </div>

                    <div class="card-actions">
                        <x-actions.create route="{{ route('suppliers.create') }}" />
                    </div>
                </div>
                <div class="card-body">
                    <livewire:power-grid.suppliers-table/>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
