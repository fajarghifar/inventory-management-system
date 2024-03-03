@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        @if (!$orders)
            <x-empty title="No orders found" message="Try adjusting your search or filter to find what you're looking for."
                button_label="{{ __('Add your first Order') }}" button_route="{{ route('orders.create') }}" />
        @else
            <div class="container-xl">
                {{--        <x-card> --}}
                {{--            <x-slot:header> --}}
                {{--                <x-slot:title> --}}
                {{--                    {{ __('Orders') }} --}}
                {{--                </x-slot:title> --}}

                {{--                <x-slot:actions> --}}
                {{--                    <x-action.create route="{{ route('orders.create') }}" /> --}}
                {{--                </x-slot:actions> --}}
                {{--            </x-slot:header> --}}

                {{-- -
            <x-table.index>
                <x-slot:th>
                    <x-table.th>{{ __('No.') }}</x-table.th>
                    <x-table.th>{{ __('Invoice No.') }}</x-table.th>
                    <x-table.th>{{ __('Customer') }}</x-table.th>
                    <x-table.th>{{ __('Date') }}</x-table.th>
                    <x-table.th>{{ __('Payment') }}</x-table.th>
                    <x-table.th>{{ __('Total') }}</x-table.th>
                    <x-table.th>{{ __('Status') }}</x-table.th>
                    <x-table.th>{{ __('Actions') }}</x-table.th>
                </x-slot:th>
                <x-slot:tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <x-table.td>{{ $loop->iteration }}</x-table.td>
                            <x-table.td>{{ $order->invoice_no }}</x-table.td>
                            <x-table.td>{{ $order->customer->name }}</x-table.td>
                            <x-table.td>{{ $order->order_date->format('d-m-Y') }}</x-table.td>
                            <x-table.td>{{ $order->payment_type }}</x-table.td>
                            <x-table.td>{{ Number::currency($order->total, 'EUR') }}</x-table.td>
                            <x-table.td>
                                <x-badge class="{{ $order->order_status === 'complete' ? 'bg-green' : 'bg-orange' }}">
                                    {{ $order->order_status }}
                                </x-badge>
                            </x-table.td>
                            <x-table.td>
                                <x-button.show class="btn-icon" route="{{ route('orders.show', $order->uuid) }}"/>
                                <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-slot:tbody>
            </x-table.index>
            - --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <h3 class="mb-1">Success</h3>
                        <p>{{ session('success') }}</p>

                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                @endif
                <livewire:tables.order-table />
            </div>
        @endif
    </div>
@endsection
