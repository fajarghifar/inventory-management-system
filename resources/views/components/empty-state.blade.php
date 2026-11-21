@props([
    'title' => 'No items found',
    'description' => '',
    'actionLabel' => null,
    'actionUrl' => null,
    'actionWireClick' => null,
])

<div {{ $attributes->merge(['class' => 'col-span-full py-12 px-6 text-center bg-white rounded-2xl border border-gray-100 shadow-2xs space-y-3 select-none']) }}>
    <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 text-gray-300 flex items-center justify-center mx-auto">
        {{ $slot->isEmpty() ? '<x-heroicon-o-cube class="w-7 h-7 stroke-1 text-gray-300" />' : $slot }}
    </div>

    <div>
        <h3 class="text-sm font-bold text-gray-800">{{ $title }}</h3>
        @if($description)
            <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">{{ $description }}</p>
        @endif
    </div>

    @if($actionLabel)
        <div class="pt-1">
            @if($actionUrl)
                <a
                    href="{{ $actionUrl }}"
                    class="h-9 px-4 rounded-xl bg-brand text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-xs hover:bg-brand-700 transition-all active:scale-95"
                >
                    <span>{{ $actionLabel }}</span>
                </a>
            @elseif($actionWireClick)
                <button
                    type="button"
                    wire:click="{{ $actionWireClick }}"
                    class="h-9 px-4 rounded-xl bg-brand text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-xs hover:bg-brand-700 transition-all active:scale-95"
                >
                    <span>{{ $actionLabel }}</span>
                </button>
            @endif
        </div>
    @endif
</div>
