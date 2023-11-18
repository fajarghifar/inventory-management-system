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
                    {{ __('Orders') }}
                </h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('orders.create') }}" class="btn btn-outline-success d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        {{ __('Create') }}
                    </a>
                </div>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $orders])
    </div>

    @include('partials.session')
</div>

<div class="page-body">

    @if($orders->isEmpty())
        <div class="container-xl d-flex flex-column justify-content-center">
            <div class="empty">
                <div class="empty-img">
                    <img src="{{ asset('static/illustrations/undraw_bug_fixing_oc7a.svg') }}" height="128" alt="">
                </div>
                <p class="empty-title">No results found</p>
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
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body">
                <div class="row mx-n4">
                    <div class="col-lg-12 card-header mt-n4">
                        <form action="#" method="GET">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="form-group row align-items-center">
                                    <label for="row" class="col-auto">Row:</label>
                                    <div class="col-auto">
                                        <label>
                                            <select class="form-control" name="row">
                                                <option value="10" @if(request('row') == '10')selected="selected"@endif>10</option>
                                                <option value="25" @if(request('row') == '25')selected="selected"@endif>25</option>
                                                <option value="50" @if(request('row') == '50')selected="selected"@endif>50</option>
                                                <option value="100" @if(request('row') == '100')selected="selected"@endif>100</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center justify-content-between">
                                    <label class="control-label col-sm-3" for="search">Search:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control me-1" name="search" placeholder="Search order" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">@sortablelink('customer.name', 'name')</th>
                                    <th scope="col">@sortablelink('order_date', 'Date')</th>
                                    <th scope="col">Payment</th>
                                    <th scope="col">@sortablelink('total')</th>
                                    <th scope="col">@sortablelink('order_status', 'status')</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <th scope="row">{{ (($orders->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}</th>
                                        <td>{{ $order->invoice_no }}</td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>{{ $order->order_date }}</td>
                                        <td>{{ $order->payment_type }}</td>
                                        <td>{{ $order->total }}</td>
                                        <td>
                                            <span class="btn btn-success btn-sm text-uppercase">{{ $order->order_status }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-success btn-sm mx-1"><i class="fa-solid fa-eye"></i></a>
                                                <a href="{{ route('order.downloadInvoice', $order->id) }}" class="btn btn-outline-primary btn-sm mx-1"><i class="fa-solid fa-print"></i></a>
                                            </div>
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
    @endif



</div>
@endsection

@pushonce('page-scripts')
    {{--    --}}
@endpushonce
