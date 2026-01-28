@props(['label', 'value' => null])

<div class="flex flex-col space-y-1">
    <dt class="text-sm font-medium text-gray-500 flex items-center gap-2">
        {{ $slot }}
        {{ $label }}
    </dt>
    <dd class="text-base font-medium text-gray-900">
        {{ $value ?? '-' }}
    </dd>
</div>
