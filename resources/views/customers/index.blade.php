@extends('layouts.tabler')

@pushonce('page-styles')
    {{--- ---}}
@endpushonce

@section('content')
<div class="page-body">
    @if($customers->isEmpty())
        <x-empty
            title="No customers found"
            message="Try adjusting your search or filter to find what you're looking for."
            button_label="{{ __('Add your first Customer') }}"
            button_route="{{ route('customers.create') }}"
        />
    @else
        <div class="container-xl">
            <div class="card">
                <div class="card-body">
                    <livewire:power-grid.customers-table/>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@pushonce('page-scripts')
    {{--    --}}
@endpushonce

