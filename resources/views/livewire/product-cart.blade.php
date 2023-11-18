<div>
    <div>
        @if (session()->has('message'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="alert-body">
                    <span>{{ session('message') }}</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="table-responsive position-relative">
            <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center" style="top:0;right:0;left:0;bottom:0;background-color: rgba(255,255,255,0.5);z-index: 99;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th class="align-middle">Product</th>
                    <th class="align-middle text-center">Net Unit Price</th>
                    <th class="align-middle text-center">Stock</th>
                    <th class="align-middle text-center">Quantity</th>
                    <th class="align-middle text-center">Discount</th>
                    <th class="align-middle text-center">Tax</th>
                    <th class="align-middle text-center">Sub Total</th>
                    <th class="align-middle text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                    @if($cart_items->isNotEmpty())
                        @foreach($cart_items as $cart_item)
                            <tr>
                                <td class="align-middle">
                                    {{ $cart_item->name }} <br>
                                    <span class="badge badge-success">
                                        {{ $cart_item->options->code }}
                                    </span>
                                    @include('livewire.includes.product-cart-modal')
                                </td>

                                <td x-data="{ open{{ $cart_item->id }}: false }" class="align-middle text-center">
                                    <span x-show="!open{{ $cart_item->id }}" @click="open{{ $cart_item->id }} = !open{{ $cart_item->id }}">
                                        {{ format_currency($cart_item->price) }}
                                    </span>

                                    <div x-show="open{{ $cart_item->id }}">
                                        @include('livewire.includes.product-cart-price')
                                    </div>
                                </td>

                                <td class="align-middle text-center text-center">
                                    <span class="badge badge-info">
                                        {{ $cart_item->options->stock . ' ' . $cart_item->options->unit }}
                                    </span>

{{--                                    {{ $cart_item->options->stock . ' ' . $cart_item->options->unit }}--}}
                                </td>

                                <td class="align-middle text-center">
                                    @include('livewire.includes.product-cart-quantity')
                                </td>

                                <td class="align-middle text-center">
                                    {{ format_currency($cart_item->options->product_discount) }}
                                </td>

                                <td class="align-middle text-center">
                                    {{ format_currency($cart_item->options->product_tax) }}
                                </td>

                                <td class="align-middle text-center">
                                    {{ format_currency($cart_item->options->sub_total) }}
                                </td>

                                <td class="align-middle text-center">
{{--                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')">--}}
{{--                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>--}}
{{--                                        Del--}}
{{--                                    </a>--}}

                                    <a href="#" wire:click.prevent="removeItem('{{ $cart_item->rowId }}')" class="btn btn-icon btn-outline-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">
                        <span class="text-danger">
                            Please search & select products!
                        </span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-md-end">
        <div class="col-md-4">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tr>
                        <th>Tax ({{ $global_tax }}%)</th>
                        <td>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</td>
                    </tr>
                    <tr>
                        <th>Discount ({{ $global_discount }}%)</th>
                        <td>(-) {{ format_currency(Cart::instance($cart_instance)->discount()) }}</td>
                    </tr>
                    <tr>
                        <th>Shipping</th>
                        <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                        <td>(+) {{ format_currency($shipping) }}</td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        @php
                            $total_with_shipping = Cart::instance($cart_instance)->total() + (float) $shipping
                        @endphp
                        <th>
                            (=) {{ format_currency($total_with_shipping) }}
                        </th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <input type="hidden" name="total_amount" value="{{ $total_with_shipping }}">

    <div class="row gx-3 mb-3">
        <div class="col">
            <label for="tax_percentage" class="small mb-1">
                Tax (%)
            </label>

            <input type="number"
                   id="tax_percentage"
                   name="tax_percentage"
                   wire:model.blur="global_tax"
                   class="form-control"
                   min="0" max="100" value="{{ $global_tax }}"
                   required
            >
        </div>

        <div class="col">
            <label for="discount_percentage" class="small mb-1">Discount (%)</label>
            <input wire:model.blur="global_discount" type="number" class="form-control" name="discount_percentage" id="discount_percentage" min="0" max="100" value="{{ $global_discount }}" required>
        </div>

        <div class="col">
            <label for="shipping_amount" class="small mb-1">Shipping</label>
            <input wire:model.blur="shipping" type="number" class="form-control" name="shipping_amount" id="shipping_amount" min="0" value="0" required step="0.01">
        </div>
    </div>
</div>
