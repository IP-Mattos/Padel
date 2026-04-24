/* ============================================================
   ADMIN.JS
   - Profile & categoria results are cached to avoid redundant
     fetches when the same user appears multiple times per load.
   - Price logic uses categoriasSocios: if estadoSocio === "1"
     the user's categoria valorHoraBaja / valorHoraAlta is used.
     Empty slots (no user) always use the non-socio default.
   - Fixed hours (horaFija == 1): the reserving user (index 0)
     is always priced at the non-socio default rate.
============================================================ */

const hourSlotsContainer = document.getElementById("container");
const currentDateDisplay = document.getElementById("currentDate");
const prevDayBtn = document.getElementById("prevDay");
const nextDayBtn = document.getElementById("nextDay");

let currentDate = new Date();
let activeTab = "normal";

// Per-load caches — cleared on each loadSlots() call
let profileCache = new Map();
let categoriaCache = new Map();

// Generation counter — incremented on every loadSlots() call so
// any in-flight load from a previous date is silently discarded.
let loadGeneration = 0;

/* --------------------------------------------------------------
   Tabs
-------------------------------------------------------------- */
document.getElementById("tab-normal").addEventListener("click", function () {
  activeTab = "normal";
  this.classList.add("active");
  document.getElementById("tab-serv6").classList.remove("active");
  loadSlots();
});

document.getElementById("tab-serv6").addEventListener("click", function () {
  activeTab = "serv6";
  this.classList.add("active");
  document.getElementById("tab-normal").classList.remove("active");
  loadSlots();
});

/* --------------------------------------------------------------
   Helpers
-------------------------------------------------------------- */
function debounce(fn, delay) {
  let t = null;
  return function (...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), delay);
  };
}

function formatDate(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}

// Converts "yyyy-mm-dd" → "dd-mm-yyyy" for display
function formatDateDisplay(isoDate) {
  return isoDate.slice(0, 10).split("-").reverse().join("-");
}

function updateDateLabel() {
  currentDateDisplay.textContent =
    formatDate(currentDate) === formatDate(new Date())
      ? "Hoy"
      : currentDate.toLocaleDateString("es-UY");
}

function changeDate(offset) {
  currentDate.setDate(currentDate.getDate() + offset);
  updateDateLabel();
  loadSlots();
}

function getProfileImage(pic) {
  const isValid =
    pic &&
    pic.trim() !== "" &&
    pic !== "0" &&
    pic.toLowerCase() !== "profile.png" &&
    pic.toLowerCase() !== "default.png";
  return isValid
    ? `./accion/imgPerfilUser/${pic}`
    : "./accion/imgPerfilUser/profile.png";
}

/* --------------------------------------------------------------
   Data Fetchers (with caching)
-------------------------------------------------------------- */
async function fetchProfile(userId) {
  const key = String(userId);
  if (profileCache.has(key)) return profileCache.get(key);

  const params = new URLSearchParams({ idPerfil: userId });
  const res = await fetch("./accion/getPerfil.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params,
  });
  const data = await res.json();
  const profile = data.consultaResponse;
  profileCache.set(key, profile);
  return profile;
}

async function fetchCategoria(categoriaId) {
  const key = String(categoriaId);
  if (categoriaCache.has(key)) return categoriaCache.get(key);

  const params = new URLSearchParams({ idCategoria: categoriaId });
  const res = await fetch("./accion/getCategoriaSocio.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params,
  });
  const data = await res.json();
  const categoria = data.consultaResponse?.categoria || null;
  categoriaCache.set(key, categoria);
  return categoria;
}

async function fetchPayments(idAgenda) {
  const params = new URLSearchParams({ idAgenda });
  const res = await fetch("./accion/getFDP.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params,
  });
  const data = await res.json();
  return data.consultaResponse?.datos?.[0] || null;
}

