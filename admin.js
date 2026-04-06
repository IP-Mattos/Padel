const hourSlotsContainer = document.getElementById("container");
const currentDateDisplay = document.getElementById("currentDate");
const prevDayBtn = document.getElementById("prevDay");
const nextDayBtn = document.getElementById("nextDay");
const datePicker = document.getElementById("datePicker");

let currentDate = new Date();

/* --------------------------------------------------------------
   Helpers
-------------------------------------------------------------- */

let activeTab = "normal"; // "normal" | "serv6"

const tabNormal = document.getElementById("tab-normal");
const tabServ6 = document.getElementById("tab-serv6");

tabNormal.addEventListener("click", () => {
  activeTab = "normal";
  tabNormal.classList.add("active");
  tabServ6.classList.remove("active");
  loadSlots();
});

tabServ6.addEventListener("click", () => {
  activeTab = "serv6";
  tabServ6.classList.add("active");
  tabNormal.classList.remove("active");
  loadSlots();
});

function calculatePrice(hasUser, hour, servicio) {
  if (servicio === 2) return 250; // servicio 2 always 250

  // If there is a user AND hour < 17:00 → 150
  if (hasUser && hour <= 17) {
    return 150;
  }

  // Any other case → 250
  return 250;
}

function debounce(fn, delay) {
  let timer = null;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}

function formatDate(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}

function updateDateLabel() {
  const today = new Date();
  const formattedCurrent = formatDate(currentDate);
  const formattedToday = formatDate(today);

  currentDateDisplay.textContent =
    formattedCurrent === formattedToday
      ? "Hoy"
      : currentDate.toLocaleDateString("es-UY");
}

/* --------------------------------------------------------------
   Change Date
-------------------------------------------------------------- */
function changeDate(offset) {
  currentDate.setDate(currentDate.getDate() + offset);
  updateDateLabel();
  loadSlots();
}

/* --------------------------------------------------------------
   Load Time Slots
-------------------------------------------------------------- */

function getProfileImage(pic) {
  const DEFAULT_IMG = "./accion/imgPerfilUser/profile.png";

  const valid =
    pic &&
    pic.trim() !== "" &&
    pic !== "0" &&
    pic.toLowerCase() !== "profile.png" &&
    pic.toLowerCase() !== "default.png";

  return valid ? `./accion/imgPerfilUser/${pic}` : DEFAULT_IMG;
}

