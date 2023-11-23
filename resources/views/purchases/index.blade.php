@extends('layouts.tabler')

@section('content')
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
            <p class="empty-title">
                No purchases found
            </p>
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
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Purchases') }}
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
                                <th scope="col" class="align-middle">{{ __('No.') }}</th>
                                <th scope="col" class="align-middle">{{ __('Purchase No.') }}</th>
                                <th scope="col" class="align-middle">{{ __('Supplier') }}</th>
                                <th scope="col" class="align-middle text-center">{{ __('Date') }}</th>
                                <th scope="col" class="align-middle text-center">{{ __('Total') }}</th>
                                <th scope="col" class="align-middle text-center">{{ __('Status') }}</th>
                                <th scope="col" class="align-middle text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                    <tbody>
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $purchase->purchase_no }}
                            </td>
                            <td>
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="align-middle text-center">
                                {{ $purchase->purchase_date->format('d-m-Y') }}
                            </td>
                            <td class="align-middle text-center">
                                {{ Illuminate\Support\Number::currency($purchase->total_amount, 'EUR') }}
                            </td>

                            @if ($purchase->purchase_status == 1)
                                <td class="align-middle text-center">
                                    <span class="btn btn-success btn-sm text-uppercase">
                                        {{ __('APPROVED') }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-icon btn-outline-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </a>
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-icon btn-outline-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                    </a>
                                </td>
                            @else
                                <td class="align-middle text-center">
                                    <span class="btn btn-warning btn-sm text-uppercase">
                                        {{ __('PENDING') }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-icon btn-outline-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </a>

                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-icon btn-outline-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                    </a>

                                    <form action="{{ route('purchases.delete', $purchase) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-icon btn-outline-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