/* --------------------------------------------------------------
   Price Logic
   - Empty slot (no profile)      → default non-socio price
   - estadoSocio !== "1"          → default non-socio price
   - Active socio                 → categoria valorHoraBaja / valorHoraAlta
   - servicio === 2               → fixed 250 always
   - forceDefault === true        → skip socio lookup (used for the
     reserving user on a fixed hour — they pay the non-socio rate)
-------------------------------------------------------------- */
const DEFAULT_PRICE_BAJA = 150;
const DEFAULT_PRICE_ALTA = 250;

async function getPriceForUser(profile, hour, servicio, forceDefault = false) {
  if (servicio === 2) return DEFAULT_PRICE_ALTA;

  if (!forceDefault) {
    const isActiveSocio =
      profile &&
      profile.estadoSocio === "1" &&
      profile.soySocio &&
      profile.soySocio !== "0";

    if (isActiveSocio) {
      const categoria = await fetchCategoria(profile.soySocio);
      if (categoria) {
        return hour < 17
          ? Number(categoria.valorHoraBaja)
          : Number(categoria.valorHoraAlta);
      }
    }
  }

  return hour < 17 ? DEFAULT_PRICE_BAJA : DEFAULT_PRICE_ALTA;
}

/* --------------------------------------------------------------
   Load Slots
-------------------------------------------------------------- */
async function loadSlots() {
  // Bump generation — any previous async load will see a mismatch
  // and stop appending cards from a stale date.
  const gen = ++loadGeneration;

  // Clear caches on each fresh load
  profileCache = new Map();
  categoriaCache = new Map();

  hourSlotsContainer.innerHTML = `<div class="loading-indicator">Cargando horarios…</div>`;

  const params = new URLSearchParams({
    fechaDesde: formatDate(currentDate),
    fechaHasta: formatDate(currentDate),
  });

  try {
    const res = await fetch("./accion/getHorasReservAdmin.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });
    const data = await res.json();

    // Another load started while we were waiting — bail out
    if (gen !== loadGeneration) return;

    const slots = data.consultaResponse.datos;

    const filtered =
      activeTab === "serv6"
        ? slots.filter((s) => String(s.servicio) === "6")
        : slots.filter((s) => String(s.servicio) !== "6");

    hourSlotsContainer.innerHTML = "";

    for (const slot of filtered) {
      // Check again inside the loop: user may have tapped arrows
      // several times while cards were being built async.
      if (gen !== loadGeneration) return;

      const card = await buildSlotCard(slot);

      if (gen !== loadGeneration) return;

      hourSlotsContainer.appendChild(card);
    }

    attachCardHandlers();
  } catch (err) {
    if (gen !== loadGeneration) return;
    console.error("Error loading slots:", err);
    hourSlotsContainer.innerHTML = `<p class="error-msg">No se pudieron cargar los horarios.</p>`;
  }
}

/* --------------------------------------------------------------
   Build Slot Card
-------------------------------------------------------------- */
async function buildSlotCard(slot) {
  const div = document.createElement("div");
  div.className = "card";
  const time = slot.hora.slice(0, 5);
  const date = formatDateDisplay(slot.fecha); // dd-mm-yyyy
  const hasActiveUser =
    (slot.estado == 1 || slot.estado == 2) && slot.idUsuario;

  if (hasActiveUser) {
    const paymentInfo = await fetchPayments(slot.id);
    const hasPayments =
      paymentInfo &&
      (paymentInfo.fdpUsuario ||
        paymentInfo.fdpInvitado1 ||
        paymentInfo.fdpInvitado2 ||
        paymentInfo.fdpInvitado3);

    const userIds = [
      slot.idUsuario,
      slot.idUserRival,
      slot.invitado1,
      slot.invitado2,
      slot.invitado3,
    ].filter((id) => id && id !== "0");
    const profiles = await Promise.all([...new Set(userIds)].map(fetchProfile));

    const profilesHtml = profiles
      .map(
        (p) => `
      <div class="profile">
        <img class="profile-img" src="${getProfileImage(p.imgperfil)}" alt="${p.nombre}"
             onerror="this.src='./accion/imgPerfilUser/profile.png'">
        <div class="profile-info">
          <p class="profile-name">${p.nombre}</p>
          <p class="profile-phone">${p.celular.replace(598, 0)}</p>
        </div>
      </div>
    `,
      )
      .join("");

    const isConfirmed = slot.estado == 2;
    div.classList.add(isConfirmed ? "confirmed" : "reserved");

    div.innerHTML = `
      <div class="card-header">
        <span class="slot-badge ${isConfirmed ? "badge-confirmed" : "badge-reserved"}">
          ${isConfirmed ? "Confirmada" : "Pendiente"}
        </span>
        <span class="slot-time">${date} · ${time}</span>
      </div>
      <div class="profiles-container">${profilesHtml}</div>
      <div class="actions">
        ${
          isConfirmed
            ? `
          <button class="action-btn payments-btn ${hasPayments ? "has-payment" : ""}"
                  data-slot='${JSON.stringify({ ...slot, hasPayments })}'>
            <img src="./img/${hasPayments ? "pago.png" : "nopago.png"}" alt="">
            ${hasPayments ? "Ver pagos" : "Registrar pago"}
          </button>
          ${
            hasPayments
              ? `
            <button class="action-btn fix-btn"
                    data-slot='${JSON.stringify({ ...slot, hasPayments })}'>
              <img src="./img/${slot.horaFija == 1 ? "fija.png" : "nofija.png"}" alt="">
              ${slot.horaFija == 1 ? "Hora fija" : "No fija"}
            </button>
          `
              : ""
          }
        `
            : `
          <button class="action-btn cancel-btn"  data-id="${slot.id}">
            <img src="./img/cancelar.png" alt=""> Cancelar
          </button>
          <button class="action-btn confirm-btn" data-id="${slot.id}">
            <img src="./img/confirmar.png" alt=""> Confirmar
          </button>
        `
        }
      </div>
    `;
  } else if (slot.estado == 3) {
    div.classList.add("unavailable");
    div.innerHTML = `
      <div class="card-header">
        <span class="slot-badge badge-unavailable">No disponible</span>
        <span class="slot-time">${date} · ${time}</span>
      </div>
      <div class="actions">
        <button class="action-btn cancel-btn" data-id="${slot.id}">
          <img src="./img/cancelar.png" alt=""> Cancelar
        </button>
      </div>
    `;
  }

  return div;
}

/* --------------------------------------------------------------
   Card Event Handlers
-------------------------------------------------------------- */
function attachCardHandlers() {
  hourSlotsContainer.querySelectorAll(".cancel-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const { isConfirmed } = await Swal.fire({
        title: "¿Cancelar esta reserva?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "No, mantener",
      });
      if (isConfirmed) updateReservation("putReservCancel.php", btn.dataset.id);
    });
  });

  hourSlotsContainer.querySelectorAll(".confirm-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const { isConfirmed } = await Swal.fire({
        title: "¿Confirmar asistencia?",
        text: "La reserva será marcada como utilizada.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, confirmar",
        cancelButtonText: "No, volver",
      });
      if (isConfirmed)
        updateReservation("putReservConfirm.php", btn.dataset.id);
    });
  });

  hourSlotsContainer.querySelectorAll(".payments-btn").forEach((btn) => {
    btn.addEventListener("click", () =>
      openPaymentsModal(JSON.parse(btn.dataset.slot)),
    );
  });

  hourSlotsContainer.querySelectorAll(".fix-btn").forEach((btn) => {
    btn.addEventListener("click", () =>
      handleFixHour(JSON.parse(btn.dataset.slot)),
    );
  });
}

