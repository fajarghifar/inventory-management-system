@props([
    'route'
])

<form action="{{ $route }}" method="POST" class="d-inline-block">
    @csrf
    @method('delete')
    <x-button type="submit" {{ $attributes->class(['btn btn-outline-danger']) }}>
        <x-icon.trash/>
        {{ $slot }}
    </x-button>
</form>
