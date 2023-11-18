@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Purchases') }}
                </h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('orders.create') }}" class="btn btn-outline-success d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        {{ __('Create') }}
                    </a>

                    <a href="{{ route('purchases.getPurchaseReport') }}"
                       class="btn btn-outline-info d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-export" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3" /></svg>
                        {{ __('Export') }}
                    </a>
                </div>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $purchases])
    </div>

    @include('partials.session')
</div>

<div class="page-body">
    @if($purchases->isEmpty())
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
            <p class="empty-title">No purchases found</p>
            <p class="empty-subtitle text-secondary">
                Try adjusting your search or filter to find what you're looking for.
            </p>
            <div class="empty-action">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    Add your first Purchase
                </a>
            </div>
        </div>
    @else
        <div class="container-xl">
            <div class="card">
                <div class="card-body">

                    <div class="row mx-n4">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">No.</th>
                                            <th scope="col">Purchase</th>
                                            <th scope="col">@sortablelink('supplier.name', 'Supplier')</th>
                                            <th scope="col">@sortablelink('purchase_date', 'Date')</th>
                                            <th scope="col">@sortablelink('total')</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchases as $purchase)
                                        <tr>
                                            <td>{{ $purchase->purchase_no }}</td>
                                            <td>{{ $purchase->supplier->name }}</td>
                                            <td>{{ $purchase->purchase_date }}</td>
                                            <td>{{ $purchase->total_amount }}</td>
                                            @if ($purchase->purchase_status == 1)
                                                <td>
                                                    <span class="btn btn-success btn-sm text-uppercase">approved</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-success btn-sm mx-1">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex">
                                                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-outline-success btn-sm mx-1">
                                                            <i class="fa-solid fa-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            @else
                                                <td>
                                                    <span class="btn btn-warning btn-sm text-uppercase">pending</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-icon btn-outline-success">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                                        </a>
                                                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-icon btn-outline-warning">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                                        </a>
                                                        <form action="{{ route('purchases.delete', $purchase) }}" method="POST">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-icon btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
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
    @endif
</div>
@endsection
