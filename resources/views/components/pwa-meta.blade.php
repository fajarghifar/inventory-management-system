<!-- PWA Web App Manifest -->
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#0f172a">

<!-- Android & Chrome -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="Inventory PWA">

<!-- iOS Safari Support -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Inventory PWA">
<link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-touch-icon.png">

<!-- Windows Tile & Icons -->
<meta name="msapplication-TileColor" content="#0f172a">
<meta name="msapplication-TileImage" content="/images/icons/icon-192x192.png">

<!-- Favicons -->
<link rel="icon" type="image/png" sizes="32x32" href="/images/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/icons/favicon-16x16.png">
<link rel="shortcut icon" href="/favicon.ico">

<!-- IndexedDB Storage Engine for Offline Capabilities -->
<script src="/js/pwa-db.js"></script>

<!-- Mobile Viewport & Zoom Lock Engine (Prevents iOS/Android pinch, double-tap & trackpad zooming) -->
<script>
    (function() {
        // 1. Prevent iOS Safari gesture events (pinch-to-zoom)
        document.addEventListener('gesturestart', function(e) {
            e.preventDefault();
        }, { passive: false });
        document.addEventListener('gesturechange', function(e) {
            e.preventDefault();
        }, { passive: false });
        document.addEventListener('gestureend', function(e) {
            e.preventDefault();
        }, { passive: false });

        // 2. Prevent multi-finger pinch touch zoom on Android and iOS
        document.addEventListener('touchstart', function(e) {
            if (e.touches && e.touches.length > 1) {
                e.preventDefault();
            }
        }, { passive: false });

        // 3. Prevent double-tap to zoom on interactive screens
        var lastTouchEnd = 0;
        document.addEventListener('touchend', function(e) {
            var now = Date.now();
            if (now - lastTouchEnd <= 300) {
                var target = e.target;
                var tag = (target && target.tagName) ? target.tagName.toLowerCase() : '';
                if (tag !== 'input' && tag !== 'textarea' && tag !== 'select') {
                    e.preventDefault();
                }
            }
            lastTouchEnd = now;
        }, { passive: false });

        // 4. Prevent Ctrl + wheel/trackpad zoom
        window.addEventListener('wheel', function(e) {
            if (e.ctrlKey) {
                e.preventDefault();
            }
        }, { passive: false });

        // 5. Prevent Ctrl + / Ctrl - keyboard zooming
        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === '+' || e.key === '-' || e.key === '=' || e.key === '0')) {
                e.preventDefault();
            }
        });
    })();
</script>

<!-- Service Worker Registration & Seamless PWA Auto-Update Engine -->
<script>
    if ('serviceWorker' in navigator) {
        let isRefreshing = false;

        // 1. Immediately listen for controller change to reload into new version
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (isRefreshing) return;
            isRefreshing = true;
            console.log('[PWA] New version activated and took control. Refreshing page for latest updates...');
            window.location.reload();
        });

        window.addEventListener('load', () => {
            // Register with updateViaCache: 'none' to always fetch fresh sw.js from network
            navigator.serviceWorker.register('/sw.js', { scope: '/', updateViaCache: 'none' })
                .then((registration) => {
                    window.pwaRegistration = registration;
                    console.log('[PWA] Service Worker registered successfully (scope: /)');

                    // Helper function to notify UI of pending update
                    const notifyUpdate = (worker) => {
                        window.dispatchEvent(new CustomEvent('pwa-update-available', {
                            detail: { registration, worker }
                        }));
                    };

                    // Case A: A service worker was ALREADY waiting before page opened
                    if (registration.waiting && navigator.serviceWorker.controller) {
                        console.log('[PWA] Pending update already waiting in background.');
                        notifyUpdate(registration.waiting);
                    }

                    // Case B: A new service worker is currently installing
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        if (newWorker) {
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    console.log('[PWA] New update installed and ready.');
                                    notifyUpdate(newWorker);
                                }
                            });
                        }
                    });

                    // 2. Proactively check for updates immediately on load
                    registration.update().catch(() => {});

                    // 3. Proactively check for updates whenever user returns to the app / unlocks device
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') {
                            registration.update().catch(() => {});
                        }
                    });

                    window.addEventListener('focus', () => {
                        registration.update().catch(() => {});
                    });

                    // 4. Periodic background update check every 10 minutes while app is in use
                    setInterval(() => {
                        registration.update().catch(() => {});
                    }, 10 * 60 * 1000);
                })
                .catch((error) => {
                    console.warn('[PWA] Service Worker registration failed:', error);
                });
        });

        // Global helper allowing any component or button to trigger an update
        window.pwaApplyUpdate = function() {
            if (window.pwaRegistration && window.pwaRegistration.waiting) {
                window.pwaRegistration.waiting.postMessage({ action: 'skipWaiting' });
            } else {
                window.location.reload();
            }
        };
    }
</script>