async function handleFixHour(slot) {
  const isFixed = slot.horaFija == 1;
  const { isConfirmed } = await Swal.fire({
    title: isFixed ? "¿Desfijar esta hora?" : "¿Fijar esta hora?",
    text: isFixed
      ? "La hora dejará de estar fija para este usuario."
      : "La hora quedará fija para este usuario.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí",
    cancelButtonText: "No, volver",
  });
  if (!isConfirmed) return;

  const params = new URLSearchParams({
    dia: currentDate.getDay() + 1,
    hora: slot.hora,
    servicio: slot.servicio,
    idUsuario: slot.idUsuario,
    accion: isFixed ? "0" : "1",
  });

  try {
    const res = await fetch("./accion/putFijarHora.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });
    const data = await res.json();
    if (data.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Éxito",
        text: isFixed ? "Hora desfijada." : "Hora fijada.",
        timer: 1500,
        showConfirmButton: false,
      });
      loadSlots();
    } else {
      Swal.fire(
        "Error",
        data.consultaResponse?.detalleError || "No se pudo actualizar.",
        "error",
      );
    }
  } catch {
    Swal.fire("Error", "No se pudo conectar al servidor.", "error");
  }
}

/* --------------------------------------------------------------
   Payments Modal
-------------------------------------------------------------- */
const modal = document.getElementById("paymentsModal");
const paymentRows = document.getElementById("paymentRows");
const paymentsForm = document.getElementById("paymentsForm");

