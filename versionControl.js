// ─────────────────────────────────────────────────────────────
//  GO Padel — Version Control
//
//  This class checks version.json on the server and tells the
//  user when an update is available.
//
//  On each deploy you need to update TWO files:
//    1. CACHE_VERSION in sw.js  → triggers cache rebuild
//    2. version.json            → triggers this update prompt
//
//  That's it. Don't change currentVersion here manually —
//  it reads from the page's meta tag so it's always in sync.
// ─────────────────────────────────────────────────────────────

class VersionControl {
  constructor() {
    // Read version from the meta tag injected by PHP/HTML.
    // Falls back to "0.0.0" if the tag is missing.
    this.currentVersion =
      document.querySelector('meta[name="app-version"]')?.content || "0.0.0";

    this.versionCheckKey = "gopadel_last_version_check";
    this.checkInterval = 3600000; // 1 hour
  }

  // ── Semver comparison ──────────────────────────────────────
  // Returns: -1 if v1 < v2 | 0 if equal | 1 if v1 > v2
  compareVersions(v1, v2) {
    const a = v1.split(".").map(Number);
    const b = v2.split(".").map(Number);
    for (let i = 0; i < 3; i++) {
      if (a[i] > b[i]) return 1;
      if (a[i] < b[i]) return -1;
    }
    return 0;
  }

  // ── Rate limiting — don't hammer the server ───────────────
  shouldCheckVersion() {
    const last = localStorage.getItem(this.versionCheckKey);
    if (!last) return true;
    return Date.now() - parseInt(last) > this.checkInterval;
  }

  updateLastCheck() {
    localStorage.setItem(this.versionCheckKey, Date.now().toString());
  }

  // ── Fetch version.json — always bypasses cache ────────────
  async fetchVersionInfo() {
    try {
      const res = await fetch(`/version.json?t=${Date.now()}`, {
        cache: "no-store",
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return await res.json();
    } catch (err) {
      console.warn("[VersionControl] Could not fetch version.json:", err);
      return null;
    }
  }

  // ── Update prompt ─────────────────────────────────────────
  showUpdateModal(info, forced = false) {
    const featureList = info.features?.length
      ? `<ul>${info.features.map((f) => `<li>${f}</li>`).join("")}</ul>`
      : "";

    return Swal.fire({
      title: forced ? "¡Actualización Requerida!" : "Nueva Versión Disponible",
      html: `
        <p><strong>Versión actual:</strong> ${this.currentVersion}</p>
        <p><strong>Nueva versión:</strong> ${info.version}</p>
        ${info.updateMessage ? `<p>${info.updateMessage}</p>` : ""}
        ${featureList ? `<p><strong>Novedades:</strong></p>${featureList}` : ""}
      `,
      icon: "info",
      showCancelButton: !forced,
      confirmButtonText: "Actualizar Ahora",
      cancelButtonText: "Más Tarde",
      allowOutsideClick: !forced,
      allowEscapeKey: !forced,
    });
  }

  // ── Hard reload — wipes cache, unregisters SW, reloads ────
  async forceReload() {
    Swal.fire({
      title: "Actualizando...",
      html: "Por favor esperá mientras actualizamos la aplicación.",
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => Swal.showLoading(),
    });

    try {
      if ("caches" in window) {
        const keys = await caches.keys();
        await Promise.all(keys.map((k) => caches.delete(k)));
      }

      if ("serviceWorker" in navigator) {
        const regs = await navigator.serviceWorker.getRegistrations();
        await Promise.all(regs.map((r) => r.unregister()));
      }

      // Small delay so the SW unregistration settles before reload
      setTimeout(() => window.location.reload(true), 800);
    } catch (err) {
      console.error("[VersionControl] forceReload error:", err);
      Swal.fire({
        title: "Error",
        text: "Hubo un problema al actualizar. Por favor recargá la página manualmente.",
        icon: "error",
      });
    }
  }

  // ── Main check ────────────────────────────────────────────
  async performVersionCheck() {
    if (!this.shouldCheckVersion()) return;

    const info = await this.fetchVersionInfo();
    if (!info) return;

    this.updateLastCheck();

    const { version: serverVersion, minVersion, forceUpdate } = info;
    const isOutdated =
      this.compareVersions(this.currentVersion, minVersion) < 0;
    const hasUpdate =
      this.compareVersions(this.currentVersion, serverVersion) < 0;

    console.log(
      `[VersionControl] current=${this.currentVersion} server=${serverVersion} min=${minVersion}`,
    );

    if (isOutdated || forceUpdate) {
      const result = await this.showUpdateModal(info, true);
      if (result.isConfirmed) await this.forceReload();
    } else if (hasUpdate) {
      const result = await this.showUpdateModal(info, false);
      if (result.isConfirmed) await this.forceReload();
    }
    // If up to date, do nothing quietly.
  }

  // ── Init ──────────────────────────────────────────────────
  init() {
    this.performVersionCheck();

    // Re-check every hour
    setInterval(() => this.performVersionCheck(), this.checkInterval);

    // Re-check when tab comes back into focus
    document.addEventListener("visibilitychange", () => {
      if (!document.hidden) this.performVersionCheck();
    });
  }
}

const versionControl = new VersionControl();
