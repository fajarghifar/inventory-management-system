@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ __('Profile Image') }}
                        </h3>

                        <img id="image-preview"
                             class="img-account-profile mb-2"
                             src="{{ asset('assets/img/demo/user-placeholder.svg') }}"
                             alt=""
                        >
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">
                                {{ __('Supplier Details') }}
                            </h3>
                        </div>

                        <div class="card-actions">
                            <x-action.close route="{{ route('suppliers.index') }}" />
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email address</td>
                                    <td>{{ $supplier->email }}</td>
                                </tr>
                                <tr>
                                    <td>Phone number</td>
                                    <td>{{ $supplier->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>{{ $supplier->address }}</td>
                                </tr>
                                <tr>
                                    <td>Shop name</td>
                                    <td>{{ $supplier->shopname }}</td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td>{{ $supplier->type }}</td>
                                </tr>
                                <tr>
                                    <td>Account holder</td>
                                    <td>{{ $supplier->account_holder }}</td>
                                </tr>
                                <tr>
                                    <td>Account number</td>
                                    <td>{{ $supplier->account_number }}</td>
                                </tr>
                                <tr>
                                    <td>Bank name</td>
                                    <td>{{ $supplier->bank_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-end">
                        <x-button.edit class="btn btn-outline-warning" route="{{ route('suppliers.edit', $supplier) }}">
                            {{ __('Edit') }}
                        </x-button.edit>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
