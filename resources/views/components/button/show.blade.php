@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-outline-info']) }} route="{{ $route }}">
    <x-icon.eye/>
    {{ $slot }}
</x-button>
