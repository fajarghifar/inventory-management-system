@props(['status'])

@php
    $color = $status instanceof \App\Enums\PurchaseStatus ? $status->color() : 'text-gray-500 bg-gray-100';
    $label = $status instanceof \App\Enums\PurchaseStatus ? $status->label() : $status;
@endphp

<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $color }}">
    {{ $label }}
</span>
