@extends('layouts.dashboard')

@push('page-styles')
    {{--- ---}}
@endpush

@section('content')
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon">
                            <i class="fa-solid fa-folder"></i>
                        </div>
                        {{ __('Add Category') }}
                    </h1>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>
</header>

<div class="container-xl px-2 mt-n10">
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        {{ __('Category Details') }}
                    </div>
                    <div class="card-body">
                        <livewire:name />

                        <livewire:slug />

                        <button class="btn btn-primary" type="submit">
                            {{ __('Create') }}
                        </button>

                        <a class="btn btn-danger" href="{{ route('categories.index') }}">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('page-scripts')
    {{--- ---}}
@endpush
