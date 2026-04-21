class VersionControl {
  constructor() {
    this.currentVersion = "1.0.2"; // Versión actual de la app
    this.versionKey = "gopadel_app_version";
    this.lastCheckKey = "gopadel_last_version_check";
    this.checkInterval = 3600000; // 1 hora en milisegundos
  }

  // Obtener versión almacenada localmente
  getStoredVersion() {
    return localStorage.getItem(this.versionKey) || "0.0.0";
  }

  // Guardar versión actual
  setStoredVersion(version) {
    localStorage.setItem(this.versionKey, version);
  }

  // Verificar si es momento de chequear versión
  shouldCheckVersion() {
    const lastCheck = localStorage.getItem(this.lastCheckKey);
    if (!lastCheck) return true;

    const timeSinceLastCheck = Date.now() - parseInt(lastCheck);
    return timeSinceLastCheck > this.checkInterval;
  }

  // Actualizar timestamp de último chequeo
  updateLastCheck() {
    localStorage.setItem(this.lastCheckKey, Date.now().toString());
  }

  // Comparar versiones (retorna: -1 si v1 < v2, 0 si iguales, 1 si v1 > v2)
  compareVersions(v1, v2) {
    const parts1 = v1.split(".").map(Number);
    const parts2 = v2.split(".").map(Number);

    for (let i = 0; i < 3; i++) {
      if (parts1[i] > parts2[i]) return 1;
      if (parts1[i] < parts2[i]) return -1;
    }
    return 0;
  }

  // Verificar versión con el servidor
  async checkVersion() {
    try {
      const response = await fetch("./version.json?t=" + Date.now(), {
        cache: "no-store",
      });

      if (!response.ok) {
        throw new Error("No se pudo obtener información de versión");
      }

      const versionInfo = await response.json();
      return versionInfo;
    } catch (error) {
      console.error("Error al verificar versión:", error);
      return null;
    }
  }

  // Mostrar modal de actualización
  showUpdateModal(versionInfo, isForced = false) {
    const features = versionInfo.features
      ? `<ul>${versionInfo.features.map((f) => `<li>${f}</li>`).join("")}</ul>`
      : "";

    const modalConfig = {
      title: isForced
        ? "¡Actualización Requerida!"
        : "Nueva Versión Disponible",
      html: `
        <p><strong>Versión actual:</strong> ${this.currentVersion}</p>
        <p><strong>Nueva versión:</strong> ${versionInfo.version}</p>
        ${versionInfo.updateMessage ? `<p>${versionInfo.updateMessage}</p>` : ""}
        ${features ? `<p><strong>Novedades:</strong></p>${features}` : ""}
      `,
      icon: "info",
      showCancelButton: !isForced,
      confirmButtonText: "Actualizar Ahora",
      cancelButtonText: "Más Tarde",
      allowOutsideClick: !isForced,
      allowEscapeKey: !isForced,
    };

    return Swal.fire(modalConfig);
  }

  // Forzar recarga de la aplicación
  async forceReload() {
    // Mostrar loader
    Swal.fire({
      title: "Actualizando...",
      html: "Por favor espera mientras actualizamos la aplicación.",
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    try {
      // Limpiar caché del navegador
      if ("caches" in window) {
        const cacheNames = await caches.keys();
        await Promise.all(
          cacheNames.map((cacheName) => caches.delete(cacheName)),
        );
      }

      // Desregistrar service workers antiguos
      if ("serviceWorker" in navigator) {
        const registrations = await navigator.serviceWorker.getRegistrations();
        await Promise.all(
          registrations.map((registration) => registration.unregister()),
        );
      }

      // Esperar un momento antes de recargar
      setTimeout(() => {
        window.location.reload(true);
      }, 1000);
    } catch (error) {
      console.error("Error al actualizar:", error);
      Swal.fire({
        title: "Error",
        text: "Hubo un problema al actualizar. Por favor recarga la página manualmente.",
        icon: "error",
      });
    }
  }

  // Proceso principal de verificación
  async performVersionCheck() {
    // Verificar si es momento de chequear
    if (!this.shouldCheckVersion()) {
      console.log("Version check skipped - checked recently");
      return;
    }

    console.log("Checking for updates...");
    const versionInfo = await this.checkVersion();

    if (!versionInfo) {
      console.log("Could not retrieve version info");
      return;
    }

    this.updateLastCheck();

    const storedVersion = this.getStoredVersion();
    const serverVersion = versionInfo.version;
    const minVersion = versionInfo.minVersion;
    const forceUpdate = versionInfo.forceUpdate;

    console.log("Current version:", this.currentVersion);
    console.log("Server version:", serverVersion);
    console.log("Stored version:", storedVersion);
    console.log("Min version:", minVersion);

    // Verificar si la versión actual es menor que la mínima requerida
    const isOutdated =
      this.compareVersions(this.currentVersion, minVersion) < 0;

    // Verificar si hay una nueva versión disponible
    const hasUpdate =
      this.compareVersions(this.currentVersion, serverVersion) < 0;

    if (isOutdated || forceUpdate) {
      // Actualización forzada
      console.log("Force update required");
      const result = await this.showUpdateModal(versionInfo, true);
      if (result.isConfirmed) {
        await this.forceReload();
      }
    } else if (hasUpdate) {
      // Actualización opcional
      console.log("Update available");
      const result = await this.showUpdateModal(versionInfo, false);
      if (result.isConfirmed) {
        await this.forceReload();
      } else {
        console.log("User postponed update");
      }
    } else {
      console.log("App is up to date");
      this.setStoredVersion(this.currentVersion);
    }
  }

  // Inicializar control de versiones
  init() {
    // Verificar al cargar la página
    this.performVersionCheck();

    // Verificar periódicamente (cada hora)
    setInterval(() => {
      this.performVersionCheck();
    }, this.checkInterval);

    // Escuchar eventos de visibilidad para verificar cuando el usuario vuelve
    document.addEventListener("visibilitychange", () => {
      if (!document.hidden) {
        this.performVersionCheck();
      }
    });
  }
}

// Exportar instancia
const versionControl = new VersionControl();
