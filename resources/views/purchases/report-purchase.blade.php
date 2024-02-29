@extends('layouts.tabler')

@section('content')
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                        Purchase Report
                    </h1>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>
</header>

<div class="container-xl px-2 mt-n10">
    <form action="{{ route('purchases.getPurchaseReport') }}" method="POST">
        @csrf
        <div class="row">

            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Purchase Report Details
                    </div>
                    <div class="card-body">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small my-1" for="start_date">Start Date <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid example-date-input @error('start_date') is-invalid @enderror" name="start_date" id="date" type="date" value="{{ old('start_date') }}">
                                @error('purchase_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="small my-1" for="end_date">End Date <span class="text-danger">*</span></label>
                                <input class="form-control form-control-solid example-date-input @error('end_date') is-invalid @enderror" name="end_date" id="date" type="date" value="{{ old('end_date') }}">
                                @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit">Save</button>
                        <a class="btn btn-danger" href="{{ URL::previous() }}">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
