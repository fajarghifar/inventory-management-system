@props([
    'title' => '',
    'subtitle' => null,
    'backUrl' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => null,
])

<div {{ $attributes->merge(['class' => 'sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-gray-200/80 shadow-2xs select-none']) }} style="padding-top: env(safe-area-inset-top, 0px);">
    <div class="px-4 py-3 flex items-center justify-between gap-3 max-w-7xl mx-auto">
        <div class="flex items-center gap-2.5 min-w-0">
            @if($backUrl)
                <a
                    href="{{ $backUrl }}"
                    class="w-9 h-9 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 flex items-center justify-center transition-colors shrink-0 active:scale-90"
                    title="Back"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
                </a>
            @endif

            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold tracking-tight text-gray-900 truncate leading-snug">
                    {{ $title }}
                </h1>
                @if($subtitle)
                    <p class="text-xs text-gray-500 truncate leading-tight">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if(isset($actions))
                {{ $actions }}
            @elseif($actionUrl && $actionLabel)
                <a
                    href="{{ $actionUrl }}"
                    class="h-9 px-3.5 rounded-xl bg-brand text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-xs hover:bg-brand-700 transition-all active:scale-95"
                >
                    @if($actionIcon)
                        {!! $actionIcon !!}
                    @endif
                    <span>{{ $actionLabel }}</span>
                </a>
            @endif
        </div>
    </div>
</div>
