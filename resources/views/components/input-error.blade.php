@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm font-medium text-destructive space-y-1']) }}>
        @foreach (\Illuminate\Support\Arr::flatten((array) $messages) as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
