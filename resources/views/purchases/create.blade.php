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
                    {{ __('Create Purchase') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf
                <div class="row">

                    <div class="col-xl-12">
                        <div class="card mb-4">
                            <div class="card-body">

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-4">
                                        <label for="purchase_date" class="small my-1">
                                            Date
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input name="purchase_date" id="purchase_date" type="date"
                                               class="form-control example-date-input

                                               @error('purchase_date') is-invalid @enderror"
                                               value="{{ old('purchase_date') ?? now()->format('Y-m-d') }}"
                                               required
                                        >

                                        @error('purchase_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small my-1" for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                            <option selected="" disabled="">Select a supplier:</option>

                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" @if(old('supplier_id') == $supplier->id) selected="selected" @endif>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>


                                    <div class="col-md-4">
                                        <label class="small mb-1" for="reference">
                                            Reference
                                        </label>

                                        <input type="text" class="form-control"
                                               id="reference" name="reference" value="QT" readonly>

                                        @error('reference')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                {{---
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-5">
                                        <label for="category_id" class="form-label required">
                                            {{ __('Category') }}
                                        </label>

                                        <select class="form-select form-control-solid @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                            <option selected="" disabled="">
                                                Select a category:
                                            </option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected="selected" @endif>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-5">
                                        <label for="product_id" class="form-label required">
                                            {{ __('Product') }}
                                        </label>

                                        <select class="form-select" id="product_id" name="product_id">
                                            <option disabled>Select a product:</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label"></label>

                                        <button class="btn btn-primary form-control addEventMore" type="button">

                                            Add Product
                                        </button>
                                    </div>
                                </div>
                                ---}}





                                <div class="table-responsive position-relative">

                                    <table class="table table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="align-middle">Product</th>
                                                <th class="align-middle text-center">Quantity</th>
                                                <th class="align-middle text-center">Price</th>
                                                <th class="align-middle text-center">Total</th>
                                                <th class="align-middle text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <span class="text-danger">
                                                        Please search & select products!
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                                
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to purchase?')">
                                    {{ __('Purchase') }}
                                </button>
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
