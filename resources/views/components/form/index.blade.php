@props([
    'action',
    'method'
])

<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)

    {{ $slot }}
</form>
