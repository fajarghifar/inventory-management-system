@props(['status'])

@php
    $color = method_exists($status, 'color') ? $status->color() : 'bg-gray-100 text-gray-800 border-gray-200';
    $label = method_exists($status, 'label') ? $status->label() : $status;
@endphp

<span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
    {{ $label }}
</span>
