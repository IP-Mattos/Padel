const CACHE_VERSION = "1.0.0";
const CACHE_NAME = `gopadel-cache-v${CACHE_VERSION}`;

const urlsToCache = [
  "/",
  "/index.php",
  "/index.js",
  "/style.css",
  "/manifest.json",
  "/img/logo.jpg",
];

// Instalación del Service Worker
self.addEventListener("install", (event) => {
  console.log("SW: Installing version", CACHE_VERSION);
  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => {
        console.log("SW: Caching files");
        return cache.addAll(urlsToCache);
      })
      .then(() => self.skipWaiting()), // Forzar activación inmediata
  );
});

// Activación del Service Worker
self.addEventListener("activate", (event) => {
  console.log("SW: Activating version", CACHE_VERSION);
  event.waitUntil(
    caches
      .keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            // Eliminar cachés antiguos
            if (cacheName !== CACHE_NAME) {
              console.log("SW: Deleting old cache:", cacheName);
              return caches.delete(cacheName);
            }
          }),
        );
      })
      .then(() => self.clients.claim()), // Tomar control inmediato
  );
});

// Interceptar peticiones
self.addEventListener("fetch", (event) => {
  // No cachear peticiones a la API
  if (
    event.request.url.includes("/api/") ||
    event.request.url.includes("/accion/") ||
    event.request.url.includes("version.json")
  ) {
    return;
  }

  event.respondWith(
    caches.match(event.request).then((response) => {
      if (response) {
        return response;
      }
      return fetch(event.request).then((response) => {
        if (!response || response.status !== 200 || response.type !== "basic") {
          return response;
        }
        const responseToCache = response.clone();
        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseToCache);
        });
        return response;
      });
    }),
  );
});

// Escuchar mensajes desde el cliente
self.addEventListener("message", (event) => {
  if (event.data && event.data.type === "SKIP_WAITING") {
    self.skipWaiting();
  }
});

// ============================================================
// PUSH NOTIFICATIONS — añadir al final de sw.js
// ============================================================

// 1. Recibir la notificación desde el servidor
self.addEventListener("push", (event) => {
  let data = { title: "GO Padel", body: "Tenés una nueva notificación." };

  if (event.data) {
    try {
      data = event.data.json();
    } catch (e) {
      data.body = event.data.text();
    }
  }

  const options = {
    body: data.body,
    icon: "/img/logo192v2.png",
    badge: "/img/logo192v2.png",
    data: { url: data.url || "/" }, // URL a abrir al tocar
  };

  event.waitUntil(self.registration.showNotification(data.title, options));
});

// 2. Abrir la app al tocar la notificación
self.addEventListener("notificationclick", (event) => {
  event.notification.close();

  const urlToOpen = event.notification.data?.url || "/";

  event.waitUntil(
    clients
      .matchAll({ type: "window", includeUncontrolled: true })
      .then((clientList) => {
        // Si ya hay una ventana abierta, enfocarla
        for (const client of clientList) {
          if (client.url.includes(self.location.origin) && "focus" in client) {
            return client.focus();
          }
        }
        // Si no, abrir una nueva
        return clients.openWindow(urlToOpen);
      }),
  );
});
