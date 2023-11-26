@props([
    'route' => null
])

<x-button {{ $attributes->class(['btn btn-primary']) }} route="{{ $route }}">
    <x-icon.floppy-disk/>
    {{ $slot }}
</x-button>
