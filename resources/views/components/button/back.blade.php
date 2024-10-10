@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-danger']) }} route="{{ $route }}">
    <x-icon.arrow/>

    {{ $slot }}
</x-button>
