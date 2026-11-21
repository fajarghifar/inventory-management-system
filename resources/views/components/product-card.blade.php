@props([
    'product',
    'inCartQty' => 0,
    'wireClickAdd' => 'addToCart',
])

@php
    $isOutOfStock = $product->quantity <= 0;
    $isLowStock = !$isOutOfStock && ($product->quantity <= ($product->min_stock ?? 5));
    $unitLabel = $product->unit?->symbol ?: $product->unit?->name ?: 'pcs';
@endphp

<div
    {{ $attributes->merge(['class' => 'bg-white rounded-2xl border transition-all relative flex flex-col justify-between p-3 shadow-2xs ' . ($inCartQty > 0 ? 'border-brand ring-2 ring-brand/20' : 'border-gray-100 hover:border-gray-200')]) }}
>
    {{-- In-Cart Badge --}}
    @if($inCartQty > 0)
        <div class="absolute top-2.5 right-2.5 z-10 w-6 h-6 rounded-full bg-brand text-white font-mono font-bold text-xs flex items-center justify-center shadow-xs">
            {{ $inCartQty }}
        </div>
    @endif

    <div>
        {{-- Thumbnail --}}
        <div class="w-full h-28 rounded-xl bg-gray-50 border border-gray-100/80 overflow-hidden flex items-center justify-center text-gray-300 relative select-none">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
            @else
                <x-heroicon-o-cube class="w-10 h-10 stroke-1 text-gray-300" />
            @endif

            {{-- Stock Indicator Pill --}}
            <div class="absolute bottom-1.5 left-1.5">
                @if($isOutOfStock)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700">
                        Out of stock
                    </span>
                @elseif($isLowStock)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                        {{ $product->quantity }} left
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-white/90 backdrop-blur-xs text-gray-600 border border-gray-200">
                        {{ $product->quantity }} in stock
                    </span>
                @endif
            </div>
        </div>

        {{-- Name & Unit --}}
        <div class="mt-2.5">
            <h3 class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-2 leading-snug" title="{{ $product->name }}">
                {{ $product->name }}
            </h3>
            <p class="text-[11px] text-gray-400 font-mono mt-0.5">
                {{ $unitLabel }}
            </p>
        </div>
    </div>

    {{-- Price & Add Action --}}
    <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between gap-1.5 select-none">
        <div>
            <span class="text-[10px] text-gray-400 block leading-none">Price</span>
            <span class="font-mono font-black text-xs sm:text-sm text-gray-900 mt-0.5 block">
                ETB {{ number_format((float)$product->selling_price, 2) }}
            </span>
        </div>

        <button
            type="button"
            wire:click="{{ $wireClickAdd }}({{ $product->id }})"
            @if($isOutOfStock) disabled @endif
            class="h-9 px-3 rounded-xl font-bold text-xs inline-flex items-center gap-1 transition-all active:scale-95 shadow-2xs {{ $isOutOfStock ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ($inCartQty > 0 ? 'bg-brand text-white hover:bg-brand-700' : 'bg-brand-50 hover:bg-brand-100 text-brand border border-brand-200/80') }}"
        >
            <x-heroicon-o-plus class="w-3.5 h-3.5 stroke-[2.5]" />
            <span>Add</span>
        </button>
    </div>
</div>
