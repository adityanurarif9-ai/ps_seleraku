const CACHE = 'ps-seleraku-static-v1';
const OFFLINE = '/offline.html';
self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE).then(cache => cache.addAll([OFFLINE, '/icons/app-192.png', '/icons/app-512.png'])).then(() => self.skipWaiting()));
});
self.addEventListener('activate', event => {
    event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key.startsWith('ps-seleraku-static-') && key !== CACHE).map(key => caches.delete(key)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== self.location.origin) return;
    // Account pages, API responses, timers and form submissions are never cached.
    if (event.request.mode === 'navigate' && /^\/(login|register|home-page|page-ke2|sewa-harian|menu-fnb|rental-saya|reservasi(?:\/.*)?)\/?$/.test(url.pathname)) {
        event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE).then(response => response || Response.error())));
    } else if ([OFFLINE, '/icons/app-192.png', '/icons/app-512.png'].includes(url.pathname)) {
        event.respondWith(caches.match(url.pathname).then(response => response || fetch(event.request)));
    }
});
