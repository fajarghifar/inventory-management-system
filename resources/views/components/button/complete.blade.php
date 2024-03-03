@props([
    'route'
])
<form action="{{ $route }}" method="POST" class="d-inline-block">
    @csrf
    <x-button type="submit" {{ $attributes->class(['btn btn-outline-success']) }}>
        <x-icon.check/>
        {{ $slot }}
    </x-button>
</form>