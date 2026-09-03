// CartFlow Service Worker for Android PWA & Offline Readiness
const CACHE_NAME = 'cartflow-cache-v1';
const STATIC_ASSETS = [
    '/favicon.ico',
    '/favicon.svg',
    '/apple-touch-icon.png',
    '/icon-192.png',
    '/icon-512.png',
    '/manifest.json'
];

// Install: pre-cache core icons and manifest
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate: cleanup older caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch: network-first for live POS data, fallback to cache for static assets
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Never cache Livewire update requests or dynamic API endpoints
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/livewire') || url.pathname.includes('/api/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                // Cache successful responses for static assets
                if (
                    networkResponse.status === 200 &&
                    (url.pathname.endsWith('.png') ||
                     url.pathname.endsWith('.svg') ||
                     url.pathname.endsWith('.ico') ||
                     url.pathname.endsWith('.css') ||
                     url.pathname.endsWith('.js'))
                ) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});
