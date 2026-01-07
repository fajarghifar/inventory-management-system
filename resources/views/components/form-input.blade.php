@props([
    'name' => null,
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
])

<div class="space-y-2">
    @if($label || $slot->isNotEmpty())
        <div class="flex justify-between items-center">
            @if($label)
                <label for="{{ $name }}" class="text-sm font-medium leading-none text-gray-700">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif
            @if(isset($cornerHint))
                {{ $cornerHint }}
            @endif
        </div>
    @endif
    <input
        id="{{ $name ?? $attributes->get('id') }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $name ? old($name, $value) : $value }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'flex w-full h-10 px-3 py-2 text-sm bg-white border border-gray-300 rounded-md ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors']) }}
    >
    @error($name)
        <p class="text-sm font-medium text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>
