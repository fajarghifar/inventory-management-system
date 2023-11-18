@extends('layouts.dashboard')

@push('page-styles')
    {{--- ---}}
@endpush

@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg></div>
                            New Quotation
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @include('partials.session')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-xl px-lg-4">
        <div class="row">
            <div class="col">
                <div class="card mb-4">
                    <div class="card-header">
                        Products
                    </div>
                    <div class="card-body">
                        <livewire:search-product/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('quotations.store') }}" method="POST">
                            @csrf
                            <div class="row gx-3 mb-3">
                                <div class="col">
                                    <label class="small mb-1" for="date">
                                        Date
                                        <span class="text-danger">*</span>
                                    </label>

{{--                                    <input class="form-control form-control-solid example-date-input @error('date') is-invalid @enderror"--}}
{{--                                           name="purchase_date" id="date" type="date" value="{{ old('purchase_date') }}"--}}
{{--                                    >--}}
                                    <input class="form-control @error('date') is-invalid @enderror"
                                           name="date" id="date" type="date" value="{{ now()->format('Y-m-d')  }}"
                                    >

                                    @error('date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col">
                                    <label class="small mb-1" for="customer_id">
                                        Customer
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                        <option selected="" disabled="">
                                            Select a customer:
                                        </option>

                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected( old('customer_id') == $customer->id)>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('customer_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col">
                                    <label for="status" class="small mb-1">
                                        Status
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Sent">Sent</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="small mb-1" for="reference">
                                        Reference
                                    </label>

                                    <input type="text" class="form-control"
                                           id="reference" name="reference" value="QT" readonly>

                                    @error('reference')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'quotation'"/>


                            <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    <label for="note">Notes</label>
                                    <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="d-flex flex-wrap">
                                    <button type="submit" class="btn btn-success add-list mx-1">
                                        Create Quotation
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('page-scripts')
{{--    <script src="{{ asset('assets/js/img-preview.js') }}"></script>--}}
@endpush
