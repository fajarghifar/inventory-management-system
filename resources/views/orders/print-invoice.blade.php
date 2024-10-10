<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            {{ config('app.name') }}
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <!-- External CSS libraries -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}">
        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <!-- Custom Stylesheet -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
    </head>
    <body>
        <div class="invoice-16 invoice-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-inner-9" id="invoice_wrapper">
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="logo">
                                            <h1>Name Store</h1>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="invoice">
                                            <h1>
                                                Invoice # <span>{{ $order->invoice_no }}</span>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-info">
                                <div class="row">
                                    <div class="col-sm-6 mb-50">
                                        <div class="invoice-number">
                                            <h4 class="inv-title-1">
                                                Invoice date:
                                            </h4>
                                            <p class="invo-addr-1">
                                                {{ $order->order_date }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-50">
                                        <h4 class="inv-title-1">Customer</h4>
                                        <p class="inv-from-1">{{ $order->customer->name }}</p>
                                        <p class="inv-from-1">{{ $order->customer->phone }}</p>
                                        <p class="inv-from-1">{{ $order->customer->email }}</p>
                                        <p class="inv-from-2">{{ $order->customer->address }}</p>
                                    </div>
                                    <div class="col-sm-6 text-end mb-50">
                                        <h4 class="inv-title-1">Store</h4>
                                        <p class="inv-from-1">Name Store</p>
                                        <p class="inv-from-1">(+62) 123 123 123</p>
                                        <p class="inv-from-1">email@example.com</p>
                                        <p class="inv-from-2">Cirebon, Jawa Barat, Indonesia</p>
                                    </div>
                                </div>
                            </div>
                            <div class="order-summary">
                                <div class="table-outer">
                                    <table class="default-table invoice-table">
                                        <thead>
                                            <tr>
                                                <th class="align-middle">Item</th>
                                                <th class="align-middle text-center">Price</th>
                                                <th class="align-middle text-center">Quantity</th>
                                                <th class="align-middle text-center">Subtotal</th>
                                            </tr>
                                        </thead>

                                        <tbody>
{{--                                            @foreach ($orderDetails as $item)--}}
                                            @foreach ($order->details as $item)
                                            <tr>
                                                <td class="align-middle">
                                                    {{ $item->product->name }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ Number::currency($item->unitcost, 'EUR') }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ Number::currency($item->total, 'EUR') }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>
                                                        Subtotal
                                                    </strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ Number::currency($order->sub_total, 'EUR') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>Tax</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ Number::currency($order->vat, 'EUR') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>Total</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ Number::currency($order->total, 'EUR') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- <div class="invoice-informeshon-footer">
                                <ul>
                                    <li><a href="#">www.website.com</a></li>
                                    <li><a href="mailto:sales@hotelempire.com">info@example.com</a></li>
                                    <li><a href="tel:+088-01737-133959">+62 123 123 123</a></li>
                                </ul>
                            </div> --}}
                        </div>
                        <div class="invoice-btn-section clearfix d-print-none">
                            <a href="javascript:window.print()" class="btn btn-lg btn-print">
                                <i class="fa fa-print"></i>
                                Print Invoice
                            </a>
                            <a id="invoice_download_btn" class="btn btn-lg btn-download">
                                <i class="fa fa-download"></i>
                                Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/app.js') }}"></script>
    </body>
</html>
