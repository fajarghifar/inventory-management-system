<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
</head>
<body>
    <div class="invoice-16 invoice-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="invoice-inner-9" id="invoice_wrapper">
                        <div class="invoice-top">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="logo"><h1>Name Store</h1></div>
                                </div>
                                <div class="col-lg-6 text-end">
                                    <div class="invoice">
                                        <h1>Invoice # <span>123456</span></h1>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="invoice-info">
                            <div class="row">
                                <div class="col-sm-6 mb-50">
                                    <h4 class="inv-title-1">Invoice date:</h4>
                                    <p>{{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-50">
                                    <h4 class="inv-title-1">Customer</h4>
                                    <p>{{ $customer->name }}</p>
                                    <p>{{ $customer->phone }}</p>
                                    <p>{{ $customer->email }}</p>
                                    <p>{{ $customer->address }}</p>
                                </div>
                                <div class="col-sm-6 text-end mb-50">
                                    <h4 class="inv-title-1">Store</h4>
                                    <p>Name Store</p>
                                    <p>(+62) 123 123 123</p>
                                    <p>email@example.com</p>
                                    <p>Cirebon, Jawa Barat, Indonesia</p>
                                </div>
                            </div>
                        </div>

                        <div class="order-summary">
                            <div class="table-outer">
                                <table class="table table-bordered invoice-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Item</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $subTotal = 0;
                                            $taxRate = 0.1; // 10%
                                        @endphp

                                        @foreach ($carts as $item)
                                            @php
                                                $itemTotal = $item->price * $item->quantity;
                                                $subTotal += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $item->name }}</td>
                                                <td class="text-center">{{ number_format($item->price, 2) }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-center">{{ number_format($itemTotal, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        @php
                                            $tax = $subTotal * $taxRate;
                                            $grandTotal = $subTotal + $tax;
                                        @endphp

                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                            <td class="text-center"><strong>{{ number_format($subTotal, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Tax ({{ $taxRate * 100 }}%)</strong></td>
                                            <td class="text-center"><strong>{{ number_format($tax, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                                            <td class="text-center"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="invoice-btn-section clearfix d-print-none mt-4">
                        <a class="btn btn-primary" href="{{ route('orders.index') }}">Back</a>
                        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal">Pay Now</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('orders.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title text-center mx-auto" id="modalTitle">
                        Invoice of {{ $customer->name }}<br/>
                        Total Amount ${{ number_format($grandTotal, 2) }}
                    </h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    
                    <div class="mb-3">
                        <label for="payment_type">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                            <option selected disabled>Select a payment:</option>
                            <option value="HandCash">HandCash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Due">Due</option>
                        </select>
                        @error('payment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pay">Pay Now <span class="text-danger">*</span></label>
                        <input type="text" name="pay" id="pay" class="form-control @error('pay') is-invalid @enderror" required autocomplete="off">
                        @error('pay')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" type="submit">Pay</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
