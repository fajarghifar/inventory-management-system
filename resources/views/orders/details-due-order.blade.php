@extends('dashboard.body.main')

@section('content')
<!-- BEGIN: Header -->
<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg></div>
                        Due Order Details
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END: Header -->

<!-- BEGIN: Main Page Content -->
<div class="container-xl px-4">
    <div class="row">

        <!-- BEGIN: Information Customer -->
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    Information Customer
                </div>
                <div class="card-body">
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (customer name) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Name</label>
                            <div class="form-control form-control-solid">{{ $order->customer->name }}</div>
                        </div>
                        <!-- Form Group (customer email) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Email</label>
                            <div class="form-control form-control-solid">{{ $order->customer->email }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (customer phone number) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Phone</label>
                            <div class="form-control form-control-solid">{{ $order->customer->phone }}</div>
                        </div>
                        <!-- Form Group (order date) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Order Date</label>
                            <div class="form-control form-control-solid">{{ $order->order_date }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (no invoice) -->
                        <div class="col-md-6">
                            <label class="small mb-1">No Invoice</label>
                            <div class="form-control form-control-solid">{{ $order->invoice_no }}</div>
                        </div>
                        <!-- Form Group (payment type) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Payment Type</label>
                            <div class="form-control form-control-solid">{{ $order->payment_type }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (paid amount) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Paid Amount</label>
                            <div class="form-control form-control-solid">{{ $order->pay }}</div>
                        </div>
                        <!-- Form Group (due amount) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Due Amount</label>
                            <div class="form-control form-control-solid">{{ $order->due }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (due amount) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Tax</label>
                            <div class="form-control form-control-solid">{{ $order->vat }}</div>
                        </div>
                        <!-- Form Group (paid amount) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Total</label>
                            <div class="form-control form-control-solid">{{ $order->total }}</div>
                        </div>
                    </div>
                    <!-- Form Group (address) -->
                    <div class="mb-3">
                        <label  class="small mb-1">Address</label>
                        <div class="form-control form-control-solid">{{ $order->customer->address }}</div>
                    </div>

                    <!-- Submit button -->
                    <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal">Pay Due</button>
                    <a class="btn btn-primary" href="{{ URL::previous() }}">Back</a>
                </div>
            </div>
        </div>
        <!-- END: Information Customer -->


        <!-- BEGIN: Table Product -->
        <div class="col-xl-12">
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">List Product</div>

                <div class="card-body">
                    <!-- BEGIN: Products List -->
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Photo</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderDetails as $item)
                                    <tr>
                                        <td>{{ $loop->iteration  }}</td>
                                        <td>
                                            <div style="max-height: 80px; max-width: 80px;">
                                                <img class="img-fluid"  src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}">
                                            </div>
                                        </td>
                                        <td>{{ $item->product->product_name }}</td>
                                        <td>{{ $item->product->product_code }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->unitcost }}</td>
                                        <td>{{ $item->total }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END: Products List -->
                </div>
            </div>
        </div>
        <!-- END: Table Product -->
    </div>
</div>

<!-- BEGIN: Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title text-center mx-auto" id="modalCenterTitle">Invoice of {{ $order->customer->name }}<br/>Total Amount <b>${{ $order->total }}</b></h3>
            </div>

            <form action="{{ route('order.updateDueOrder') }}" method="POST">
                @csrf
                @method('put')
                <div class="modal-body">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $order->id }}">
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (paid amount) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Pay</label>
                                <div class="form-control form-control-solid">{{ $order->pay }}</div>
                            </div>
                            <!-- Form Group (due amount) -->
                            <div class="col-md-6">
                                <label class="small mb-1">Due</label>
                                <div class="form-control form-control-solid">{{ $order->due }}</div>
                            </div>
                        </div>
                        <!-- Form Group (pay now) -->
                        <div class="mb-3">
                            <label class="small mb-1" for="pay">Pay Now <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pay') is-invalid @enderror" id="pay" name="pay" placeholder="" value="{{ old('pay') }}" autocomplete="off"/>
                            @error('pay')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-lg btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-lg btn-success" type="submit">Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: Modal -->

<!-- END: Main Page Content -->
@endsection
