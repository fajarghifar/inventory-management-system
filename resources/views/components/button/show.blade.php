@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-info']) }} route="{{ $route }}">
    <x-icon.eye/>

    {{ $slot }}
</x-button>
