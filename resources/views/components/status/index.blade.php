@props([
    'color',
    'dot',
    'animated',
])

<span {{ $attributes->class(['status status-'.$color]) }}>

    @isset($dot)
    <span class="status-dot @isset($animated) status-dot-animated @endisset"></span>
    @endisset

    {{ $slot }}
</span>
