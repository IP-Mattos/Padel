const hourSlotsContainer = document.getElementById("container");
const currentDateDisplay = document.getElementById("currentDate");
const prevDayBtn = document.getElementById("prevDay");
const nextDayBtn = document.getElementById("nextDay");
const datePicker = document.getElementById("datePicker");

let currentDate = new Date();

/* --------------------------------------------------------------
   Helpers
-------------------------------------------------------------- */

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

    for (const slot of slots) {
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
            </div>
          `;
          })
          .join("");

        const isConfirmed = slot.estado == 2;
        div.classList.add(isConfirmed ? "confirmed" : "reserved");

        div.innerHTML = `
          <div class="profiles-container">
              ${profileHtml}
              <p class="slot-time">${horaSinSegundos}</p>
          </div>

          ${
            isConfirmed
              ? `
              <div class="actions">
                ${(() => {
                  const paymentIcon = hasPayments ? "pago.png" : "nopago.png";
                  const paymentAlt = hasPayments
                    ? "Pagos registrados"
                    : "Sin pagos";

                  return `
                    <button class="payments-btn" data-slot='${JSON.stringify(
                      slot
                    )}'>
                      <img class="payment-ico" src="./img/${paymentIcon}" alt="${paymentAlt}">
                    </button>
                  `;
                })()}
              </div>
            `
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
          ${horaSinSegundos}
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

  // Fetch existing payments for THIS agenda
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

  function calculatePrice(hasUser, hour, servicio) {
    if (servicio === 2) return 250; // servicio 2 always 250

    // If there is a user AND hour < 17:00 → 150
    if (hasUser && hour <= 17) {
      return 150;
    }

    // Any other case → 250
    return 250;
  }

  // const computedPrice = calculatePrice();
  const editablePrice = servicio === 2;

  const rowsHtml = [];

  for (let i = 0; i < 4; i++) {
    const userId = userIds[i] || 0;
    const hasUser = userId !== 0;

    const fieldBase = i === 0 ? "Usuario" : `Invitado${i}`;

    const priceField = i === 0 ? "impUsuario" : `impInvitado${i}`;
    const fdpField = i === 0 ? "fdpUsuario" : `fdpInvitado${i}`;

    const price = paymentData
      ? Number(
          paymentData[priceField] || calculatePrice(hasUser, hour, servicio)
        )
      : calculatePrice(hasUser, hour, servicio);

    const fdp = paymentData ? paymentData[fdpField] || "EFECTIVO" : "EFECTIVO";

    rowsHtml.push(await buildPaymentRow(userId, i, price, editablePrice, fdp));
  }

  paymentRows.innerHTML = rowsHtml.join("");

  // Add agenda ID
  paymentRows.innerHTML += `<input type="hidden" name="idAgenda" value="${slot.id}">`;
}

async function buildPaymentRow(userId, index, price, editable, selectedFdp) {
  const fieldBase = index === 0 ? "Usuario" : `Invitado${index}`;
  const priceInputName = index === 0 ? "impUsu" : `impInv${index}`;

  const profile =
    userId !== 0
      ? await fetchProfile(userId)
      : { nombre: "Vacío", imgperfil: null };

  const img = getProfileImage(profile.imgperfil);
  const name = profile.nombre;

  return `
    <div class="payment-row">
        <img src="${img}" class="profile-img" 
             onerror="this.src='./accion/imgPerfilUser/profile.png'">

        <span>${name}</span>

        <input type="hidden" name="id${fieldBase}" value="${userId}">

        <select name="fdp${fieldBase}">
            ${["EFECTIVO", "TRANS", "MERCPAGO", "DEBITO", "CREDITO"]
              .map(
                (fdp) =>
                  `<option value="${fdp}" ${
                    fdp === selectedFdp ? "selected" : ""
                  }>${fdp}</option>`
              )
              .join("")}
        </select>

        ${
          editable
            ? `
              <!-- Editable price ONLY for servicio = 2 -->
              <input 
                type="number" 
                name="${priceInputName}" 
                value="${price}" 
                class="payment-amount">
            `
            : `
              <!-- Fixed price label for servicio = 1 -->
              <span class="price-label">$${price}</span>
              <input type="hidden" name="${priceInputName}" value="${price}">
            `
        }
    </div>
  `;
}

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
    currentDate.getDate()
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
        selectedDates[0].getDate()
      );
      updateDateLabel();
      loadSlots();
    }
  },
});
