// PlantDoc Service Worker — v2.0 (file d'attente hors-ligne)
const CACHE_NAME = 'plantdoc-v2';
const BASE = '/plantdoc';

// Assets statiques à mettre en cache à l'installation
const STATIC_ASSETS = [
  `${BASE}/`,
  `${BASE}/diagnostic/new`,
  `${BASE}/public/css/style.css`,
  `${BASE}/public/css/redesign.css`,
  `${BASE}/public/css/responsive.css`,
  `${BASE}/public/css/pages.css`,
  `${BASE}/public/js/offline-queue.js`,
  `${BASE}/public/images/icon-192.png`,
  `${BASE}/public/images/icon-512.png`,
  `${BASE}/public/images/farmer-hero.jpg`,
  `${BASE}/public/images/farmer-field.jpg`,
  `${BASE}/public/images/cocoa.jpg`,
  `${BASE}/public/images/tomato.jpg`,
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
  'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
];

// INSTALL — pré-cache des ressources statiques
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('[SW] Pré-cache des ressources statiques');
      return cache.addAll(STATIC_ASSETS).catch(err => {
        console.warn('[SW] Certaines ressources non cachées', err);
      });
    })
  );
  self.skipWaiting();
});

// ACTIVATE — nettoyage des anciens caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(names =>
      Promise.all(names.filter(n => n !== CACHE_NAME).map(n => caches.delete(n)))
    )
  );
  self.clients.claim();
});

// FETCH — stratégies de cache
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Ne pas cacher les POST ou les routes API/auth
  if (request.method !== 'GET') return;
  if (url.pathname.includes('/login') || url.pathname.includes('/logout') ||
      url.pathname.includes('/diagnostic') && request.method === 'POST') return;

  // Stratégie : Cache First pour les assets statiques (CSS, JS, images, fonts)
  if (/\.(css|js|png|jpg|jpeg|webp|svg|woff2?|ttf)$/i.test(url.pathname) ||
      url.hostname.includes('fonts.gstatic.com') ||
      url.hostname.includes('cdnjs.cloudflare.com')) {
    event.respondWith(
      caches.match(request).then(cached => {
        if (cached) return cached;
        return fetch(request).then(response => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
          }
          return response;
        });
      })
    );
    return;
  }

  // Stratégie : Network First pour les pages HTML (avec fallback cache)
  event.respondWith(
    fetch(request)
      .then(response => {
        if (response.ok && url.origin === self.location.origin) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
        }
        return response;
      })
      .catch(() => caches.match(request).then(cached =>
        cached || new Response(
          '<h1 style="font-family:sans-serif;text-align:center;padding:40px;color:#2d6a4f">Hors-ligne</h1><p style="text-align:center">PlantDoc n\'a pas pu charger cette page. Vérifiez votre connexion.</p>',
          { headers: { 'Content-Type': 'text/html' } }
        )
      ))
  );
});

// Message handler — pour reset cache à la demande
self.addEventListener('message', event => {
  if (event.data === 'SKIP_WAITING') self.skipWaiting();
  if (event.data === 'CLEAR_CACHE') {
    caches.delete(CACHE_NAME).then(() => console.log('[SW] Cache vidé'));
  }
});
