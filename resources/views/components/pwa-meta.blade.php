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

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then((registration) => {
                    // Check for SW updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        if (newWorker) {
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    window.dispatchEvent(new CustomEvent('pwa-update-available', {
                                        detail: { registration }
                                    }));
                                }
                            });
                        }
                    });
                })
                .catch((error) => {
                    console.warn('[PWA] Service Worker registration failed:', error);
                });
        });
    }
</script>
