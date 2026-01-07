@props(['active' => false, 'icon' => null])

@php
    // Determine styles based on active state
    $classes = ($active ?? false)
                ? 'group flex items-center gap-x-3 px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-900 transition-all duration-200'
                : 'group flex items-center gap-x-3 px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200';

    $iconClasses = ($active ?? false)
                ? 'text-gray-900'
                : 'text-gray-400 group-hover:text-gray-500 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} :class="sidebarCollapsed ? 'justify-center px-2' : ''">
    <!-- Icon Section -->
    @if ($icon)
        <div class="flex-shrink-0 {{ $iconClasses }}">
            @if ($icon === 'home')
                <x-heroicon-o-home class="w-5 h-5" />
            @elseif ($icon === 'users')
                <x-heroicon-o-users class="w-5 h-5" />
            @elseif ($icon === 'cube')
                <x-heroicon-o-cube class="w-5 h-5" />
            @elseif ($icon === 'cog')
                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
            @elseif ($icon === 'truck')
                <x-heroicon-o-truck class="w-5 h-5" />
            @else
                {{ $icon }}
            @endif
        </div>
    @endif

    <!-- Label Section (Hidden when Sidebar Collapsed) -->
    <span class="truncate"
          x-show="!sidebarCollapsed"
          x-transition:enter="transition ease-out duration-100"
          x-transition:enter-start="opacity-0 scale-90"
          x-transition:enter-end="opacity-100 scale-100"
          style="display: none;">
        {{ $slot }}
    </span>
</a>
