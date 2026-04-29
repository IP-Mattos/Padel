// ─── DOM refs ─────────────────────────────────────────────
const container = document.getElementById("container");

// ─── Constants ────────────────────────────────────────────
const DEFAULT_IMG = "./accion/imgPerfilUser/profile.png";

// ─── Profile helpers ──────────────────────────────────────
async function fetchProfile(userId) {
  const body = new URLSearchParams({ idPerfil: userId });
  const res = await fetch("./accion/getPerfil.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  });
  const data = await res.json();
  return data.consultaResponse;
}

function resolveProfileImage(pic) {
  const invalid =
    !pic ||
    pic.trim() === "" ||
    pic === "0" ||
    pic.toLowerCase() === "profile.png" ||
    pic.toLowerCase() === "default.png";
  return invalid ? DEFAULT_IMG : `./accion/imgPerfilUser/${pic}`;
}

// ─── Canje actions ────────────────────────────────────────
async function updateCanje(endpoint, idCanje) {
  const body = new URLSearchParams({ idCanje });
  try {
    const res = await fetch(`./accion/${endpoint}`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    });
    const data = await res.json();

    if (data.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Éxito",
        timer: 1500,
        showConfirmButton: false,
      });
      loadSlots();
    } else {
      Swal.fire("Error", "No se pudo modificar el canje.", "error");
    }
  } catch {
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

async function handleCancel(id) {
  const { isConfirmed } = await Swal.fire({
    title: "¿Cancelar este canje?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, cancelar",
    cancelButtonText: "No, mantener",
  });
  if (isConfirmed) await updateCanje("putCanjeCancel.php", id);
}

async function handleConfirm(id) {
  const { isConfirmed } = await Swal.fire({
    title: "¿Confirmar canje?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, confirmar",
    cancelButtonText: "No, volver",
  });
  if (isConfirmed) await updateCanje("putCanjeConfirm.php", id);
}

// ─── Card builder ─────────────────────────────────────────
function buildCard(profile, slot) {
  const img = resolveProfileImage(profile.imgperfil);
  const phone = profile.celular?.toString().replace("598", "0") ?? "—";
  const points = slot.puntos ?? 0;

  const card = document.createElement("div");
  card.className = "card";
  card.innerHTML = `
    <div class="profile">
      <img
        class="profile-img"
        src="${img}"
        alt="${profile.nombre}"
        onerror="this.src='${DEFAULT_IMG}'"
      />
      <div class="profile-info">
        <span class="profile-name">${profile.nombre}</span>
        <span class="profile-phone">${phone}</span>
        <span class="profile-points">★ ${points} pts</span>
      </div>
    </div>
    <div class="actions">
      <button class="action-btn confirm" title="Confirmar canje" data-id="${slot.id}">
        <img src="./img/confirmar.png" alt="Confirmar" />
      </button>
      <button class="action-btn cancel" title="Cancelar canje" data-id="${slot.id}">
        <img src="./img/cancelar.png" alt="Cancelar" />
      </button>
    </div>
  `;

  card
    .querySelector(".confirm")
    .addEventListener("click", () => handleConfirm(slot.id));
  card
    .querySelector(".cancel")
    .addEventListener("click", () => handleCancel(slot.id));

  return card;
}

// ─── Load all slots ───────────────────────────────────────
async function loadSlots() {
  container.innerHTML = "<p>Cargando canjes…</p>";

  try {
    const res = await fetch("./accion/getCanjeUser.php", { method: "POST" });
    const data = await res.json();
    const slots = data.consultaResponse.datos ?? [];

    container.innerHTML = "";

    if (slots.length === 0) {
      container.innerHTML = "<p>No hay canjes pendientes.</p>";
      return;
    }

    // Fetch all profiles in parallel, then render
    const profiles = await Promise.all(
      slots.map((s) => fetchProfile(s.usuario)),
    );

    slots.forEach((slot, i) => {
      container.appendChild(buildCard(profiles[i], slot));
    });
  } catch {
    container.innerHTML = "<p>Error al cargar los canjes.</p>";
  }
}

// ─── Init ─────────────────────────────────────────────────
loadSlots();