async function loadSlots() {
  hourSlotsContainer.innerHTML = "";

  const params = new URLSearchParams();
  params.append("fechaDesde", formatDate(currentDate));
  params.append("fechaHasta", formatDate(currentDate));

  try {
    const response = await fetch("./accion/getHorasReservAdmin.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });

    const data = await response.json();
    const slots = data.consultaResponse.datos;

    const filteredSlots =
      activeTab === "serv6"
        ? slots.filter((s) => String(s.servicio) === "6")
        : slots.filter((s) => String(s.servicio) !== "6");

    for (const slot of filteredSlots) {
      const paymentInfo = await fetchPayments(slot.id);

      const hasPayments =
        paymentInfo &&
        ((paymentInfo.fdpUsuario || "") !== "" ||
          (paymentInfo.fdpInvitado1 || "") !== "" ||
          (paymentInfo.fdpInvitado2 || "") !== "" ||
          (paymentInfo.fdpInvitado3 || "") !== "");
      const div = document.createElement("div");
      div.className = "card";
      const horaSinSegundos = slot.hora.slice(0, 5);
      const fechaRegistro = slot.fecha.slice(0, 10);

      if ((slot.estado == 1 || slot.estado == 2) && slot.idUsuario) {
        const userIds = [
          slot.idUsuario,
          slot.idUserRival,
          slot.invitado1,
          slot.invitado2,
          slot.invitado3,
        ].filter((id) => id && id !== "0");

        const uniqueIds = [...new Set(userIds)];
        const profiles = await Promise.all(uniqueIds.map(fetchProfile));

        const profileHtml = profiles
          .map((profile) => {
            const img = getProfileImage(profile.imgperfil);
            return `
            <div class="profile">
              <img class="profile-img" src="${img}" alt="${profile.nombre}"
                  onerror="this.src='./accion/imgPerfilUser/profile.png'">
              <p>${profile.nombre}</p>
              <p>${profile.celular.replace(598, 0)}</p>              
            </div>
          `;
          })
          .join("");

        const isConfirmed = slot.estado == 2;
        div.classList.add(isConfirmed ? "confirmed" : "reserved");

        div.innerHTML = `
          <div class="profiles-container">
              ${profileHtml}
              <p class="slot-time">${fechaRegistro + " " + horaSinSegundos}</p>
          </div>

          ${
            isConfirmed
              ? `<div class="actions">
        ${(() => {
          const paymentIcon = hasPayments ? "pago.png" : "nopago.png";
          const paymentAlt = hasPayments ? "Pagos registrados" : "Sin pagos";
          const fixIcon = slot.horaFija == 1 ? "fija.png" : "nofija.png";
          const fixAlt = slot.horaFija == 1 ? "Hora fija" : "Hora no fija";

          return `
            <button class="payments-btn" data-slot='${JSON.stringify({ ...slot, hasPayments })}'>
              <img class="payment-ico" src="./img/${paymentIcon}" alt="${paymentAlt}">
            </button>
            ${
              hasPayments
                ? `
              <button class="fix-btn" data-slot='${JSON.stringify({ ...slot, hasPayments })}'>
                <img class="payment-ico" src="./img/${fixIcon}" alt="${fixAlt}">
              </button>
            `
                : ""
            }
          `;
        })()}
      </div>`
              : ""
          }


          ${
            slot.estado == 1
              ? `
              <div class="actions">
                  <img class="card-ico cancel-btn" src="./img/cancelar.png" data-id="${slot.id}" alt="Cancelar">
                  <img class="card-ico confirm-btn" src="./img/confirmar.png" data-id="${slot.id}" alt="Confirmar">
              </div>`
              : ""
          }
        `;
      }

      // Unavailable
      else if (slot.estado == 3) {
        div.classList.add("unavailable");
        div.innerHTML = `
          ${fechaRegistro + " " + horaSinSegundos}
          <div class="actions">
              <img class="card-ico cancel-btn" data-id="${slot.id}" src="./img/cancelar.png">
          </div>
        `;
      }

      hourSlotsContainer.appendChild(div);
    }

    /* Cancel handler */
    document.querySelectorAll(".cancel-btn").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        if (!id) return;

        const result = await Swal.fire({
          title: "¿Cancelar esta reserva?",
          text: "Esta acción no se puede deshacer.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, cancelar",
          cancelButtonText: "No, mantener",
        });

        if (result.isConfirmed) {
          await updateReservation("putReservCancel.php", id);
        }
      });
    });

    /* Confirm handler */
    document.querySelectorAll(".confirm-btn").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        if (!id) return;

        const result = await Swal.fire({
          title: "¿Confirmar asistencia?",
          text: "La reserva será marcada como utilizada.",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Sí, confirmar",
          cancelButtonText: "No, volver",
        });

        if (result.isConfirmed) {
          await updateReservation("putReservConfirm.php", id);
        }
      });
    });

    /* Payments Modal Handler */
    document.querySelectorAll(".payments-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const slot = JSON.parse(btn.dataset.slot);
        openModal(slot);
      });
    });

    /* Fix Hour Handler */
    document.querySelectorAll(".fix-btn").forEach((btn) => {
      btn.addEventListener("click", async () => {
        const slot = JSON.parse(btn.dataset.slot);
        const isFixed = slot.horaFija == 1;

        const result = await Swal.fire({
          title: isFixed ? "¿Desfijar esta hora?" : "¿Fijar esta hora?",
          text: isFixed
            ? "La hora dejará de estar fija para este usuario."
            : "La hora quedará fija para este usuario.",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Sí",
          cancelButtonText: "No, volver",
        });

        if (!result.isConfirmed) return;

        const dia = currentDate.getDay() + 1;
        const params = new URLSearchParams();
        params.append("dia", dia);
        params.append("hora", slot.hora);
        params.append("servicio", slot.servicio);
        params.append("idUsuario", slot.idUsuario);
        params.append("accion", isFixed ? "0" : "1");

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
              text: isFixed
                ? "Hora desfijada correctamente."
                : "Hora fijada correctamente.",
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
        } catch (err) {
          console.error("Error fixing hour:", err);
          Swal.fire("Error", "No se pudo conectar al servidor.", "error");
        }
      });
    });
  } catch (error) {
    console.error("Error loading slots:", error);
    hourSlotsContainer.innerHTML = "<p>No se encontraron horarios.</p>";
  }
}

