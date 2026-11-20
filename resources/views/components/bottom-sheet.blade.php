@props([
    'show' => false,
    'title' => '',
    'onClose' => '',
])

<div
    x-data="{ open: @entangle($show) }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="sheet-title"
    role="dialog"
    aria-modal="true"
    @keydown.escape.window="open = false; {{ $onClose }}"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity"
        @click="open = false; {{ $onClose }}"
    ></div>

    {{-- Sheet Container --}}
    <div class="fixed inset-x-0 bottom-0 z-50 max-h-[90dvh] flex flex-col bg-white rounded-t-3xl shadow-2xl border-t border-gray-100 overflow-hidden"
         style="padding-bottom: env(safe-area-inset-bottom, 0px);"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
    >
        {{-- Grab Handle --}}
        <div class="flex justify-center pt-2.5 pb-1">
            <div class="w-10 h-1 rounded-full bg-gray-300"></div>
        </div>

        {{-- Sheet Header --}}
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 id="sheet-title" class="text-base font-bold text-gray-900 leading-tight">
                {{ $title }}
            </h3>
            <button
                type="button"
                @click="open = false; {{ $onClose }}"
                class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        {{-- Sheet Content --}}
        <div class="p-5 overflow-y-auto touch-scroll flex-1">
            {{ $slot }}
        </div>
    </div>
</div>
