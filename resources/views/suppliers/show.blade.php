@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ $supplier->name }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $supplier])
    </div>
</div>
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
                             src="{{ $supplier->photo ? asset('storage/' . $supplier->photo) : asset('assets/img/demo/user-placeholder.svg') }}"
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
                                    <td>{{ $supplier->type->label() }}</td>
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
                        <a class="btn btn-info" href="{{ route('suppliers.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                            {{ __('Back') }}
                        </a>
                        <x-button.edit class="btn btn-outline-warning" route="{{ route('suppliers.edit', $supplier->uuid) }}">
                            {{ __('Edit') }}
                        </x-button.edit>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
