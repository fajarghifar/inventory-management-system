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
                    {{ __('Edit Purchase') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $purchase])
    </div>
</div>

<div class="container-xl px-4">
    <div class="row">

        <!-- BEGIN: Information Supplier -->
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    Information Supplier
                </div>
                <div class="card-body">

                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="small mb-1">Name</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small mb-1">Email</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->email }}</div>
                        </div>
                    </div>

                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="small mb-1">Phone</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small mb-1">Order Date</label>
                            <div class="form-control form-control-solid">{{ $purchase->purchase_date }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (no invoice) -->
                        <div class="col-md-6">
                            <label class="small mb-1">No Purchase</label>
                            <div class="form-control form-control-solid">{{ $purchase->purchase_no }}</div>
                        </div>
                        <!-- Form Group (paid amount) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Total</label>
                            <div class="form-control form-control-solid">{{ $purchase->total_amount }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="small mb-1">Created By</label>
                            <div class="form-control form-control-solid">{{ $purchase->createdBy->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small mb-1">Updated By</label>
                            <div class="form-control form-control-solid">{{ $purchase->updatedBy->name ?? '-' }}</div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label  class="small mb-1">Address</label>
                        <div class="form-control form-control-solid">{{ $purchase->supplier->address }}</div>
                    </div>


                    @if ($purchase->purchase_status == 0)
                        <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                            @csrf
                            @method('put')
                            <input type="hidden" name="id" value="{{ $purchase->id }}">

                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this purchase?')">
                                Approve Purchase
                            </button>
                            <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                        </form>
                    @else
                        <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                    @endif
                </div>
            </div>
        </div>


        <div class="col-xl-12">
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">
                    List Product
                </div>

                <div class="card-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Photo</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Current Stock</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
{{--                                    @foreach ($purchaseDetails as $item)--}}

                                    @foreach ($purchase->details as $item)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration  }}</td>
                                        <td scope="row">
                                            <div style="max-height: 80px; max-width: 80px;">
                                                <img class="img-fluid"  src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}">
                                            </div>
                                        </td>
                                        <td scope="row">{{ $item->product->product_name }}</td>
                                        <td scope="row">{{ $item->product->product_code }}</td>
                                        <td scope="row"><span class="btn btn-warning">{{ $item->product->quantity }}</span></td>
                                        <td scope="row"><span class="btn btn-success">{{ $item->quantity }}</span></td>
                                        <td scope="row">{{ $item->unitcost }}</td>
                                        <td scope="row">
                                            <span  class="btn btn-primary">{{ $item->total }}</span>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
