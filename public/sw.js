// Service Worker do CultoGestor — instalável + cache leve dos ícones.
// Mantido propositalmente simples para não interferir no Livewire/conteúdo dinâmico.
const CACHE = 'cultogestor-v1';

self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // Cache-first apenas para os ícones (exibição offline do app).
    if (url.pathname.startsWith('/icons/')) {
        event.respondWith(
            caches.open(CACHE).then((cache) =>
                cache.match(req).then((cached) =>
                    cached || fetch(req).then((resp) => {
                        cache.put(req, resp.clone());
                        return resp;
                    })
                )
            )
        );
    }
    // Demais requisições: rede normal (sem interferir).
});

// --- Push notifications ---
self.addEventListener('push', (event) => {
    let dados = {};
    try { dados = event.data ? event.data.json() : {}; } catch (e) { dados = {}; }

    const titulo = dados.title || 'CultoGestor';
    const opcoes = {
        body: dados.body || '',
        icon: dados.icon || '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        data: { url: dados.url || '/' },
        vibrate: [80, 40, 80],
    };

    event.waitUntil(self.registration.showNotification(titulo, opcoes));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const destino = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientes) => {
            for (const c of clientes) {
                if ('focus' in c) { c.navigate(destino); return c.focus(); }
            }
            if (self.clients.openWindow) return self.clients.openWindow(destino);
        })
    );
});
