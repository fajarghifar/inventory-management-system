{{-- Native-Style Clean Mobile Bottom Navigation Bar (Visible only on mobile/tablet screens < lg) --}}
<nav
    x-data="{
        openMenu() {
            window.dispatchEvent(new CustomEvent('toggle-mobile-menu'));
        }
    }"
    class="block lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-xl border-t border-gray-200/80 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]"
    style="padding-bottom: env(safe-area-inset-bottom, 0px);"
    aria-label="Mobile Bottom Navigation"
>
    <div class="grid grid-cols-5 h-16 max-w-lg mx-auto px-1 items-center">

        {{-- 1. Home / Dashboard --}}
        @php $isHome = request()->routeIs('dashboard'); @endphp
        <a
            href="{{ route('dashboard') }}"
            class="flex flex-col items-center justify-center py-1 transition-all group {{ $isHome ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
        >
            <div class="relative flex items-center justify-center w-7 h-7">
                <svg class="w-5 h-5 transition-transform group-active:scale-90" fill="{{ $isHome ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isHome ? '0' : '1.9' }}"
                        fill-rule="evenodd" clip-rule="evenodd"
                        d="{{ $isHome
                            ? 'M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69zM12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z'
                            : 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
                        }}"
                    />
                </svg>
            </div>
            <span class="text-[10px] tracking-tight leading-none mt-0.5">Home</span>
        </a>

        {{-- 2. Products --}}
        @php $isProducts = request()->routeIs(['products.*', 'categories.*', 'units.*', 'low-stock.*', 'stock.*']); @endphp
        <a
            href="{{ route('products.index') }}"
            class="flex flex-col items-center justify-center py-1 transition-all group {{ $isProducts ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
        >
            <div class="relative flex items-center justify-center w-7 h-7">
                <svg class="w-5 h-5 transition-transform group-active:scale-90" fill="{{ $isProducts ? 'currentColor' : 'none' }}" stroke="{{ $isProducts ? 'none' : 'currentColor' }}" viewBox="0 0 24 24">
                    @if($isProducts)
                        <path d="M12.378 1.602a.75.75 0 00-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03zM21.75 7.93l-9 5.25v9l8.628-5.032a.75.75 0 00.372-.648V7.93zM11.25 22.18v-9l-9-5.25v8.57a.75.75 0 00.372.648l8.628 5.033z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    @endif
                </svg>
            </div>
            <span class="text-[10px] tracking-tight leading-none mt-0.5">Products</span>
        </a>

        {{-- 3. POS Register (Highlighted Primary Action, Cleanly Centered in Bar) --}}
        @php $isPOS = request()->routeIs('sales.create'); @endphp
        <a
            href="{{ route('sales.create') }}"
            class="flex flex-col items-center justify-center py-1 group focus:outline-none"
            title="Open POS Register"
        >
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 group-active:scale-95 shadow-sm
                {{ $isPOS
                    ? 'bg-indigo-600 text-white shadow-indigo-600/30'
                    : 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100 group-hover:scale-105'
                }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                    />
                </svg>
            </div>
            <span class="text-[10px] font-bold leading-none mt-1 {{ $isPOS ? 'text-indigo-600' : 'text-indigo-600/90' }}">POS</span>
        </a>

        {{-- 4. Sales History --}}
        @php $isSales = request()->routeIs(['sales.index', 'sales.show']); @endphp
        <a
            href="{{ route('sales.index') }}"
            class="flex flex-col items-center justify-center py-1 transition-all group {{ $isSales ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}"
        >
            <div class="relative flex items-center justify-center w-7 h-7">
                <svg class="w-5 h-5 transition-transform group-active:scale-90" fill="{{ $isSales ? 'currentColor' : 'none' }}" stroke="{{ $isSales ? 'none' : 'currentColor' }}" viewBox="0 0 24 24">
                    @if($isSales)
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zM9.75 17.25a.75.75 0 00-1.5 0V21a.75.75 0 001.5 0v-3.75zm2.25-3a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-6a.75.75 0 01.75-.75zm3.75-1.5a.75.75 0 00-1.5 0V21a.75.75 0 001.5 0V12.75z"/>
                        <path d="M14.25 5.25a5.23 5.23 0 00-1.279-3.434 9.768 9.768 0 016.963 6.963A5.23 5.23 0 0016.5 7.5h-1.875a.375.375 0 01-.375-.375V5.25z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    @endif
                </svg>
            </div>
            <span class="text-[10px] tracking-tight leading-none mt-0.5">Sales</span>
        </a>

        {{-- 5. More / Menu --}}
        <button
            type="button"
            @click="openMenu()"
            class="flex flex-col items-center justify-center py-1 text-gray-500 hover:text-gray-900 transition-all group focus:outline-none"
        >
            <div class="relative flex items-center justify-center w-7 h-7">
                <svg class="w-5 h-5 transition-transform group-active:scale-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </div>
            <span class="text-[10px] font-medium tracking-tight leading-none mt-0.5">More</span>
        </button>

    </div>
</nav>
