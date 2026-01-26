@props(['disabled' => false, 'name', 'label' => null, 'required' => false, 'messages' => []])

<div class="space-y-2">
    @if($label)
        <x-input-label :for="$name" :value="$label" :required="$required" />
    @endif

    <x-text-input
        :id="$name"
        :name="$name"
        {{ $attributes->merge(['class' => 'block w-full']) }}
        :disabled="$disabled"
        :required="$required"
    />

    <x-input-error :messages="$messages" class="mt-2" />
</div>