/* --------------------------------------------------------------
   Modal Logic
-------------------------------------------------------------- */

const modal = document.getElementById("paymentsModal");
const paymentRows = document.getElementById("paymentRows");
const paymentsForm = document.getElementById("paymentsForm");
const closeModal = document.querySelector(".close-modal");

async function fetchPayments(idAgenda) {
  const params = new URLSearchParams();
  params.append("idAgenda", idAgenda);

  const res = await fetch("./accion/getFDP.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params,
  });

  const data = await res.json();
  return data.consultaResponse?.datos?.[0] || null;
}

async function openModal(slot) {
  modal.classList.remove("hidden");
  paymentRows.innerHTML = "";

  const paymentData = await fetchPayments(slot.id);

  const userIds = [
    slot.idUsuario,
    slot.idUserRival,
    slot.invitado1,
    slot.invitado2,
    slot.invitado3,
  ].filter((id) => id && id !== "0");

  const hour = parseInt(slot.hora.slice(0, 2), 10);
  const servicio = Number(slot.servicio);

  searchModalHour = hour;
  searchModalServicio = servicio;

  const editablePrice = servicio === 2;
  const rowsHtml = [];

  for (let i = 0; i < 4; i++) {
    const userId = userIds[i] || 0;
    const hasUser = userId !== 0;

    const priceField = i === 0 ? "impUsuario" : `impInvitado${i}`;
    const fdpField = i === 0 ? "fdpUsuario" : `fdpInvitado${i}`;

    const price = paymentData
      ? Number(
          paymentData[priceField] || calculatePrice(hasUser, hour, servicio),
        )
      : calculatePrice(hasUser, hour, servicio);

    const fdp = paymentData ? paymentData[fdpField] || "EFECTIVO" : "EFECTIVO";

    rowsHtml.push(
      await buildPaymentRow(
        userId,
        i,
        price,
        editablePrice,
        fdp,
        slot.hasPayments,
      ),
    );
  }

  paymentRows.innerHTML = rowsHtml.join("");
  paymentRows.innerHTML += `<input type="hidden" name="idAgenda" value="${slot.id}">`;

  // Hide "Guardar Pagos" if payments already exist and it's not today
  const saveBtn = paymentsForm.querySelector(".save-btn");
  const isToday = formatDate(currentDate) === formatDate(new Date());
  saveBtn.style.display = slot.hasPayments && !isToday ? "none" : "";
}

