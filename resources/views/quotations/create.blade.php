@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('New Quotation') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

@include('partials.session')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <div class="row">
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header">
                            Products
                        </div>
                        <div class="card-body">
                            <livewire:search-product/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="{{ route('quotations.store') }}" method="POST">
                                @csrf
                                <div class="row gx-3 mb-3">
                                    <div class="col">
                                        <label class="small mb-1" for="date">
                                            Date
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input class="form-control @error('date') is-invalid @enderror" name="date" id="date" type="date" value="{{ old('purchase_date', now()->format('Y-m-d'))}}" required>

                                        @error('date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col">
                                        <label class="small mb-1" for="customer_id">
                                            Customer
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                            <option selected="" disabled="">
                                                Select a customer:
                                            </option>

                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" @selected( old('customer_id') == $customer->id)>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('customer_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="col">
                                        <label for="status" class="small mb-1">
                                            Status
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select class="form-select" name="status" id="status" required>
                                            @foreach(\App\Enums\QuotationStatus::cases() as $status)
                                            <option value="{{ $status->value  }}">
                                                {{ $status->label() }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label for="reference" class="small mb-1">
                                            {{ __('Reference') }}
                                        </label>

                                        <input type="text"
                                                id="reference"
                                                name="reference"
                                                class="form-control"
                                                value="QT"
                                                readonly
                                        >

                                        @error('reference')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <livewire:product-cart :cartInstance="'quotation'"/>

                                <div class="col-md-12 mt-4">
                                    <div class="form-group">
                                        <label for="note">
                                            {{ __('Notes') }}
                                        </label>
                                        <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="d-flex flex-wrap">
                                        <button type="submit" class="btn btn-success add-list mx-1">
                                            {{ __('Create Quotation') }}
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
