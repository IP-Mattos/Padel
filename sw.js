self.addEventListener("install", (event) => {
  // Skip waiting, so the new service worker takes control immediately
  self.skipWaiting();

  event.waitUntil(
    caches.open("static").then((cache) => {
      return cache.addAll(["./img/logo192.png", "./img/logo512.png"]);
    })
  );
});

self.addEventListener("activate", (event) => {
  // Claim control of all clients immediately
  event.waitUntil(clients.claim());
});

self.addEventListener("fetch", (e) => {
  // Only handle static asset requests
  e.respondWith(
    caches.match(e.request).then((response) => {
      return (
        response || fetch(e.request) // Serve from cache if available, else fetch from network
      );
    })
  );
});
