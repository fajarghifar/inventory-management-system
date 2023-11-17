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
                    {{ __('Create Unit') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Unit Details') }}
                                </h3>

                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <livewire:name />

                                        <livewire:slug />

                                        <div class="mb-3">
                                            <label for="short_code" class="form-label">
                                                {{ __('Short Code') }}
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
                                    </div>
                                </div>

                                <div class="card-footer text-end">
                                    <button class="btn btn-primary" type="submit">
                                        {{ __('Create') }}
                                    </button>

                                    <a class="btn btn-outline-warning" href="{{ route('units.index') }}">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@pushonce('page-scripts')
    {{--- ---}}
@endpushonce
