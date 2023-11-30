@props([

])

<span {{ $attributes->merge(['class'=> 'badge text-white text-uppercase']) }}>
    {{ $slot }}
</span>
