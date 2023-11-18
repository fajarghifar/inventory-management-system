<div>
    <table class="table table-bordered" id="products_table">
        <thead class="thead-dark">
            <tr>
                <th class="align-middle">Product</th>
                <th class="align-middle text-center">Quantity</th>
                <th class="align-middle text-center">Price</th>
                <th class="align-middle text-center">Total</th>
                <th class="align-middle text-center">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($invoiceProducts as $index => $invoiceProduct)
            <tr>
                <td class="align-middle">
                    @if($invoiceProduct['is_saved'])
                        <input type="hidden"
                               name="invoiceProducts[{{$index}}][product_id]"
                               wire:model.live="invoiceProducts.{{$index}}.product_id"
                        >

                        {{---
                        @if($invoiceProduct['product_name'] && $invoiceProduct['product_price'])
                            {{ $invoiceProduct['product_name'] }}
                            (${{ number_format($invoiceProduct['product_price'], 2) }})
                        @endif
                        ---}}

                        {{ $invoiceProduct['product_name'] }}
                    @else
                        <select wire:model.live="invoiceProducts.{{$index}}.product_id" id="invoiceProducts[{{$index}}][product_id]"
                                class="form-control @error('invoiceProducts.' . $index . '.product_id') is-invalid @enderror">

                            <option value="">-- choose product --</option>

                            @foreach ($allProducts as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} (${{ number_format($product->buying_price, 2) }})
                                </option>
                            @endforeach
                        </select>

                        @error('invoiceProducts.' . $index)
                        <em class="text-danger">
                            {{ $message }}
                        </em>
                        @enderror
                    @endif
                </td>
                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        {{ $invoiceProduct['quantity'] }}
                    @else
                        <input wire:model="invoiceProducts.{{$index}}.quantity" type="number" id="invoiceProducts[{{$index}}][quantity]" class="form-control" />
                    @endif
                </td>
                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        {{ number_format($invoiceProduct['product_price'], 2) }}
                    @endif
                </td>
                <td class="align-middle text-center">
                    {{ $invoiceProduct['product_price'] * $invoiceProduct['quantity'] }}
                </td>

                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        <button type="button" wire:click="editProduct({{$index}})" class="btn btn-icon btn-outline-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                        </button>

                    @elseif($invoiceProduct['product_id'])

                        <button type="button" wire:click="saveProduct({{$index}})" class="btn btn-icon btn-outline-success mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                        </button>
                    @endif

                    <button type="button" wire:click="removeProduct({{$index}})" class="btn btn-icon btn-outline-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td class="text-center">
                    <button type="button" wire:click="addProduct" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        {{ __('Add') }}
                    </button>
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Subtotal
                </th>
                <td class="text-center">
                    ${{ number_format($subtotal, 2) }}
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Taxes
                </th>
                <td width="150" class="align-middle text-center">
                    <input wire:model.live="taxes" type="number" id="taxes" class="form-control w-75 d-inline" min="0" max="100">
                    %

                    @error('taxes')
                    <em class="invalid-feedback">
                        {{ $message }}
                    </em>
                    @enderror
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Total
                </th>
                <td class="text-center">
                    ${{ number_format($total, 2) }}
                </td>
            </tr>

        </tbody>
    </table>

    {{---
    <div class="row">
        <div class="col-md-12">
            <button type="button" wire:click="addProduct" class="btn btn-outline-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                {{ __('Add') }}
            </button>
        </div>
    </div>
    ---}}

    {{---
    <div class="col-lg-5 ml-auto text-right mt-4">
        <table class="table pull-right">
            <tr>
                <th>Subtotal</th>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <th>Taxes</th>
                <td width="125">
                    <input wire:model.live="taxes" type="number" id="taxes" class="form-control w-75 d-inline" min="0" max="100">
                    %
                    @error('taxes')
                    <em class="invalid-feedback">
                        {{ $message }}
                    </em>
                    @enderror
                </td>
            </tr>
            <tr>
                <th>Total</th>
                <td>${{ number_format($total, 2) }}</td>
                <td>
                    <input type="text" wire:model="{{ $total }}" id="total" name="total">
                </td>
            </tr>
        </table>
    </div>
    ---}}
</div>
