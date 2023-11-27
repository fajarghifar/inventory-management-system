@props([
    'route' => null,
    'type'
])

<x-button type="{{ $type }}" {{ $attributes->class(['btn btn-primary']) }} route="{{ $route }}">
    <x-icon.floppy-disk/>
    {{ $slot }}
</x-button>
