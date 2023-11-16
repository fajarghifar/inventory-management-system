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
                        {{ __('Categories') }}
                    </h2>
                </div>

                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('categories.create') }}" class="btn btn-outline-success d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                            {{ __('Create') }}
                        </a>
                    </div>
                </div>
            </div>

            @include('partials._breadcrumbs', ['model' => $categories])
        </div>
    </div>


    <div class="page-body">
        @if($categories->isEmpty())
            <div class="container-xl d-flex flex-column justify-content-center">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('static/illustrations/undraw_bug_fixing_oc7a.svg') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">No results found</p>
                    <p class="empty-subtitle text-secondary">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                            Add your first category
                        </a>
                    </div>
                </div>
            </div>
        @else
        <div class="container-xl">
            <x-alert/>

            <div class="card">
                <div class="card-body">
                    <livewire:power-grid.categories-table/>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@pushonce('page-scripts')
    {{--    --}}
@endpushonce
