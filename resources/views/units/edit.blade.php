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
                    {{ __('Edit Unit') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $unit])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <form action="{{ route('units.update', $unit) }}" method="POST">
                @csrf
                @method('put')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Unit Details') }}
                                </h3>

                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                Unit Name
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   class="form-control form-control-solid @error('name') is-invalid @enderror"
                                                   value="{{ old('name', $unit->name) }}"
                                            >

                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="slug" class="form-label">
                                                {{ __('Slug') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   class="form-control @error('slug') is-invalid @enderror"
                                                   id="slug"
                                                   name="slug"
                                                   value="{{ old('slug', $unit->slug) }}"
                                            >

                                            @error('slug')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="short_code" class="form-label">
                                                {{ __('Short Code') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   class="form-control @error('short_code') is-invalid @enderror"
                                                   id="short_code"
                                                   name="short_code"
                                                   value="{{ old('short_code', $unit->short_code) }}"
                                            >

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
                                        {{ __('Update') }}
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
<script>
    // Slug Generator
    const title = document.querySelector("#name");
    const slug = document.querySelector("#slug");
    title.addEventListener("keyup", function() {
        let preslug = title.value;
        preslug = preslug.replace(/ /g,"-");
        slug.value = preslug.toLowerCase();
    });
</script>
@endpushonce
