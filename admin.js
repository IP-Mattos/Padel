const hourSlotsContainer = document.getElementById("container");
const currentDateDisplay = document.getElementById("currentDate");
const prevDayBtn = document.getElementById("prevDay");
const nextDayBtn = document.getElementById("nextDay");

let currentDate = new Date();

function debounce(fn, delay) {
  let timer = null;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}

function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
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

function changeDate(offset) {
  currentDate.setDate(currentDate.getDate() + offset);
  updateDateLabel();
  loadSlots();
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
      const div = document.createElement("div");
      div.className = "card";
      const horaSinSegundos = slot.hora.slice(0, 5); // "14:30:00" → "14:30"

      if ((slot.estado == 1 || slot.estado == 2) && slot.idUsuario) {
        const profile = await fetchProfile(slot.idUsuario);
        const classToAdd = slot.estado == 2 ? "confirmed" : "reserved";
        div.classList.add(classToAdd);

        div.innerHTML = `
    <div class="profile">
      <img class="profile-img" src="./accion/imgPerfilUser/${
        profile.imgperfil
      }" alt="Perfil" />
      <p>${profile.nombre} - ${slot.hora.slice(0, 5)}</p>
    </div>
    ${
      slot.estado == 1
        ? `<div class="actions">
             <img class="card-ico cancel-btn" src="./img/cancelar.png" data-id="${slot.id}" alt="Cancelar">
             <img class="card-ico confirm-btn" src="./img/confirmar.png" data-id="${slot.id}" alt="Confirmar">
           </div>`
        : ""
    }
  `;
      } else if (slot.estado == 3) {
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

    // Cancel button handler
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

    // Confirm button handler
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
  } catch (error) {
    console.error("Error loading slots:", error);
    hourSlotsContainer.innerHTML = "<p>No se encontraron horarios.</p>";
  }
}

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
      console.warn(result);
    }
  } catch (error) {
    console.error("Error updating reservation:", error);
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

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

// Event listeners
const debouncedChangeDate = debounce(changeDate, 300);

prevDayBtn.addEventListener("click", () => debouncedChangeDate(-1));
nextDayBtn.addEventListener("click", () => debouncedChangeDate(1));

// Initialize
updateDateLabel();
loadSlots();

const datePicker = document.getElementById("datePicker");

currentDateDisplay.addEventListener("click", () => {
  document.querySelector("#datePicker")._flatpickr.open();
});

flatpickr("#datePicker", {
  disableMobile: true,
  defaultDate: new Date(
    currentDate.getFullYear(),
    currentDate.getMonth(),
    currentDate.getDate()
  ), // use local date object to avoid timezone issues
  dateFormat: "Y-m-d",
  appendTo: document.body,
  positionElement: document.getElementById("currentDate"),
  position: "below",
  onChange: function (selectedDates) {
    if (selectedDates.length) {
      console.log("📅 Selected date (raw):", selectedDates[0]);
      console.log(
        "📅 Local date string:",
        selectedDates[0].toLocaleDateString()
      );

      currentDate = new Date(
        selectedDates[0].getFullYear(),
        selectedDates[0].getMonth(),
        selectedDates[0].getDate()
      ); // strip time, just to be safe
      updateDateLabel();
      loadSlots();
    }
  },
});
