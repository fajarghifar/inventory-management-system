@props([
    'label',
    'value',
])

<div class="space-y-1">
    <label class="text-sm font-medium leading-none text-gray-500">
        {{ $label }}
    </label>
    <div class="flex items-center gap-2">
        @if($slot->isNotEmpty())
            <div class="text-gray-400">
                {{ $slot }}
            </div>
        @endif
        <p class="text-sm text-slate-900 leading-relaxed">{{ $value }}</p>
    </div>
</div>
