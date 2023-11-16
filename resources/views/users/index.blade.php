@extends('layouts.tabler')

@pushonce('page-styles')
    {{---
    <style>
        .btn .icon {
            margin: 0;
        }
    </style>
    ---}}
@endpushonce


@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center mb-3">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Users') }}
                    </h2>
                </div>

                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('users.create') }}" class="btn btn-outline-success d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                            {{ __('Create') }}
                        </a>
                    </div>
                </div>
            </div>

            @include('partials._breadcrumbs', ['model' => $users])
        </div>
    </div>


    <div class="page-body">
        <div class="container-xl">
            <x-alert/>

            <div class="card">
                <div class="card-body">
                    <livewire:power-grid.user-table/>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('page-scripts')
    {{--    --}}
@endpushonce
