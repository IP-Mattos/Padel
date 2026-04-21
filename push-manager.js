// ============================================================
// push-manager.js  — incluir en las páginas donde quieras
//                    pedir permiso (ej: index.php, landing.php)
//
// Uso:  <script src="push-manager.js"></script>
//       pushManager.init();   (llamar después del DOMContentLoaded)
// ============================================================

const pushManager = (() => {
  // ▸ Reemplazá con tu clave pública VAPID (ver generate-vapid-keys.php)
  const VAPID_PUBLIC_KEY =
    "BGhsYwW3JWMqSmeN_P2fP4PGnK9L8Nek4XA7AjfuKN8CUzW8bzLHRvOp5ntXBg_ou_9dglz79ZS_OtAfqprRwKE";

  // Convierte la clave VAPID de base64url a Uint8Array (requerido por la API)
  function urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
      .replace(/-/g, "+")
      .replace(/_/g, "/");
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
  }

  // Suscribe al usuario y envía el objeto al servidor
  async function init(registration = null) {
    if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
      console.warn("Push notifications no soportadas.");
      return;
    }

    const permission = await Notification.requestPermission();
    if (permission !== "granted") {
      console.warn("Permiso denegado.");
      return;
    }

    try {
      await subscribe(registration);
    } catch (err) {
      console.error("Error al suscribirse a push:", err);
    }
  }

  async function subscribe(registration = null) {
    console.log("1. subscribe() called");

    // Use passed registration or fall back to waiting for an active one
    const activeRegistration =
      registration ||
      (await Promise.race([
        navigator.serviceWorker.ready,
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error("SW activation timed out")), 10000),
        ),
      ]));

    console.log("2. active registration:", activeRegistration);

    const convertedKey = urlBase64ToUint8Array(VAPID_PUBLIC_KEY);

    const subscription = await activeRegistration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: convertedKey,
    });
    console.log("3. Subscription created:", subscription);

    const response = await fetch("/api/push/save-subscription.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(subscription),
    });
    console.log("4. Fetch done, status:", response.status);
    const text = await response.text();
    console.log("5. Response body:", text);
  }

  return { init };
})();
