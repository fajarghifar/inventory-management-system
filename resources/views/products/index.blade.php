@extends('layouts.tabler')


@pushonce('page-styles')
    {{--- ---}}
@endpushonce


@section('content')
{{---
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Products') }}
                </h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('products.create') }}" class="btn btn-outline-success d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        {{ __('Create') }}
                    </a>
                </div>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $products])
    </div>
</div>`
---}}

    <div class="page-body">
        @if($products->isEmpty())
            <!---
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
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                            Add your first Product
                        </a>
                    </div>
                </div>
            </div>
            --->
            <div class="empty">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <line x1="9" y1="10" x2="9.01" y2="10" />
                        <line x1="15" y1="10" x2="15.01" y2="10" />
                        <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
                    </svg>
                </div>
                <p class="empty-title">No products found</p>
                <p class="empty-subtitle text-secondary">
                    Try adjusting your search or filter to find what you're looking for.
                </p>
                <div class="empty-action">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        Add your first Product
                    </a>
                </div>
            </div>

        @else
            <div class="container-xl">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">
                                {{ __('Products') }}
                            </h3>
                        </div>

                        <div class="card-actions">
                            <a href="{{ route('products.create') }}" class="btn btn-icon btn-outline-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            </a>
                        </div>
                    </div>
                    <livewire:power-grid.products-table/>
                    <div class="card-footer">

                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@pushonce('page-scripts')
    {{--    --}}
@endpushonce

