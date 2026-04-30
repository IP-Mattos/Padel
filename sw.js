// ─────────────────────────────────────────────────────────────
//  GO Padel — Service Worker
//
//  THE ONLY LINE YOU NEED TO CHANGE ON EACH DEPLOY:
const CACHE_VERSION = "1.0.4";
// ─────────────────────────────────────────────────────────────

const CACHE_NAME = `gopadel-v${CACHE_VERSION}`;

// Static assets that are safe to cache — these never change between
// deploys unless you bump CACHE_VERSION, which wipes and rebuilds.
// Do NOT include .php files here. Ever.
const PRECACHE_URLS = [
  "/index.js",
  "/landing.js",
  "/style.css",
  "/landing.css",
  "/admin.css",
  "/admin.js",
  "/push-manager.js",
  "/manifest.json",
  "/favicon.ico",
  "/img/logo.jpg",
  "/img/logo192v2.png",
  "/img/logo512v2.png",
  "/img/reserva.png",
  "/img/puntos.png",
  "/img/profile.png",
  "/img/llave.png",
  "/img/logout.png",
];

// ─────────────────────────────────────────────────────────────
//  Helpers
// ─────────────────────────────────────────────────────────────

// Returns true for anything that must ALWAYS hit the network.
// PHP pages are session-dependent — serving them from cache would
// give the wrong loggedIn state, which is exactly the bug we're fixing.
function mustBeNetwork(url) {
  try {
    const { pathname } = new URL(url);
    return (
      pathname.endsWith(".php") ||
      pathname === "/" ||
      pathname.includes("/api/") ||
      pathname.includes("/accion/") ||
      pathname.includes("/pagos/") ||
      pathname.endsWith("version.json")
    );
  } catch {
    return false;
  }
}

// ─────────────────────────────────────────────────────────────
//  Install — pre-cache static shell, activate immediately
// ─────────────────────────────────────────────────────────────
self.addEventListener("install", (event) => {
  console.log(`[SW] Installing v${CACHE_VERSION}`);
  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => {
        console.log(`[SW] Pre-cache complete`);
        // Take over immediately — don't wait for all tabs to close.
        // This avoids the "waiting" state that causes NS_BINDING_ABORTED.
        return self.skipWaiting();
      }),
  );
});

// ─────────────────────────────────────────────────────────────
//  Activate — delete old caches, claim all clients
// ─────────────────────────────────────────────────────────────
self.addEventListener("activate", (event) => {
  console.log(`[SW] Activating v${CACHE_VERSION}`);
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter((k) => k !== CACHE_NAME)
            .map((k) => {
              console.log(`[SW] Deleting old cache: ${k}`);
              return caches.delete(k);
            }),
        ),
      )
      .then(() => self.clients.claim()),
  );
});

// ─────────────────────────────────────────────────────────────
//  Fetch — the core strategy
//
//  PHP / API → Network only (never touch the cache)
//  Static assets → Cache first, update cache in background
// ─────────────────────────────────────────────────────────────
self.addEventListener("fetch", (event) => {
  // Ignore non-GET (POST to API etc.)
  if (event.request.method !== "GET") return;

  // ── PHP pages and API calls: go straight to the network ──
  if (mustBeNetwork(event.request.url)) {
    event.respondWith(
      fetch(event.request).catch(() => {
        // Only reach here if the user is genuinely offline.
        // Return a minimal offline response so the browser doesn't
        // show a scary error — adjust the message as you like.
        return new Response(
          "<h2>Sin conexión</h2><p>Verificá tu conexión a internet.</p>",
          { headers: { "Content-Type": "text/html" } },
        );
      }),
    );
    return;
  }

  // ── Static assets: cache first, refresh in background ──
  // The user gets an instant response from cache. Meanwhile the SW
  // fetches a fresh copy and stores it for the next visit.
  event.respondWith(
    caches.open(CACHE_NAME).then((cache) =>
      cache.match(event.request).then((cached) => {
        const networkFetch = fetch(event.request)
          .then((response) => {
            if (response.ok) {
              cache.put(event.request, response.clone());
            }
            return response;
          })
          .catch((err) => {
            // Only fall back to cache if we actually have something cached
            if (cached) return cached;
            // Otherwise let the browser handle the failure naturally
            throw err;
          });

        return cached || networkFetch;
      }),
    ),
  );
});

// ─────────────────────────────────────────────────────────────
//  Messages from the page
// ─────────────────────────────────────────────────────────────
self.addEventListener("message", (event) => {
  if (event.data?.type === "SKIP_WAITING") {
    self.skipWaiting();
  }
});

// ─────────────────────────────────────────────────────────────
//  Push notifications
// ─────────────────────────────────────────────────────────────
self.addEventListener("push", (event) => {
  let data = { title: "GO Padel", body: "Tenés una nueva notificación." };

  if (event.data) {
    try {
      data = event.data.json();
    } catch {
      data.body = event.data.text();
    }
  }

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: "/img/logo192v2.png",
      badge: "/img/logo192v2.png",
      data: { url: data.url || "/" },
    }),
  );
});

self.addEventListener("notificationclick", (event) => {
  event.notification.close();
  const urlToOpen = event.notification.data?.url || "/";

  event.waitUntil(
    clients
      .matchAll({ type: "window", includeUncontrolled: true })
      .then((clientList) => {
        for (const client of clientList) {
          if (client.url.includes(self.location.origin) && "focus" in client) {
            return client.focus();
          }
        }
        return clients.openWindow(urlToOpen);
      }),
  );
});
