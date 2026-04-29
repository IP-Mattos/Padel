// ─── DOM refs ────────────────────────────────────────────
const hourSlotsContainer = document.getElementById("container");
const currentDateDisplay = document.getElementById("currentDate");
const prevDayBtn = document.getElementById("prevDay");
const nextDayBtn = document.getElementById("nextDay");
const datePicker = document.getElementById("datePicker");

// ─── State ───────────────────────────────────────────────
let currentDate = new Date();
let currentServicio = 1;
let selectedCard = null;

// ─── Helpers ─────────────────────────────────────────────
function formatDate(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}

function debounce(fn, delay) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), delay);
  };
}

// ─── Date display ─────────────────────────────────────────
function updateDateLabel() {
  const isToday = formatDate(currentDate) === formatDate(new Date());
  currentDateDisplay.textContent = isToday
    ? "Hoy"
    : currentDate.toLocaleDateString("es-UY");
}

function changeDate(offset) {
  currentDate.setDate(currentDate.getDate() + offset);
  updateDateLabel();
  fetchHours();
}

// ─── Fetch hours ─────────────────────────────────────────
async function fetchHours() {
  hourSlotsContainer.innerHTML = "<p>Cargando horarios…</p>";

  const body = new URLSearchParams({
    servicio: currentServicio,
    profe: 0,
    fecha: formatDate(currentDate),
  });

  try {
    const res = await fetch("./accion/getHorarios.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    });
    const json = await res.json();
    hourSlotsContainer.innerHTML = "";

    if (json.consultaResponse.codigoError === "0") {
      renderHourCards(json.consultaResponse.datos);
    } else {
      hourSlotsContainer.innerHTML = "<p>Error al cargar horarios.</p>";
    }
  } catch {
    hourSlotsContainer.innerHTML = "<p>Error de conexión.</p>";
  }
}

// ─── Render cards ────────────────────────────────────────
function renderHourCards(hourData) {
  selectedCard = null;

  hourData.forEach(({ hora, estado, idReserva }) => {
    const card = document.createElement("div");
    card.className = "card";
    card.setAttribute("tabindex", "0");

    const hourSpan = document.createElement("span");
    hourSpan.className = "hour";
    hourSpan.textContent = hora;
    card.appendChild(hourSpan);

    if (estado === "3") {
      // Restricted — show unrestrict button
      card.classList.add("state-restricted");
      const btn = makeButton("❌", "unrestrict-btn", "Eliminar restricción");
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        unrestrictHour(idReserva);
      });
      card.appendChild(btn);
    } else if (estado === 0) {
      // Available — selectable + lock button
      const btn = makeButton("🔒", "lock-btn", "Restringir este horario");
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        restrictHour(hora);
      });
      card.appendChild(btn);

      card.addEventListener("click", () => toggleSelection(card));
      card.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          toggleSelection(card);
        }
      });
    } else {
      // Reserved / unavailable
      card.classList.add("state-taken");
      card.removeAttribute("tabindex");
    }

    hourSlotsContainer.appendChild(card);
  });
}

function makeButton(label, className, title) {
  const btn = document.createElement("button");
  btn.textContent = label;
  btn.className = className;
  btn.title = title;
  return btn;
}

function toggleSelection(card) {
  if (selectedCard) selectedCard.classList.remove("selected");
  selectedCard = card;
  card.classList.add("selected");
}

// ─── Restrict hour ───────────────────────────────────────
async function restrictHour(hora) {
  const { isConfirmed } = await Swal.fire({
    title: "¿Restringir este horario?",
    text: `El horario ${hora} será bloqueado para reservas.`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, restringir",
    cancelButtonText: "Cancelar",
  });

  if (!isConfirmed) return;

  const body = new URLSearchParams({
    fecha: formatDate(currentDate),
    hora,
    servicio: currentServicio,
    userId,
  });

  try {
    const res = await fetch("./accion/putRestrictHoras.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    });
    const json = await res.json();

    if (json.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Horario restringido",
        timer: 1500,
        showConfirmButton: false,
      });
      fetchHours();
    } else {
      Swal.fire("Error", "No se pudo restringir el horario.", "error");
    }
  } catch {
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

// ─── Unrestrict hour ─────────────────────────────────────
async function unrestrictHour(idReserv) {
  const { isConfirmed } = await Swal.fire({
    title: "¿Eliminar restricción?",
    text: "Este horario volverá a estar disponible.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, liberar",
    cancelButtonText: "Cancelar",
  });

  if (!isConfirmed) return;

  const body = new URLSearchParams({ idReserv });

  try {
    const res = await fetch("./accion/putReservCancel.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    });
    const json = await res.json();

    if (json.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Restricción eliminada",
        timer: 1500,
        showConfirmButton: false,
      });
      fetchHours();
    } else {
      Swal.fire("Error", "No se pudo liberar el horario.", "error");
    }
  } catch {
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

// ─── Tabs ─────────────────────────────────────────────────
document.querySelectorAll(".tab").forEach((tab) => {
  tab.addEventListener("click", () => {
    document.querySelectorAll(".tab").forEach((t) => {
      t.classList.remove("active");
      t.setAttribute("aria-selected", "false");
    });
    tab.classList.add("active");
    tab.setAttribute("aria-selected", "true");

    currentServicio = Number(tab.dataset.servicio);
    fetchHours();
  });
});

// ─── Flatpickr ────────────────────────────────────────────
flatpickr("#datePicker", {
  disableMobile: true,
  defaultDate: new Date(
    currentDate.getFullYear(),
    currentDate.getMonth(),
    currentDate.getDate(),
  ),
  dateFormat: "Y-m-d",
  appendTo: document.body,
  positionElement: currentDateDisplay,
  position: "below",
  onChange([selected]) {
    if (!selected) return;
    currentDate = new Date(
      selected.getFullYear(),
      selected.getMonth(),
      selected.getDate(),
    );
    updateDateLabel();
    fetchHours();
  },
});

currentDateDisplay.addEventListener("click", () =>
  datePicker._flatpickr.open(),
);

// ─── Date buttons ─────────────────────────────────────────
const debouncedChange = debounce(changeDate, 300);
prevDayBtn.addEventListener("click", () => debouncedChange(-1));
nextDayBtn.addEventListener("click", () => debouncedChange(1));

// ─── Init ─────────────────────────────────────────────────
updateDateLabel();
fetchHours();
