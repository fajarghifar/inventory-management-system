@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-outline-warning']) }} route="{{ $route }}">
    <x-icon.printer/>

    {{ $slot }}
</x-button>
