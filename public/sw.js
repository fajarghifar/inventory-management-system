/**
 * Service Worker for Inventory & Financial Management System PWA
 * Version: 1.0.0
 */

const CACHE_VERSION = 'inventory-pwa-v1.0.0';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

const PRECACHE_ASSETS = [
    '/offline',
    '/manifest.webmanifest',
    '/manifest.json',
    '/images/logo.svg',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/icon-maskable-512x512.png',
    '/images/icons/apple-touch-icon.png',
    '/images/icons/favicon-32x32.png',
    '/images/icons/favicon-16x16.png',
    '/favicon.ico'
];

// Install Event - Precache static assets & skip waiting
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[SW] Some precache assets could not be loaded:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - Clean up outdated caches & claim clients
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== STATIC_CACHE && cacheName !== RUNTIME_CACHE) {
                        console.log('[SW] Removing old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Message Event - Support immediate skipWaiting updates
self.addEventListener('message', (event) => {
    if (event.data && event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
});

// Fetch Event - Route strategies
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // 1. Only process GET requests. Never intercept POST, PUT, DELETE, etc.
    if (request.method !== 'GET') {
        return;
    }

    // 2. CRITICAL BYPASS: Livewire reactive calls must NEVER be cached or delayed
    if (url.pathname.startsWith('/livewire/')) {
        return;
    }

    // 3. CRITICAL BYPASS: Authentication & sensitive routes must never be cached
    const bypassPaths = [
        '/login',
        '/logout',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/verify-email',
        '/confirm-password'
    ];
    if (bypassPaths.some(path => url.pathname.startsWith(path))) {
        return;
    }

    // 4. Navigation Requests (HTML Pages) -> Network-First with Offline Fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Cache successful navigation responses safely in runtime cache
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Try to match navigation request from cache first
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Fallback to the dedicated offline page
                    const offlineFallback = await caches.match('/offline');
                    if (offlineFallback) {
                        return offlineFallback;
                    }
                    return new Response('You are offline. Please reconnect to continue.', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: { 'Content-Type': 'text/plain' }
                    });
                })
        );
        return;
    }

    // 5. Static Assets (Vite build, images, fonts, styles, scripts) -> Stale-While-Revalidate
    const isStaticAsset = (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/fonts/') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com') ||
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    );

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // 6. Read-only Catalog API endpoint (/ajax/products/catalog) -> Network-First with Cache Fallback
    if (url.pathname === '/ajax/products/catalog') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(RUNTIME_CACHE).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => caches.match(request))
        );
        return;
    }
});

