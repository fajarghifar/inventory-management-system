@props(['name', 'class' => ''])

@php
    $initials = collect(explode(' ', $name))
        ->map(function ($segment) {
            return strtoupper(substr($segment, 0, 1));
        })
        ->take(2)
        ->join('');
@endphp

<div {{ $attributes->merge(['class' => 'relative flex shrink-0 overflow-hidden rounded-full h-9 w-9 items-center justify-center bg-muted ' . $class]) }}>
    <span class="flex h-full w-full items-center justify-center rounded-full bg-muted font-medium text-muted-foreground">
        {{ $initials }}
    </span>
</div>
