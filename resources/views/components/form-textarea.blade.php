@props([
    'name' => null,
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'rows' => 3,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="text-sm font-medium leading-none text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <textarea
        id="{{ $name ?? $attributes->get('id') }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'flex w-full min-h-[80px] px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors']) }}
    >{{ $name ? old($name, $value) : $value }}</textarea>
    @error($name)
        <p class="text-sm font-medium text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