let modalHour = null;
let modalServicio = null;

document
  .querySelector(".close-modal")
  .addEventListener("click", () => modal.classList.add("hidden"));

async function openPaymentsModal(slot) {
  modal.classList.remove("hidden");
  paymentRows.innerHTML = `<div class="loading-indicator">Cargando…</div>`;

  const paymentData = await fetchPayments(slot.id);
  const userIds = [
    slot.idUsuario,
    slot.idUserRival,
    slot.invitado1,
    slot.invitado2,
    slot.invitado3,
  ].filter((id) => id && id !== "0");

  modalHour = parseInt(slot.hora.slice(0, 2), 10);
  modalServicio = Number(slot.servicio);

  const editablePrice = modalServicio === 2;
  const isFixed = slot.horaFija == 1;
  const rows = [];

  for (let i = 0; i < 4; i++) {
    rows.push(
      await buildPaymentRow(
        userIds[i] || 0,
        i,
        paymentData,
        editablePrice,
        slot.hasPayments,
        isFixed,
      ),
    );
  }

  paymentRows.innerHTML =
    rows.join("") + `<input type="hidden" name="idAgenda" value="${slot.id}">`;

  const saveBtn = paymentsForm.querySelector(".save-btn");
  const isToday = formatDate(currentDate) === formatDate(new Date());
  saveBtn.style.display = slot.hasPayments && !isToday ? "none" : "";
}

async function buildPaymentRow(
  userId,
  index,
  paymentData,
  editable,
  hasPayments,
  isFixed = false,
) {
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;
  const fdpField = index === 0 ? "fdpUsuario" : `fdpInvitado${index}`;
  const priceField = index === 0 ? "impUsuario" : `impInvitado${index}`;

  const isEmpty = !userId || userId === 0;
  const profile = !isEmpty ? await fetchProfile(userId) : null;
  const img = getProfileImage(profile?.imgperfil);
  const name = profile?.nombre || "Vacío";

  // Fixed hours: the reserving user (index 0) pays the non-socio default —
  // no discount regardless of their category. Everyone else pays normally.
  const forceDefault = isFixed && index === 0;

  // Use saved price if it exists; otherwise calculate from categoria
  const price = paymentData?.[priceField]
    ? Number(paymentData[priceField])
    : await getPriceForUser(profile, modalHour, modalServicio, forceDefault);

  const selectedFdp = paymentData?.[fdpField] || "EFECTIVO";

  return `
    <div class="payment-row" data-index="${index}">
      <div class="profile-img-wrapper">
        <img src="${img}" class="profile-img" alt="${name}"
             onerror="this.src='./accion/imgPerfilUser/profile.png'">
        ${
          !hasPayments
            ? !isEmpty
              ? `<button type="button" class="remove-user-btn" data-index="${index}">&times;</button>`
              : `<button type="button" class="search-user-btn" data-index="${index}">🔍</button>`
            : ""
        }
      </div>
      <span class="user-name">${name}</span>
      <input type="hidden" name="id${fieldBase}" value="${userId || 0}">
      <select name="fdp${fieldBase}">
        ${["EFECTIVO", "TRANS", "MERCPAGO", "DEBITO", "CREDITO"]
          .map(
            (f) =>
              `<option value="${f}" ${f === selectedFdp ? "selected" : ""}>${f}</option>`,
          )
          .join("")}
      </select>
      ${
        editable
          ? `<input type="number" name="${priceInputName}" value="${price}" class="payment-amount">`
          : `<span class="price-label">$${price}</span>
           <input type="hidden" name="${priceInputName}" value="${price}">`
      }
    </div>
  `;
}

