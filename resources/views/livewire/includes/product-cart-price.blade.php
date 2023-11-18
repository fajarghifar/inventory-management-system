<div class="input-group d-flex justify-content-center shadow-none">
    <input wire:model="unit_price.{{ $cart_item->id }}" style="min-width: 40px;max-width: 90px;" type="text" class="form-control" min="0">

    <div class="input-group-append">
        <button @click="open{{ $cart_item->id }} = !open{{ $cart_item->id }}" type="button" wire:click="updatePrice('{{ $cart_item->rowId }}', {{ $cart_item->id }})" class="btn btn-icon btn-info">
{{--            <i class="bi bi-check"></i>--}}
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
        </button>
    </div>
</div>
