@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($purchases->isEmpty())
    <x-empty
        title="No purchases found"
        message="Try adjusting your search or filter to find what you're looking for."
        button_label="{{ __('Add your first Purchase') }}"
        button_route="{{ route('purchases.create') }}"
    />
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
                    <x-action.create route="{{ route('purchases.create') }}" />
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="align-middle text-center">{{ __('No.') }}</th>
                            <th scope="col" class="align-middle text-center">{{ __('Purchase No.') }}</th>
                            <th scope="col" class="align-middle text-center">{{ __('Supplier') }}</th>
                            <th scope="col" class="align-middle text-center">{{ __('Date') }}</th>
                            <th scope="col" class="align-middle text-center">{{ __('Total') }}</th>
                            <th scope="col" class="align-middle text-center">{{ __('Status') }}</th>
                            <th scope="col" class="align-middle text-center">{{ __('Action') }}</th>
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
                        <td class="align-middle text-center">
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
                                <span class="badge bg-green text-white text-uppercase">
                                    {{ __('APPROVED') }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <x-button.show class="btn-icon" route="{{ route('purchases.show', $purchase) }}"/>

                                <x-button.edit class="btn-icon" route="{{ route('purchases.edit', $purchase) }}"/>
                            </td>
                        @else
                            <td class="align-middle text-center">
                                <span class="badge bg-orange text-white text-uppercase">
                                    {{ __('PENDING') }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <x-button.show class="btn-icon" route="{{ route('purchases.show', $purchase) }}"/>
                                <x-button.edit class="btn-icon" route="{{ route('purchases.edit', $purchase) }}"/>
                                <x-button.delete class="btn-icon" route="{{ route('purchases.delete', $purchase) }}"/>
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
