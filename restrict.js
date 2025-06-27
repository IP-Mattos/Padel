const hourSlotsContainer = document.getElementById("container");
const currentDateDisplay = document.getElementById("currentDate");
const prevDayBtn = document.getElementById("prevDay");
const nextDayBtn = document.getElementById("nextDay");
const datePicker = document.getElementById("datePicker");

let currentDate = new Date();
let selectedCard = null; // To store the selected card

const defaultParams = {
  servicio: 1,
  profe: 0,
};

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
  fetchHours(
    {
      ...defaultParams,
      fecha: formatDate(currentDate),
    },
    hourSlotsContainer
  );
}

async function fetchHours({ servicio, profe, fecha }, container) {
  container.innerHTML = "<p>Cargando horarios...</p>";

  try {
    const formData = new URLSearchParams();
    formData.append("servicio", servicio);
    formData.append("profe", profe);
    formData.append("fecha", fecha);

    const res = await fetch("./accion/getHorarios.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: formData.toString(),
    });

    const json = await res.json();
    container.innerHTML = "";

    if (json.consultaResponse.codigoError === "0") {
      const datos = json.consultaResponse.datos;
      generateHourCards(container, datos, {
        servicio,
        profe,
        fecha,
        container,
      });
    } else {
      container.innerHTML = "<p>Error al cargar horarios</p>";
    }
  } catch (err) {
    console.error("Error fetching hours:", err);
    container.innerHTML = "<p>Error de conexión</p>";
  }
}

function generateHourCards(container, hourData, fetchParams) {
  container.innerHTML = "";

  hourData.forEach(({ hora, estado, idReserva }) => {
    const card = document.createElement("div");
    card.className = "card";
    card.setAttribute("tabindex", "0");
    card.innerHTML = `<span class="hour">${hora}</span>`;

    if (estado === "3") {
      // ❌ Unrestrict button
      const unrestrictBtn = document.createElement("button");
      unrestrictBtn.textContent = "❌";
      unrestrictBtn.className = "unrestrict-btn";
      unrestrictBtn.title = "Eliminar restricción";

      unrestrictBtn.addEventListener("click", async (e) => {
        e.stopPropagation();
        await unrestrictHour(idReserva, fetchParams.fecha);
      });

      card.appendChild(unrestrictBtn);
      card.style.backgroundColor = "red";
      card.style.opacity = "1";
    } else if (estado === 0) {
      // 🔒 Lock button
      const lockBtn = document.createElement("button");
      lockBtn.textContent = "🔒";
      lockBtn.className = "lock-btn";
      lockBtn.title = "Restringir este horario";

      lockBtn.addEventListener("click", async (e) => {
        e.stopPropagation();
        await restrictHour(fetchParams.fecha, hora);
      });

      card.appendChild(lockBtn);

      // Allow selection
      card.addEventListener("click", () => toggleCardSelection(card));
      card.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          toggleCardSelection(card);
        }
      });
    } else {
      // Reserved/unavailable (no interaction)
      card.style.backgroundColor = "red";
      card.style.opacity = "0.6";
      card.style.pointerEvents = "none";
    }

    container.appendChild(card);
  });
}

async function unrestrictHour(idReserv, fecha) {
  const result = await Swal.fire({
    title: "¿Eliminar restricción?",
    text: "Este horario volverá a estar disponible.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, liberar",
    cancelButtonText: "Cancelar",
  });

  if (!result.isConfirmed) return;

  const params = new URLSearchParams();
  params.append("idReserv", idReserv);

  try {
    const res = await fetch("./accion/putReservCancel.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString(),
    });

    const json = await res.json();

    if (json.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Restricción eliminada",
        text: "El horario fue liberado.",
        timer: 1500,
        showConfirmButton: false,
      });

      // Refresh
      fetchHours(
        {
          ...defaultParams,
          fecha,
        },
        hourSlotsContainer
      );
    } else {
      Swal.fire("Error", "No se pudo liberar el horario.", "error");
    }
  } catch (err) {
    console.error("Error unrestricting hour:", err);
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

function toggleCardSelection(card) {
  if (selectedCard) {
    selectedCard.classList.remove("selected");
  }
  card.classList.add("selected");
  selectedCard = card;
}

// Inject selection style
const style = document.createElement("style");
style.textContent = `
  .card.selected {
    background-color: var(--primary-color);
    color: black;
  }
  .card.selected .hour {
    text-shadow: 1px 0 0 black;
  }
`;
document.head.appendChild(style);

async function restrictHour(fecha, hora) {
  const result = await Swal.fire({
    title: "¿Restringir este horario?",
    text: `El horario ${hora} será bloqueado para reservas.`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, restringir",
    cancelButtonText: "No, cancelar",
  });

  if (!result.isConfirmed) return;

  const params = new URLSearchParams();
  params.append("fecha", fecha);
  params.append("hora", hora);
  params.append("userId", userId); // Ensure this is defined globally

  try {
    const res = await fetch("./accion/putRestrictHoras.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString(),
    });

    const json = await res.json();

    if (json.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Horario restringido",
        text: "El horario fue bloqueado con éxito.",
        timer: 1500,
        showConfirmButton: false,
      });

      // Refresh the list
      fetchHours(
        {
          ...defaultParams,
          fecha,
        },
        hourSlotsContainer
      );
    } else {
      Swal.fire("Error", "No se pudo restringir el horario.", "error");
    }
  } catch (err) {
    console.error("Error restricting hour:", err);
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

// Event listeners
const debouncedChangeDate = debounce(changeDate, 300);
prevDayBtn.addEventListener("click", () => debouncedChangeDate(-1));
nextDayBtn.addEventListener("click", () => debouncedChangeDate(1));

currentDateDisplay.addEventListener("click", () => {
  datePicker._flatpickr.open();
});

// Flatpickr setup
flatpickr("#datePicker", {
  disableMobile: true,
  defaultDate: new Date(
    currentDate.getFullYear(),
    currentDate.getMonth(),
    currentDate.getDate()
  ),
  dateFormat: "Y-m-d",
  appendTo: document.body,
  positionElement: currentDateDisplay,
  position: "below",
  onChange: function (selectedDates) {
    if (selectedDates.length) {
      currentDate = new Date(
        selectedDates[0].getFullYear(),
        selectedDates[0].getMonth(),
        selectedDates[0].getDate()
      );
      updateDateLabel();
      fetchHours(
        {
          ...defaultParams,
          fecha: formatDate(currentDate),
        },
        hourSlotsContainer
      );
    }
  },
});

// Init on page load
updateDateLabel();
fetchHours(
  {
    ...defaultParams,
    fecha: formatDate(currentDate),
  },
  hourSlotsContainer
);
