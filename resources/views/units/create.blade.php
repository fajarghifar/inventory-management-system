@extends('layouts.dashboard')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-folder"></i></div>
                        Add Unit
                    </h1>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>
</header>

<div class="container-xl px-2 mt-n10">
    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <div class="row">

            <div class="col-xl-12">
                <!-- BEGIN: Unit Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        Unit Details
                    </div>
                    <div class="card-body">
                        <livewire:name />

                        <livewire:slug />

                        <div class="mb-3">
                            <label class="small mb-1" for="short_code">
                                Code
                            </label>

                            <input type="text"
                                   id="short_code"
                                   name="short_code"
                                   class="form-control form-control-solid @error('short_code') is-invalid @enderror"
                                   placeholder="Enter short code"
                                   value="{{ old('short_code') }}"
                            />

                            @error('short_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <button class="btn btn-primary" type="submit">Add</button>
                        <a class="btn btn-danger" href="{{ route('units.index') }}">Cancel</a>
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
