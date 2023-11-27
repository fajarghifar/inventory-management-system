@props([

])

<x-button type="submit" {{ $attributes->class(['btn btn-primary']) }}>
    <x-icon.floppy-disk/>
    {{ $slot }}
</x-button>
