@extends('layouts.tabler')

@pushonce('page-styles')
    <style>
        /*.btn .icon {*/
        /*    margin: 0;*/
        /*}*/
    </style>
@endpushonce


@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Empty page') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    {{--    @include('partials._breadcrumbs', ['model' => $users])--}}

    <div class="page-body">
        <div class="container-xl">
            <x-alert/>

            <div class="card">
                <div class="card-body">
                    <livewire:user-table/>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('page-scripts')
    {{--    --}}
@endpushonce
