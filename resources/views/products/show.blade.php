@extends('layouts.dashboard')

@push('page-styles')
    {{--- ---}}
@endpush

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                        Details Product
                    </h1>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>
</header>

<div class="container-xl px-2 mt-n10">
    <div class="row">
        <div class="col-xl-4">
            <!-- Product image card-->
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">Product Image</div>
                <div class="card-body text-center">
                    <!-- Product image -->
                    <img class="img-account-profile mb-2" src="{{ asset('assets/img/products/default.webp') }}" alt="" id="image-preview" />
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <!-- BEGIN: Product Code -->
            <div class="card mb-4">
                <div class="card-header">
                    Product Code
                </div>
                <div class="card-body">
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (type of product category) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Product code</label>
                            <div class="form-control form-control-solid">{{ $product->product_code  }}</div>
                        </div>
                        <!-- Form Group (type of product unit) -->
                        <div class="col-md-6 align-middle">
                            <label class="small mb-1">Barcode</label>
                            <div class="mt-1">
                              {!! $barcode !!}
                              </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Product Code -->

            <!-- BEGIN: Product Information -->
            <div class="card mb-4">
                <div class="card-header">
                    Product Information
                </div>
                <div class="card-body">
                    <!-- Form Group (product name) -->
                    <div class="mb-3">
                        <label class="small mb-1">Product name</label>
                        <div class="form-control form-control-solid">{{ $product->product_name }}</div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (type of product category) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Product category</label>
                            <div class="form-control form-control-solid">{{ $product->category->name  }}</div>
                        </div>
                        <!-- Form Group (type of product unit) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Product unit</label>
                            <div class="form-control form-control-solid">{{ $product->unit->name  }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (buying price) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Buying price</label>
                            <div class="form-control form-control-solid">{{ $product->buying_price  }}</div>
                        </div>
                        <!-- Form Group (selling price) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Selling price</label>
                            <div class="form-control form-control-solid">{{ $product->selling_price  }}</div>
                        </div>
                    </div>
                    <!-- Form Group (stock) -->
                    <div class="mb-3">
                        <label class="small mb-1">Stock</label>
                        <div class="form-control form-control-solid">{{ $product->stock  }}</div>
                    </div>

                    <!-- Submit button -->
                    <a class="btn btn-primary" href="{{ route('products.index') }}">Back</a>
                </div>
            </div>
            <!-- END: Product Information -->
        </div>
    </div>
</div>
<!-- END: Main Page Content -->
@endsection

@push('page-scripts')
    {{--- ---}}
@endpush
