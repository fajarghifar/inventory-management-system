@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-warning']) }} route="{{ $route }}">
    <x-icon.printer/>

    {{ $slot }}
</x-button>
