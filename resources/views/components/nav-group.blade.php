@props(['active' => false, 'icon' => null, 'label' => ''])

@php
    // Auto-expand if child route is active
    $expanded = $active ? 'true' : 'false';
@endphp

<div x-data="{ expanded: {{ $expanded }} }" class="space-y-1">

    <!-- Group Header Button -->
    <button @click="if(sidebarCollapsed) { toggleSidebar(); expanded = true; } else { expanded = !expanded; }"
            type="button"
            class="group flex w-full items-center gap-x-3 rounded-md px-3 py-2 text-left text-sm font-medium transition-all duration-200"
            :class="(expanded ? 'bg-gray-50 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900') + (sidebarCollapsed ? ' justify-center px-2' : '')">

        <!-- Icon Section -->
        @if ($icon)
            <div class="flex-shrink-0 transition-colors duration-200"
                 :class="expanded ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-500'">
                @if ($icon === 'cube')
                    <x-heroicon-o-cube class="w-5 h-5" />
                @elseif ($icon === 'currency-dollar')
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                @elseif ($icon === 'shopping-cart')
                    <x-heroicon-o-shopping-cart class="w-5 h-5" />
                @elseif ($icon === 'banknotes')
                    <x-heroicon-o-banknotes class="w-5 h-5" />
                @else
                    {{ $icon }}
                @endif
            </div>
        @endif

        <!-- Label (Hidden when Collapsed) -->
        <span class="flex-1 truncate"
              x-show="!sidebarCollapsed"
              style="display: none;">
            {{ $label }}
        </span>

        <!-- Chevron Icon (Hidden when Collapsed) -->
        <div x-show="!sidebarCollapsed" style="display: none;">
            <x-heroicon-o-chevron-right class="h-4 w-4 transform transition-transform duration-200"
                 ::class="expanded ? 'rotate-90 text-gray-500' : 'text-gray-400'" />
        </div>
    </button>

    <!-- Children Links Container -->
    <div x-show="expanded && !sidebarCollapsed"
         x-collapse
         class="space-y-1 px-3"
         style="display: none;">
        {{ $slot }}
    </div>
</div>
