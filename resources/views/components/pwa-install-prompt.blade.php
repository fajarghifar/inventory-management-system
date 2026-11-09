<div
    x-data="{
        deferredPrompt: null,
        showBanner: false,
        showIosModal: false,
        showHelpModal: false,
        showUpdateBanner: false,
        updateRegistration: null,
        isIos: false,
        isStandalone: false,

        init() {
            // Check standalone mode
            this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

            // Check iOS
            const ua = window.navigator.userAgent.toLowerCase();
            this.isIos = /iphone|ipad|ipod/.test(ua) && !window.MSStream;

            // Capture beforeinstallprompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                window.pwaDeferredPrompt = e;

                // Check dismissal timer (7 days)
                const dismissedUntil = localStorage.getItem('pwa_install_dismissed_until');
                const now = new Date().getTime();
                if (!dismissedUntil || now > parseInt(dismissedUntil)) {
                    if (!this.isStandalone) {
                        this.showBanner = true;
                    }
                }
            });

            // App installed event
            window.addEventListener('appinstalled', () => {
                this.showBanner = false;
                this.deferredPrompt = null;
                window.pwaDeferredPrompt = null;
                console.log('[PWA] Application was successfully installed.');
            });

            // Service worker update event
            window.addEventListener('pwa-update-available', (e) => {
                this.updateRegistration = e.detail?.registration;
                this.showUpdateBanner = true;
            });

            // Global trigger listener
            window.addEventListener('open-pwa-install', () => {
                this.triggerInstall();
            });
        },

        triggerInstall() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                this.deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('[PWA] User accepted installation prompt.');
                    }
                    this.deferredPrompt = null;
                    this.showBanner = false;
                });
            } else if (this.isIos && !this.isStandalone) {
                this.showIosModal = true;
            } else {
                this.showHelpModal = true;
            }
        },

        dismissBanner() {
            this.showBanner = false;
            // Dismiss for 7 days
            const sevenDays = 7 * 24 * 60 * 60 * 1000;
            localStorage.setItem('pwa_install_dismissed_until', (new Date().getTime() + sevenDays).toString());
        },

        applyUpdate() {
            if (this.updateRegistration && this.updateRegistration.waiting) {
                this.updateRegistration.waiting.postMessage({ action: 'skipWaiting' });
            }
            window.location.reload();
        }
    }"
    class="relative z-50"
>
    <!-- Floating Installation Banner (Desktop & Mobile) -->
    <div
        x-show="showBanner"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-8 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-8 opacity-0"
        class="fixed bottom-4 right-4 max-w-sm w-full bg-slate-900 border border-slate-700 rounded-xl shadow-2xl p-4 text-white z-50"
        style="display: none;"
    >
        <div class="flex items-start space-x-3">
            <img src="/images/icons/icon-192x192.png" alt="App Icon" class="w-12 h-12 rounded-lg flex-shrink-0 bg-slate-950 p-1 border border-slate-800">
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-white">Install Inventory App</h4>
                <p class="text-xs text-slate-300 mt-1">Install on your device for standalone window, offline sales register, and faster access.</p>
                <div class="mt-3 flex items-center space-x-2">
                    <button
                        @click="triggerInstall()"
                        type="button"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-semibold shadow-sm transition-colors"
                    >
                        Install Now
                    </button>
                    <button
                        @click="dismissBanner()"
                        type="button"
                        class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md text-xs font-medium transition-colors"
                    >
                        Not Now
                    </button>
                </div>
            </div>
            <button @click="dismissBanner()" class="text-slate-400 hover:text-white p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Update Available Notification Banner -->
    <div
        x-show="showUpdateBanner"
        x-transition
        class="fixed bottom-4 left-4 max-w-sm w-full bg-indigo-900 border border-indigo-700 rounded-xl shadow-2xl p-4 text-white z-50"
        style="display: none;"
    >
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-800 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <div>
                    <h5 class="text-xs font-semibold text-white">App Update Ready</h5>
                    <p class="text-[11px] text-indigo-200">A new version is cached and ready.</p>
                </div>
            </div>
            <button
                @click="applyUpdate()"
                class="px-2.5 py-1 bg-white text-indigo-900 rounded text-xs font-bold hover:bg-indigo-50 transition-colors"
            >
                Reload
            </button>
        </div>
    </div>

    <!-- iOS Installation Guide Modal -->
    <div
        x-show="showIosModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800" @click.outside="showIosModal = false">
            <div class="text-center">
                <img src="/images/icons/icon-192x192.png" alt="App Icon" class="w-16 h-16 mx-auto rounded-2xl shadow-md mb-3">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Install on iPhone / iPad</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Follow these 3 quick steps to install this app to your Home Screen:</p>
            </div>

            <div class="mt-6 space-y-4 text-xs text-slate-700 dark:text-slate-300">
                <div class="flex items-start space-x-3 bg-slate-50 dark:bg-slate-800 p-3 rounded-lg">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-xs">1</span>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">Tap the Share button</p>
                        <p class="text-slate-500 dark:text-slate-400 mt-0.5">At the bottom of Safari, tap the <svg class="inline w-4 h-4 mx-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg> share icon.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 bg-slate-50 dark:bg-slate-800 p-3 rounded-lg">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-xs">2</span>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">Select "Add to Home Screen"</p>
                        <p class="text-slate-500 dark:text-slate-400 mt-0.5">Scroll down the share sheet and tap <strong>Add to Home Screen</strong>.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 bg-slate-50 dark:bg-slate-800 p-3 rounded-lg">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center flex-shrink-0 text-xs">3</span>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">Tap "Add"</p>
                        <p class="text-slate-500 dark:text-slate-400 mt-0.5">Tap <strong>Add</strong> in the top-right corner to finish.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button
                    @click="showIosModal = false"
                    type="button"
                    class="w-full py-2.5 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-colors"
                >
                    Got It
                </button>
            </div>
        </div>
    </div>

    <!-- Desktop Help Modal -->
    <div
        x-show="showHelpModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800" @click.outside="showHelpModal = false">
            <div class="text-center">
                <img src="/images/icons/icon-192x192.png" alt="App Icon" class="w-14 h-14 mx-auto rounded-xl shadow-md mb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Install Inventory App</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                    To install this application on your computer or browser:
                </p>
                <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg text-xs text-left mt-3 text-slate-700 dark:text-slate-300">
                    <p>• <strong>Chrome / Edge:</strong> Click the install icon (🖥️ or ➕) on the right side of the address bar.</p>
                    <p class="mt-2">• Or open browser menu <strong>(⋮) &rarr; Save and share &rarr; Install app</strong>.</p>
                </div>
                <button
                    @click="showHelpModal = false"
                    class="mt-5 w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

