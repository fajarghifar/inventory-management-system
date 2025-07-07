<div>
    @if (session()->has('message'))
        <div class="alert alert-warning">{{ session('message') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Unit Price</th>
                <th>Stock</th>
                <th>Qty</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cart_items as $item)
                <tr>
                    <td>{{ $item->name }}<br><small>{{ $item->attributes->code }}</small></td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->attributes->stock }} {{ $item->attributes->unit }}</td>
                    <td>
                        <input type="number" wire:change="updateQuantity('{{ $item->id }}', $event.target.value)"
                            value="{{ $item->quantity }}" min="1" max="{{ $item->attributes->stock }}">
                    </td>
                    <td>{{ number_format($item->attributes->product_discount, 2) }}</td>
                    <td>{{ number_format($item->attributes->product_tax, 2) }}</td>
                    <td>{{ number_format($item->attributes->sub_total * $item->quantity, 2) }}</td>
                    <td><button wire:click="removeItem('{{ $item->id }}')" class="btn btn-sm btn-danger">Del</button></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-danger">No products in cart.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="row mt-3">
        <div class="col-md-4">
            <label>Tax (%)</label>
            <input type="number" wire:model.lazy="global_tax" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Discount (%)</label>
            <input type="number" wire:model.lazy="global_discount" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Shipping</label>
            <input type="number" wire:model.lazy="shipping" class="form-control" step="0.01">
        </div>
    </div>

    <div class="mt-4 text-end">
        <h5>
            Grand Total:
            <strong>{{ number_format($cart_total, 2) }}</strong>
        </h5>
    </div>
</div>
