@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Purchase Edit') }}
                    </h3>
                </div>

                <div class="card-actions btn-actions">
                    {{--- {{ URL::previous() }} ---}}
                    <a href="{{ route('purchases.index') }}" class="btn-action">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row gx-3 mb-3">
                    <div class="col-md-6">
                        <label class="small mb-1">Name</label>
                        <div class="form-control form-control-solid">{{ $purchase->supplier->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small mb-1">Email</label>
                        <div class="form-control form-control-solid">{{ $purchase->supplier->email }}</div>
                    </div>
                </div>
                <div class="row gx-3 mb-3">
                    <div class="col-md-6">
                        <label class="small mb-1">Phone</label>
                        <div class="form-control form-control-solid">{{ $purchase->supplier->phone }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small mb-1">Order Date</label>
                        <div class="form-control form-control-solid">{{ $purchase->date }}</div>
                    </div>
                </div>
                <div class="row gx-3 mb-3">
                    <div class="col-md-6">
                        <label class="small mb-1">No Purchase</label>
                        <div class="form-control">{{ $purchase->purchase_no }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small mb-1">Total</label>
                        <div class="form-control form-control-solid">{{ $purchase->total_amount }}</div>
                    </div>
                </div>
                <div class="row gx-3 mb-3">
                    <div class="col-md-6">
                        <label class="small mb-1">Created By</label>
                        <div class="form-control form-control-solid">{{ $purchase->createdBy->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small mb-1">Updated By</label>
                        <div class="form-control form-control-solid">{{ $purchase->updatedBy->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label  class="small mb-1">Address</label>
                    <div class="form-control form-control-solid">{{ $purchase->supplier->address }}</div>
                </div>
            </div>

            <div class="card-footer text-end">
                @if ($purchase->status === \App\Enums\PurchaseStatus::PENDING)
                    <form action="{{ route('purchases.update', $purchase->uuid) }}" method="POST">
                        @csrf
                        @method('put')
                        <input type="hidden" name="id" value="{{ $purchase->id }}">

                        <button type="submit"
                                class="btn btn-success"
                                onclick="return confirm('Are you sure you want to approve this purchase?')"
                        >
                            {{ __('Approve Purchase') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