/* Payment row — remove / search via event delegation */
paymentRows.addEventListener("click", (e) => {
  const remove = e.target.closest(".remove-user-btn");
  const search = e.target.closest(".search-user-btn");
  if (remove) removeUserFromRow(Number(remove.dataset.index));
  if (search) openUserSearch(Number(search.dataset.index));
});

async function removeUserFromRow(index) {
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;
  const row = paymentRows.querySelector(`.payment-row[data-index="${index}"]`);
  if (!row) return;

  row.querySelector(`input[name="id${fieldBase}"]`).value = "0";
  row.querySelector(".profile-img").src = getProfileImage(null);
  row.querySelector(".user-name").textContent = "Vacío";

  const btn = row.querySelector(".remove-user-btn");
  btn.className = "search-user-btn";
  btn.title = "Buscar usuario";
  btn.innerHTML = "🔍";
  btn.dataset.index = index;

  // Empty slot → no socio pricing
  const newPrice = await getPriceForUser(null, modalHour, modalServicio);
  const input = row.querySelector(`input[name="${priceInputName}"]`);
  if (input) input.value = newPrice;
  const label = row.querySelector(".price-label");
  if (label) label.textContent = `$${newPrice}`;
}

/* Submit payments */
paymentsForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  try {
    const res = await fetch("./accion/putFDPAgenda.php", {
      method: "POST",
      body: new FormData(paymentsForm),
    });
    const result = await res.json();
    const resp = result?.consultaResponse;

    if (!resp) {
      Swal.fire("Error", "Respuesta inválida del servidor.", "error");
      return;
    }

    if (resp.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Éxito",
        text: "Pagos guardados correctamente",
        timer: 1500,
        showConfirmButton: false,
      });
      modal.classList.add("hidden");
      loadSlots();
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: resp.detalleError || "No se pudieron guardar los pagos.",
      });
    }
  } catch {
    Swal.fire("Error", "No se pudo conectar al servidor.", "error");
  }
});

/* --------------------------------------------------------------
   User Search Modal
-------------------------------------------------------------- */
let searchTargetIndex = null;
let selectedUser = null;

const userSearchModal = document.getElementById("userSearchModal");
const userSearchInput = document.getElementById("userSearchInput");
const userSearchResults = document.getElementById("userSearchResults");
const confirmUserSelect = document.getElementById("confirmUserSelect");

document
  .querySelector(".close-search-modal")
  .addEventListener("click", closeUserSearchModal);
userSearchModal.addEventListener("click", (e) => {
  if (e.target === userSearchModal) closeUserSearchModal();
});

function closeUserSearchModal() {
  userSearchModal.classList.add("hidden");
  selectedUser = null;
  searchTargetIndex = null;
}

function openUserSearch(index) {
  searchTargetIndex = index;
  selectedUser = null;
  userSearchInput.value = "";
  userSearchResults.innerHTML = "";
  confirmUserSelect.disabled = true;
  userSearchModal.classList.remove("hidden");
  userSearchInput.focus();
}

