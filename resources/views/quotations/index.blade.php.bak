@extends('layouts.dashboard')

@section('content')
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto my-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon">
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            Quotations
                        </h1>
                    </div>
                    <div class="col-auto my-4">
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary add-list my-1"><i class="fa-solid fa-plus me-3"></i>Add</a>
                        <a href="{{ route('quotations.index') }}" class="btn btn-danger add-list my-1"><i class="fa-solid fa-trash me-3"></i>Clear Search</a>
                    </div>
                </div>

                @include('partials._breadcrumbs')
            </div>
        </div>

        @include('partials.session')
    </header>

    <div class="container px-4 mt-n10">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row mx-n4">
                    <div class="col-lg-12 card-header mt-n4">
                        <form action="#" method="GET">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="form-group row align-items-center">
                                    <label for="row" class="col-auto">Row:</label>
                                    <div class="col-auto">
                                        <select class="form-control" id="row" name="row">
                                            <option value="10" @if(request('row') == '10')selected="selected"@endif>10</option>
                                            <option value="25" @if(request('row') == '25')selected="selected"@endif>25</option>
                                            <option value="50" @if(request('row') == '50')selected="selected"@endif>50</option>
                                            <option value="100" @if(request('row') == '100')selected="selected"@endif>100</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center justify-content-between">
                                    <label class="control-label col-sm-3" for="search">Search:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control me-1" name="search" placeholder="Search order" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="input-group-text bg-primary">
                                                    <i class="fa-solid fa-magnifying-glass font-size-20 text-white"></i>
                                                </button>
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
                                    <th scope="col">Date</th>
                                    <th scope="col">Reference</th>
                                    <th scope="col">@sortablelink('customer.name', 'Customer')</th>
                                    <th scope="col">@sortablelink('status', 'Status')</th>
                                    <th scope="col">@sortablelink('total_amount', 'Total')</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                @foreach ($quotations as $quotation)

                                    <tr>
                                        <th scope="row">
                                            {{ (($quotations->currentPage() * (request('row') ? request('row') : 10)) - (request('row') ? request('row') : 10)) + $loop->iteration  }}
                                        </th>
                                        <td>{{ $quotation->date }}</td>
                                        <td>{{ $quotation->reference }}</td>
                                        <td>{{ $quotation->customer_name }}</td>
                                        <td>{{ $quotation->status }}</td>
                                        <td>{{ $quotation->total_amount }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-outline-success btn-sm mx-1">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </div>
                                            <div class="d-flex">
                                                <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-outline-success btn-sm mx-1">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                            </div>
                                            <div class="d-flex">
                                                <form action="{{ route('quotations.destroy', $quotation) }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{ $quotations->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
