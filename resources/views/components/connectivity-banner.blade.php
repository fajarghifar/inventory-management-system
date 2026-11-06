<div
    x-data="{
        isOnline: navigator.onLine,
        wasOffline: false,
        showRestoredBanner: false,
        init() {
            window.addEventListener('online', () => {
                this.isOnline = true;
                if (this.wasOffline) {
                    this.showRestoredBanner = true;
                    setTimeout(() => {
                        this.showRestoredBanner = false;
                        this.wasOffline = false;
                    }, 4000);
                }
            });
            window.addEventListener('offline', () => {
                this.isOnline = false;
                this.wasOffline = true;
                this.showRestoredBanner = false;
            });
        }
    }"
    class="relative z-50"
>
    <!-- Offline Banner -->
    <div
        x-show="!isOnline"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-full opacity-0"
        class="bg-amber-500 text-slate-950 px-4 py-2 text-sm font-medium shadow-md flex items-center justify-between"
        style="display: none;"
    >
        <div class="flex items-center space-x-2 max-w-7xl mx-auto w-full">
            <svg class="w-5 h-5 flex-shrink-0 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-12.728 0m0 0l2.829-2.829m-2.829 2.829L3 21m5.636-12.728a5 5 0 017.072 0m-7.072 0L3 3m12.728 5.636L21 3M1 1l22 22"></path>
            </svg>
            <span class="flex-1">
                <strong>Offline Mode Active:</strong> You are currently disconnected. The POS register continues to function offline; sales are queued locally in IndexedDB and will sync when reconnected.
            </span>
            <a href="{{ route('sales.create') }}" class="underline text-xs font-semibold uppercase tracking-wider hover:text-white transition-colors ml-2 flex-shrink-0">
                Open POS Register &rarr;
            </a>
        </div>
    </div>

    <!-- Reconnected Banner -->
    <div
        x-show="showRestoredBanner"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-full opacity-0"
        class="bg-emerald-600 text-white px-4 py-2 text-sm font-medium shadow-md flex items-center justify-between"
        style="display: none;"
    >
        <div class="flex items-center space-x-2 max-w-7xl mx-auto w-full">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="flex-1">
                <strong>Internet Restored:</strong> You are back online. Any queued offline transactions are now synchronizing with the server.
            </span>
            <button @click="showRestoredBanner = false" class="text-white hover:text-emerald-100 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
</div>

