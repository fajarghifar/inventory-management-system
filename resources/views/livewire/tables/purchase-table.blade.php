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

    <div class="card-body border-bottom py-3">
        <div class="d-flex">
            <div class="text-secondary">
                Show
                <div class="mx-2 d-inline-block">
                    <select wire:model.live="perPage" class="form-select form-select-sm" aria-label="result per page">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                </div>
                entries
            </div>
            <div class="ms-auto text-secondary">
                Search:
                <div class="ms-2 d-inline-block">
                    <input type="text" wire:model.live="search" class="form-control form-control-sm" aria-label="Search invoice">
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner/>

    <div class="table-responsive">
        <table wire:loading.remove class="table table-bordered card-table table-vcenter text-nowrap datatable">
            <thead class="thead-light">
                <tr>
                    <th class="align-middle text-center w-1">
                        {{ __('No.') }}
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('purchase_no')" href="#" role="button">
                            {{ __('Purchase No.') }}
                            @include('inclues._sort-icon', ['field' => 'purchase_no'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('supplier_id')" href="#" role="button">
                            {{ __('Supplier') }}
                            @include('inclues._sort-icon', ['field' => 'supplier_id'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('date')" href="#" role="button">
                            {{ __('Date') }}
                            @include('inclues._sort-icon', ['field' => 'date'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('total_amount')" href="#" role="button">
                            {{ __('Total') }}
                            @include('inclues._sort-icon', ['field' => 'total_amount'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('status')" href="#" role="button">
                            {{ __('Status') }}
                            @include('inclues._sort-icon', ['field' => 'status'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        {{ __('Action') }}
                    </th>
                </tr>
            </thead>
            <tbody>
            @forelse ($purchases as $purchase)
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
                        {{ $purchase->date->format('d-m-Y') }}
                    </td>
                    <td class="align-middle text-center">
                        {{ Number::currency($purchase->total_amount, 'EUR') }}
                    </td>

                    @if ($purchase->status === \App\Enums\PurchaseStatus::APPROVED)
                        <td class="align-middle text-center">
                            <span class="badge bg-green text-white text-uppercase">
                                {{ __('APPROVED') }}
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            <x-button.show class="btn-icon" route="{{ route('purchases.edit', $purchase->uuid) }}"/>
                        </td>
                    @else
                        <td class="align-middle text-center">
                            <span class="badge bg-orange text-white text-uppercase">
                                {{ __('PENDING') }}
                            </span>
                        </td>
                        <td class="align-middle text-center" style="width: 10%">
                            <x-button.show class="btn-icon" route="{{ route('purchases.edit', $purchase->uuid) }}"/>
                            {{-- <x-button.complete class="btn-icon"  onclick="return confirm('Are you sure to approve purchase no. {{ $purchase->purchase_no }}!') route="{{ route('purchases.update', $purchase->uuid) }}"/> --}}
                            <x-button.complete class="btn-icon" route="{{ route('purchases.update', $purchase->uuid) }}" onclick="return confirm('Are you sure to approve purchase no. {{ $purchase->purchase_no }}?')"/>
                            <x-button.delete class="btn-icon" onclick="return confirm('Are you sure!')" route="{{ route('purchases.delete', $purchase->uuid) }}"/>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td class="align-middle text-center" colspan="7">
                        No results found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing <span>{{ $purchases->firstItem() }}</span>
            to <span>{{ $purchases->lastItem() }}</span> of <span>{{ $purchases->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
        {{ $purchases->links() }}
        </ul>
    </div>
</div>
