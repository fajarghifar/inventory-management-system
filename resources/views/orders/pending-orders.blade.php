@extends('layouts.tabler')

@section('content')
<div class="page-body">

    @include('partials.session')

    @if($orders->isEmpty())
    <div class="empty">
        <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <circle cx="12" cy="12" r="9" />
                <line x1="9" y1="10" x2="9.01" y2="10" />
                <line x1="15" y1="10" x2="15.01" y2="10" />
                <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
            </svg>
        </div>
        <p class="empty-title">No orders found</p>
        <p class="empty-subtitle text-secondary">
            Try adjusting your search or filter to find what you're looking for.
        </p>
        <div class="empty-action">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                Add your first Order
            </a>
        </div>
    </div>
    @else
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Orders: Pending') }}
                    </h3>
                </div>

                <div class="card-actions">
                    <a href="{{ route('orders.create') }}" class="btn btn-icon btn-outline-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Invoice</th>
                        <th scope="col">@sortablelink('customer.name', 'name')</th>
                        <th scope="col">@sortablelink('order_date', 'Date')</th>
                        <th scope="col">Payment</th>
                        <th scope="col">@sortablelink('total')</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>{{ $order->invoice_no }}</td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->order_date }}</td>
                            <td>{{ $order->payment_type }}</td>
                            <td>{{ $order->total }}</td>
                            <td class="text-center">
                                <span class="btn btn-warning btn-sm text-uppercase">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-icon btn-outline-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
   @endif
</div>
@endsection
