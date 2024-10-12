@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Purchases: Approved') }}
                    </h3>
                </div>

                <div class="card-actions">
                    <a href="{{ route('purchases.create') }}" class="btn btn-icon btn-outline-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="align-middle text-center w-1">No.</th>
                            <th scope="col" class="align-middle text-center">Purchase</th>
                            <th scope="col" class="align-middle text-center">Supplier</th>
                            <th scope="col" class="align-middle text-center">Date</th>
                            <th scope="col" class="align-middle text-center">Total</th>
                            <th scope="col" class="align-middle text-center">Status</th>
                            <th scope="col" class="align-middle text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $purchase)
                        <tr>
                            <td class="align-middle text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td class="align-middle text-center">
                                {{ $purchase->purchase_no }}
                            </td>
                            <td class="align-middle">
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="align-middle text-center">
                                {{ $purchase->purchase_date ? $purchase->purchase_date->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td class="align-middle text-center">
                                {{ Number::currency($purchase->total_amount, 'EUR') }}
                            </td>
                            <td class="align-middle text-center">
                                <span class="btn btn-{{ $purchase->purchase_status == 0 ? 'warning' : 'success' }} btn-sm text-uppercase">{{ $purchase->purchase_status == 0 ? 'pending' : 'approved' }}</span>
                            </td>
                            <td class="align-middle text-center">
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-icon btn-outline-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{--- ---}}
            </div>
        </div>
    </div>
</div>
@endsection
