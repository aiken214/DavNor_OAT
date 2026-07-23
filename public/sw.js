var CACHE_NAME = 'oat-v3';

var OFFLINE_PAGE = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>OAT - Offline</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:-apple-system,sans-serif;background:linear-gradient(135deg,#f8fafc,#eff6ff,#eef2ff);min-height:100vh;display:flex;align-items:center;justify-content:center}.card{background:#fff;border-radius:1rem;padding:2.5rem;max-width:400px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.08);margin:1rem}.icon{width:64px;height:64px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:28px}h1{font-size:1.25rem;font-weight:700;color:#1e293b;margin-bottom:.5rem}p{color:#64748b;font-size:.875rem;line-height:1.5;margin-bottom:1.5rem}button{padding:.75rem 1.5rem;border-radius:.75rem;border:none;background:#2563eb;color:#fff;font-weight:600;cursor:pointer;font-size:.875rem}button:hover{background:#1d4ed8}</style></head><body><div class="card"><div class="icon">📡</div><h1>You\'re Offline</h1><p>Please check your internet connection and try again. Your pending accomplishments are safely saved and will sync when you\'re back online.</p><button onclick="location.reload()">Try Again</button></div></body></html>';

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.put(new Request('/_offline'), new Response(OFFLINE_PAGE, {
                headers: { 'Content-Type': 'text/html' }
            }));
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(
                keys.filter(function(key) { return key !== CACHE_NAME; })
                    .map(function(key) { return caches.delete(key); })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', function(event) {
    if (event.request.method !== 'GET') return;

    var url = new URL(event.request.url);

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).then(function(response) {
                if (response.ok) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) { cache.put(event.request, clone); });
                }
                return response;
            }).catch(function() {
                return caches.match(event.request).then(function(cached) {
                    return cached || caches.match('/_offline');
                });
            })
        );
        return;
    }

    if (url.origin === self.location.origin) {
        event.respondWith(
            fetch(event.request).then(function(response) {
                if (response.ok) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) { cache.put(event.request, clone); });
                }
                return response;
            }).catch(function() {
                return caches.match(event.request);
            })
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then(function(cached) {
            if (cached) return cached;
            return fetch(event.request).then(function(response) {
                if (response.ok || response.type === 'opaque') {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) { cache.put(event.request, clone); });
                }
                return response;
            });
        })
    );
});
