@props([
    'quantity' => 1,
    'min' => 1,
    'max' => null,
    'wireClickDecrement' => 'decrementQty',
    'wireClickIncrement' => 'incrementQty',
    'itemId' => null,
    'size' => 'md',
])

@php
    $btnSize = match($size) {
        'sm' => 'w-8 h-8 text-sm',
        'lg' => 'w-11 h-11 text-base',
        default => 'w-9 h-9 text-base',
    };
    $displaySize = match($size) {
        'sm' => 'w-8 text-sm',
        'lg' => 'w-12 text-base font-black',
        default => 'w-10 text-sm font-bold',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center rounded-xl border border-gray-200 bg-gray-50/80 p-0.5 select-none']) }}>
    <button
        type="button"
        @if($itemId !== null)
            wire:click="{{ $wireClickDecrement }}({{ $itemId }})"
        @else
            wire:click="{{ $wireClickDecrement }}"
        @endif
        @if($quantity <= $min) disabled @endif
        class="{{ $btnSize }} rounded-lg bg-white shadow-2xs hover:bg-gray-100 disabled:opacity-40 disabled:hover:bg-white text-gray-700 font-bold flex items-center justify-center transition-all active:scale-90"
        aria-label="Decrease quantity"
    >
        &minus;
    </button>

    <span class="{{ $displaySize }} text-center font-mono text-gray-900 select-none">
        {{ $quantity }}
    </span>

    <button
        type="button"
        @if($itemId !== null)
            wire:click="{{ $wireClickIncrement }}({{ $itemId }})"
        @else
            wire:click="{{ $wireClickIncrement }}"
        @endif
        @if($max !== null && $quantity >= $max) disabled @endif
        class="{{ $btnSize }} rounded-lg bg-white shadow-2xs hover:bg-gray-100 disabled:opacity-40 disabled:hover:bg-white text-gray-700 font-bold flex items-center justify-center transition-all active:scale-90"
        aria-label="Increase quantity"
    >
        &plus;
    </button>
</div>
