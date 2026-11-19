@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'group inline-flex h-9 items-center justify-center rounded-xl bg-brand-50 text-brand px-3 py-1.5 text-xs font-bold transition-all border border-brand-200/60 whitespace-nowrap flex-shrink-0'
            : 'group inline-flex h-9 items-center justify-center rounded-xl bg-transparent px-3 py-1.5 text-xs font-semibold transition-colors text-gray-700 hover:bg-gray-100 hover:text-gray-900 border border-transparent whitespace-nowrap flex-shrink-0';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @mouseleave="open = false">
    <button type="button" @click="open = !open" @mouseover="open = true" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $icon }}
        <span>{{ $trigger }}</span>
        <x-heroicon-o-chevron-down class="relative top-[0.5px] ml-1.5 h-3.5 w-3.5 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" ::class="{ 'rotate-180': open }" />
    </button>
    <div x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="absolute left-0 top-full z-50 min-w-[220px] pt-1.5 outline-none"
            style="display: none;">
        <div class="relative rounded-2xl border border-gray-100 bg-white p-1.5 text-gray-900 shadow-xl ring-1 ring-black/5">
            <div class="flex flex-col space-y-0.5">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
