@props([
    'type' => null ?? 'button',

    'route'
])


@isset($route)

    <a href="{{ $route }}" {{ $attributes->class(['btn btn-primary w-100']) }}>
        {{ $slot }}
    </a>

@else
    <button type="{{ $type }}" {{ $attributes->class(['btn btn-primary w-100']) }}>
        {{ $slot }}
    </button>
@endisset
