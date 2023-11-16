@extends('layouts.tabler')

@pushonce('page-styles')
    {{--- ---}}
@endpushonce


@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center mb-3">
                <div class="col">
                    <h2 class="page-title">
                        {{ $user->name }}
                    </h2>
                </div>
            </div>

            @include('partials._breadcrumbs', ['model' => $user])
        </div>
    </div>


    <div class="page-body">
        <div class="container-xl">
            <x-alert/>

            <div class="card">
                <div class="card-body">
{{--                    <livewire:user-table/>--}}
                </div>
            </div>
        </div>
    </div>
@endsection


@pushonce('page-scripts')
    {{--    --}}
@endpushonce
