@props([
    'action',
    'method'

])

<form action="{{ $action }}" method="{{ $method }}">
    @method($method)
    @csrf

    {{ $slot }}
</form>
