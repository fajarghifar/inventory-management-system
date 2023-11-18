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
                    {{ $supplier->name }}
                </h2>
            </div>

{{--            <div class="col-auto ms-auto d-print-none">--}}
{{--                <div class="btn-list">--}}
{{--                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning d-none d-sm-inline-block">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>--}}
{{--                        {{ __('Edit') }}--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        @include('partials._breadcrumbs', ['model' => $supplier])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <div class="row">
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
                            <h3 class="card-title">
                                {{ __('Supplier Details') }}
                            </h3>
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
                            <a class="btn btn-info" href="{{ route('suppliers.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                                {{ __('Back') }}
                            </a>

                            <a class="btn btn-warning" href="{{ route('suppliers.edit', $supplier) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                {{ __('Edit') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@pushonce('page-scripts')
    {{--- ---}}
@endpushonce