async function buildPaymentRow(
  userId,
  index,
  price,
  editable,
  selectedFdp,
  hasPayments,
) {
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;
  const isEmpty = !userId || userId === 0;
  const profile = !isEmpty
    ? await fetchProfile(userId)
    : { nombre: "Vacío", imgperfil: null };
  const img = getProfileImage(profile.imgperfil);
  const name = profile.nombre;

  return `
    <div class="payment-row" data-index="${index}">
      <div class="profile-img-wrapper">
        <img src="${img}" class="profile-img" alt="${name}" onerror="this.src='./accion/imgPerfilUser/profile.png'">
        ${
          !hasPayments
            ? !isEmpty
              ? `<button type="button" class="remove-user-btn" data-index="${index}" title="Quitar usuario">&times;</button>`
              : `<button type="button" class="search-user-btn" data-index="${index}" title="Buscar usuario">🔍</button>`
            : ""
        }
      </div>
      <span class="user-name">${name}</span>
      <input type="hidden" name="id${fieldBase}" value="${userId || 0}">
      <select name="fdp${fieldBase}">
        ${["EFECTIVO", "TRANS", "MERCPAGO", "DEBITO", "CREDITO"]
          .map(
            (fdp) =>
              `<option value="${fdp}" ${fdp === selectedFdp ? "selected" : ""}>${fdp}</option>`,
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

/* -------------------------------------------------------------- 
   Payment Row — User Edit (Remove / Search) 
-------------------------------------------------------------- */

// Use event delegation on paymentRows so it works after innerHTML is set
paymentRows.addEventListener("click", (e) => {
  const removeBtn = e.target.closest(".remove-user-btn");
  const searchBtn = e.target.closest(".search-user-btn");
  if (removeBtn) removeUser(Number(removeBtn.dataset.index));
  if (searchBtn) openUserSearch(Number(searchBtn.dataset.index));
});

function removeUser(index) {
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const row = paymentRows.querySelector(`.payment-row[data-index="${index}"]`);
  if (!row) return;

  // Reset hidden id to 0
  row.querySelector(`input[name="id${fieldBase}"]`).value = "0";

  // Reset profile image and name
  const img = row.querySelector(".profile-img");
  img.src = getProfileImage(null);
  img.alt = "Vacío";
  row.querySelector(".user-name").textContent = "Vacío";

  // Swap X button → search button
  const xBtn = row.querySelector(".remove-user-btn");
  xBtn.className = "search-user-btn";
  xBtn.title = "Buscar usuario";
  xBtn.innerHTML = "🔍";
  xBtn.dataset.index = index;

  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;
  const newPrice = calculatePrice(false, searchModalHour, searchModalServicio);
  const priceInput = row.querySelector(`input[name="${priceInputName}"]`);
  if (priceInput) priceInput.value = newPrice;

  const priceLabel = row.querySelector(".price-label");
  if (priceLabel) priceLabel.textContent = `$${newPrice}`;
}

/* -------------------------------------------------------------- 
   User Search Modal 
-------------------------------------------------------------- */

let searchModalTargetIndex = null;
let selectedSearchUser = null;
let searchModalHour = null;
let searchModalServicio = null;

const userSearchModal = document.getElementById("userSearchModal");
const userSearchInput = document.getElementById("userSearchInput");
const userSearchResults = document.getElementById("userSearchResults");
const confirmUserSelect = document.getElementById("confirmUserSelect");
const closeSearchModal = document.querySelector(".close-search-modal");

closeSearchModal.addEventListener("click", () => {
  userSearchModal.classList.add("hidden");
  selectedSearchUser = null;
  searchModalTargetIndex = null;
});

// Close if clicking outside modal content
userSearchModal.addEventListener("click", (e) => {
  if (e.target === userSearchModal) {
    userSearchModal.classList.add("hidden");
    selectedSearchUser = null;
    searchModalTargetIndex = null;
  }
});

function openUserSearch(index) {
  searchModalTargetIndex = index;
  selectedSearchUser = null;
  userSearchInput.value = "";
  userSearchResults.innerHTML = "";
  confirmUserSelect.disabled = true;
  userSearchModal.classList.remove("hidden");
  userSearchInput.focus();
}

const debouncedSearch = debounce(async (query) => {
  userSearchResults.innerHTML = "";
  confirmUserSelect.disabled = true;
  selectedSearchUser = null;

  if (!query || query.trim().length < 2) return;

  const params = new URLSearchParams();
  params.append("filtroPerfil", query.trim());

  try {
    const res = await fetch("./accion/getPerfiles.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });
    const data = await res.json();
    const users = data.consultaResponse?.registros || [];

    if (users.length === 0) {
      userSearchResults.innerHTML =
        "<p class='no-results'>No se encontraron usuarios.</p>";
      return;
    }

    userSearchResults.innerHTML = users
      .map(
        (u) => `
        <div class="search-result-item" 
             data-id="${u.id}" 
             data-nombre="${u.nombre}" 
             data-imgperfil="${u.imgperfil || ""}">
          <img src="${getProfileImage(u.imgperfil)}" 
               class="profile-img" 
               alt="${u.nombre}"
               onerror="this.src='./accion/imgPerfilUser/profile.png'">
          <span>${u.nombre}</span>
        </div>`,
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
          selectedSearchUser = {
            id: item.dataset.id,
            nombre: item.dataset.nombre,
            imgperfil: item.dataset.imgperfil,
          };
          confirmUserSelect.disabled = false;
        });
      });
  } catch (err) {
    console.error("Error searching users:", err);
    userSearchResults.innerHTML =
      "<p class='no-results'>Error al buscar usuarios.</p>";
  }
}, 300);

userSearchInput.addEventListener("input", (e) =>
  debouncedSearch(e.target.value),
);

confirmUserSelect.addEventListener("click", () => {
  if (!selectedSearchUser || searchModalTargetIndex === null) return;

  const index = searchModalTargetIndex;
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const row = paymentRows.querySelector(`.payment-row[data-index="${index}"]`);
  if (!row) return;

  // Update hidden id
  row.querySelector(`input[name="id${fieldBase}"]`).value =
    selectedSearchUser.id;

  // Update profile image and name
  const img = row.querySelector(".profile-img");
  img.src = getProfileImage(selectedSearchUser.imgperfil);
  img.alt = selectedSearchUser.nombre;
  row.querySelector(".user-name").textContent = selectedSearchUser.nombre;

  // Swap search button → X button
  const searchBtn = row.querySelector(".search-user-btn");
  if (searchBtn) {
    searchBtn.className = "remove-user-btn";
    searchBtn.title = "Quitar usuario";
    searchBtn.innerHTML = "&times;";
    searchBtn.dataset.index = index;
  }

  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;
  const newPrice = calculatePrice(true, searchModalHour, searchModalServicio);
  const priceInput = row.querySelector(`input[name="${priceInputName}"]`);
  if (priceInput) priceInput.value = newPrice;

  // Also update the visible label if it exists (non-editable price)
  const priceLabel = row.querySelector(".price-label");
  if (priceLabel) priceLabel.textContent = `$${newPrice}`;

  // Close modal and reset
  userSearchModal.classList.add("hidden");
  selectedSearchUser = null;
  searchModalTargetIndex = null;
});

closeModal.addEventListener("click", () => modal.classList.add("hidden"));

/* --------------------------------------------------------------
   Submit Payments
-------------------------------------------------------------- */
paymentsForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(paymentsForm);

  try {
    const res = await fetch("./accion/putFDPAgenda.php", {
      method: "POST",
      body: formData,
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
        text: resp.detalleError || "Pagos guardados correctamente",
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
  } catch (err) {
    console.error("Error:", err);
    Swal.fire("Error", "No se pudo conectar al servidor.", "error");
  }
});

/* --------------------------------------------------------------
   Reservation Updates (Cancel/Confirm)
-------------------------------------------------------------- */

async function updateReservation(url, idReserv) {
  const params = new URLSearchParams();
  params.append("idReserv", idReserv);

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
  } catch (error) {
    console.error("Error updating reservation:", error);
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

/* --------------------------------------------------------------
   Fetch profile
-------------------------------------------------------------- */

async function fetchProfile(userId) {
  const params = new URLSearchParams();
  params.append("idPerfil", userId);

  const res = await fetch("./accion/getPerfil.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params,
  });

  const profileData = await res.json();
  return profileData.consultaResponse;
}

/* --------------------------------------------------------------
   Init
-------------------------------------------------------------- */

const debouncedChangeDate = debounce(changeDate, 300);
prevDayBtn.addEventListener("click", () => debouncedChangeDate(-1));
nextDayBtn.addEventListener("click", () => debouncedChangeDate(1));

updateDateLabel();
loadSlots();

currentDateDisplay.addEventListener("click", () => {
  document.querySelector("#datePicker")._flatpickr.open();
});

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
  onChange: function (selectedDates) {
    if (selectedDates.length) {
      currentDate = new Date(
        selectedDates[0].getFullYear(),
        selectedDates[0].getMonth(),
        selectedDates[0].getDate(),
      );
      updateDateLabel();
      loadSlots();
    }
  },
});
