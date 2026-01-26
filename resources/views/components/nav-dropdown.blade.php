@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'group inline-flex h-10 w-max items-center justify-center rounded-md bg-muted px-4 py-2 text-sm font-medium transition-colors text-accent-foreground disabled:pointer-events-none disabled:opacity-50'
            : 'group inline-flex h-10 w-max items-center justify-center rounded-md bg-background px-4 py-2 text-sm font-medium transition-colors hover:bg-muted hover:text-accent-foreground disabled:pointer-events-none disabled:opacity-50 data-[active]:bg-muted/50 data-[state=open]:bg-muted/50';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @mouseleave="open = false">
    <button @click="open = !open" @mouseover="open = true" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $icon }}
        {{ $trigger }}
        <x-heroicon-o-chevron-down class="relative top-[1px] ml-1 h-3 w-3 transition duration-200 group-data-[state=open]:rotate-180" />
    </button>
    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute left-0 top-full z-50 w-48 pt-2 outline-none"
            style="display: none;">
        <div class="relative rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md">
            <div class="flex flex-col">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