const debouncedSearch = debounce(async (query) => {
  userSearchResults.innerHTML = "";
  confirmUserSelect.disabled = true;
  selectedUser = null;

  if (!query || query.trim().length < 2) return;

  const params = new URLSearchParams({ filtroPerfil: query.trim() });
  try {
    const res = await fetch("./accion/getPerfiles.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });
    const data = await res.json();
    const users = data.consultaResponse?.registros || [];

    if (!users.length) {
      userSearchResults.innerHTML =
        "<p class='no-results'>No se encontraron usuarios.</p>";
      return;
    }

    userSearchResults.innerHTML = users
      .map(
        (u) => `
      <div class="search-result-item"
           data-id="${u.id}" data-nombre="${u.nombre}" data-imgperfil="${u.imgperfil || ""}">
        <img src="${getProfileImage(u.imgperfil)}" class="profile-img" alt="${u.nombre}"
             onerror="this.src='./accion/imgPerfilUser/profile.png'">
        <span>${u.nombre}</span>
      </div>
    `,
      )
      .join("");

    userSearchResults
      .querySelectorAll(".search-result-item")
      .forEach((item) => {
        item.addEventListener("click", () => {
          userSearchResults
            .querySelectorAll(".search-result-item")
            .forEach((i) => i.classList.remove("selected"));
          item.classList.add("selected");
          selectedUser = {
            id: item.dataset.id,
            nombre: item.dataset.nombre,
            imgperfil: item.dataset.imgperfil,
          };
          confirmUserSelect.disabled = false;
        });
      });
  } catch {
    userSearchResults.innerHTML =
      "<p class='no-results'>Error al buscar usuarios.</p>";
  }
}, 300);

userSearchInput.addEventListener("input", (e) =>
  debouncedSearch(e.target.value),
);

confirmUserSelect.addEventListener("click", async () => {
  if (!selectedUser || searchTargetIndex === null) return;

  const index = searchTargetIndex;
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const row = paymentRows.querySelector(`.payment-row[data-index="${index}"]`);
  if (!row) return;

  row.querySelector(`input[name="id${fieldBase}"]`).value = selectedUser.id;
  row.querySelector(".profile-img").src = getProfileImage(
    selectedUser.imgperfil,
  );
  row.querySelector(".profile-img").alt = selectedUser.nombre;
  row.querySelector(".user-name").textContent = selectedUser.nombre;

  const searchBtn = row.querySelector(".search-user-btn");
  if (searchBtn) {
    searchBtn.className = "remove-user-btn";
    searchBtn.title = "Quitar usuario";
    searchBtn.innerHTML = "&times;";
    searchBtn.dataset.index = index;
  }

  // Calculate price based on the newly added user's categoria
  const profile = await fetchProfile(selectedUser.id);
  const newPrice = await getPriceForUser(profile, modalHour, modalServicio);
  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;
  const priceInput = row.querySelector(`input[name="${priceInputName}"]`);
  if (priceInput) priceInput.value = newPrice;
  const priceLabel = row.querySelector(".price-label");
  if (priceLabel) priceLabel.textContent = `$${newPrice}`;

  closeUserSearchModal();
});

/* --------------------------------------------------------------
   Reservation Actions
-------------------------------------------------------------- */
async function updateReservation(url, idReserv) {
  const params = new URLSearchParams({ idReserv });
  try {
    const res = await fetch(`./accion/${url}`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });
    const result = await res.json();

    if (result.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Éxito",
        text: "La acción se realizó correctamente.",
        timer: 1500,
        showConfirmButton: false,
      });
      loadSlots();
    } else {
      Swal.fire("Error", "No se pudo actualizar la reserva.", "error");
    }
  } catch {
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

/* --------------------------------------------------------------
   Init
-------------------------------------------------------------- */
const debouncedChangeDate = debounce(changeDate, 300);
prevDayBtn.addEventListener("click", () => debouncedChangeDate(-1));
nextDayBtn.addEventListener("click", () => debouncedChangeDate(1));

currentDateDisplay.addEventListener("click", () =>
  document.querySelector("#datePicker")._flatpickr.open(),
);

flatpickr("#datePicker", {
  disableMobile: true,
  defaultDate: new Date(
    currentDate.getFullYear(),
    currentDate.getMonth(),
    currentDate.getDate(),
  ),
  dateFormat: "Y-m-d",
  appendTo: document.body,
  positionElement: document.getElementById("currentDate"),
  position: "below",
  onChange(selectedDates) {
    if (!selectedDates.length) return;
    const d = selectedDates[0];
    currentDate = new Date(d.getFullYear(), d.getMonth(), d.getDate());
    updateDateLabel();
    loadSlots();
  },
});

updateDateLabel();
loadSlots();
