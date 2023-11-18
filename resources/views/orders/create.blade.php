@extends('layouts.tabler')

@push('page-styles')
    {{--- ---}}
@endpush

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center mb-3">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Create Order') }}
                    </h2>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">

                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            Cart
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">QTY</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">SubTotal</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($carts as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td style="min-width: 170px;">
                                                <form action="{{ route('pos.updateCartItem', $item->rowId) }}" method="POST">
                                                    @csrf
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="qty" required value="{{ old('qty', $item->qty) }}">
                                                        <div class="input-group-append">
                                                            <button type="submit" class="btn btn-success border-none" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sumbit"><i class="fas fa-check"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->subtotal }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <form action="{{ route('pos.deleteCartItem', $item->rowId) }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1">Total Product</label>
                                    <div class="form-control form-control-solid fw-bold text-red">{{ Cart::count() }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1">Subtotal</label>
                                    <div class="form-control form-control-solid fw-bold text-red">{{ Cart::subtotal() }}</div>
                                </div>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1">Tax</label>
                                    <div class="form-control form-control-solid fw-bold text-red">{{ Cart::tax() }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1">Total</label>
                                    <div class="form-control form-control-solid fw-bold text-red">{{ Cart::total() }}</div>
                                </div>
                            </div>

    {{--                        <form action="{{ route('pos.createInvoice') }}" method="POST">--}}
                            <form action="{{ route('invoice.create') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="small mb-1" for="customer_id">
                                            Customer
                                            <span class="text-danger">*</span>
                                        </label>

    {{--                                    @dd($customers)--}}
    {{--                                    @dd($carts)--}}

                                        <select class="form-select form-control-solid @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                            <option selected="" disabled="">
                                                Select a customer:
                                            </option>

                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" @selected( old('customer_id') == $customer->id)>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach

    {{--                                        @foreach ($customers as $key => $customer)--}}
    {{--                                            <option value="{{ $customer }}" @selected( old('customer_id', $key) == $customer)>--}}
    {{--                                                {{ $customer }}--}}
    {{--                                            </option>--}}
    {{--                                        @endforeach--}}
                                        </select>

                                        @error('customer_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="d-flex flex-wrap align-items-center justify-content-center">
                                            <a href="{{ route('customers.create') }}" class="btn btn-primary add-list mx-1">
                                                Add Customer
                                            </a>

                                            <button type="submit" class="btn btn-success add-list mx-1">
                                                Create Invoice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="col-xl-6">
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header">
                            List Product
                        </div>

                        <div class="card-body">
                            <!-- BEGIN: Search products -->
                            <div class="col-lg-12">
                                <form action="{{ route('pos.index') }}" method="GET">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <div class="form-group row align-items-center">
                                            <label for="row" class="col-auto">Row:</label>
                                            <div class="col-auto">
                                                <select class="form-control" name="row">
                                                    <option value="10" @if(request('row') == '10')selected="selected"@endif>10</option>
                                                    <option value="25" @if(request('row') == '25')selected="selected"@endif>25</option>
                                                    <option value="50" @if(request('row') == '50')selected="selected"@endif>50</option>
                                                    <option value="100" @if(request('row') == '100')selected="selected"@endif>100</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row align-items-center justify-content-between">
                                            <label class="control-label col-sm-3" for="search">Search:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" id="search" class="form-control me-1" name="search" placeholder="Search product" value="{{ request('search') }}">

                                                    <div class="input-group-append d-flex">
                                                        <button type="submit" class="input-group-text bg-primary">
                                                            <i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i>
                                                        </button>

                                                        <a href="{{ route('pos.index') }}" class="input-group-text bg-danger">
                                                            <i class="fa-solid fa-trash font-size-20 text-white"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">@sortablelink('product_name', 'Name')</th>
                                                <th scope="col">@sortablelink('stock')</th>
                                                <th scope="col">@sortablelink('unit.name', 'unit')</th>
                                                <th scope="col">@sortablelink('selling_price', 'Price')</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($products as $product)
                                            <tr>
                                                <td>
                                                    <div style="max-height: 80px; max-width: 80px;">
                                                        <img class="img-fluid"  src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/img/products/default.webp') }}">
                                                    </div>
                                                    </td>
                                                    <td>{{ $product->product_name }}</td>
                                                    <td>{{ $product->stock }}</td>
                                                    <td>{{ $product->unit->name }}</td>
                                                    <td>{{ $product->selling_price }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <form action="{{ route('pos.addCartItem', $product) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                                            <input type="hidden" name="name" value="{{ $product->product_name }}">
                                                            <input type="hidden" name="price" value="{{ $product->selling_price }}">
                                                            <button type="submit" class="btn btn-icon btn-outline-primary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <th colspan="6" class="text-center" >
                                                    Data not found!
                                                </th>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@pushonce('page-scripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
@endpushonce
