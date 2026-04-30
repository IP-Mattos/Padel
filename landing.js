// ─── Utilities ────────────────────────────────────────────────────────────────

function formatDateName(dateStr) {
  const [year, month, day] = dateStr.split("-").map(Number);
  const date = new Date(year, month - 1, day);
  return `${date.getDate()} de ${date.toLocaleString("es-ES", {
    month: "long",
    timeZone: "America/Montevideo",
  })}`;
}

function formatLocalDateToYMD(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function debounce(fn, delay) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), delay);
  };
}

function getProfileImage(pic) {
  const DEFAULT_IMG = "./accion/imgPerfilUser/profile.png";
  const invalid =
    !pic ||
    !pic.trim() ||
    pic === "0" ||
    pic.toLowerCase() === "profile.png" ||
    pic.toLowerCase() === "default.png";
  return invalid ? DEFAULT_IMG : `./accion/imgPerfilUser/${pic}`;
}

// ─── Short day labels ──────────────────────────────────────────────────────────

const SHORT_DAYS = {
  lunes: "lun",
  martes: "mar",
  miercoles: "mié",
  jueves: "jue",
  viernes: "vie",
  sabado: "sáb",
  domingo: "dom",
};

// ─── DOMContentLoaded ──────────────────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => {
  // ── Loader ─────────────────────────────────────────────────────────────────
  const cover = document.getElementById("cover");
  const loader = document.getElementById("loader");
  document.body.style.overflow = "hidden";
  pushManager.init();
  window.onload = () => {
    loader.style.display = "none";
    cover.style.display = "none";
    document.body.style.overflow = "auto";
  };

  // ── Modal elements ─────────────────────────────────────────────────────────
  const cModal = document.getElementById("courtModal");
  const pModal = document.getElementById("profileModal");
  const caModal = document.getElementById("cantineModal");
  const tModal = document.getElementById("trainingModal");
  const trModal = document.getElementById("tournamentModal");
  const clModal = document.getElementById("classesModal");
  const rModal = document.getElementById("rivalsModal");
  const hModal = document.getElementById("hoursModal");
  const vModal = document.getElementById("versusModal");
  const iModal = document.getElementById("inviteModal");
  const aModal = document.getElementById("slotInviteModal");
  const ptModal = document.getElementById("pointsModal");
  const dModal = document.getElementById("deudaModal");

  // ── Modal open / close ─────────────────────────────────────────────────────
  function openModal(modal, contentClass) {
    modal.style.display = "flex";
    setTimeout(() => {
      const content = modal.querySelector(contentClass);
      if (content) setTimeout(() => content.classList.add("show"), 10);
    }, 10);
  }

  function closeModal(modal, contentClass) {
    modal.querySelector(contentClass)?.classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
      selectedCard = null;
    }, 350);
  }

  // Wire up all static modal triggers from a config table
  let selectedCard = null;

  const modalConfigs = [
    {
      modal: ptModal,
      contentClass: ".ptModal-content",
      openButtons: ["openPoints"],
      closeButtons: ["closePoints"],
    },
    {
      modal: pModal,
      contentClass: ".pModal-content",
      openButtons: ["openProfile", "openProfile2"],
      closeButtons: ["closeProfile"],
    },
    {
      modal: caModal,
      contentClass: ".caModal-content",
      openButtons: ["openCantine"],
      closeButtons: ["closeCantine"],
    },
    {
      modal: tModal,
      contentClass: ".tModal-content",
      openButtons: ["openTraining"],
      closeButtons: ["closeTraining"],
    },
    {
      modal: trModal,
      contentClass: ".trModal-content",
      openButtons: ["openTournament"],
      closeButtons: ["closeTournament"],
    },
    {
      modal: clModal,
      contentClass: ".clModal-content",
      openButtons: ["openClasses"],
      closeButtons: ["closeClasses"],
    },
    {
      modal: rModal,
      contentClass: ".rModal-content",
      openButtons: ["openRivals"],
      closeButtons: ["closeRivals"],
    },
    {
      modal: hModal,
      contentClass: ".hModal-content",
      openButtons: ["openHours"],
      closeButtons: ["closeHours"],
    },
    {
      modal: vModal,
      contentClass: ".vModal-content",
      openButtons: ["openVersus"],
      closeButtons: ["closeVersus"],
    },
    {
      modal: iModal,
      contentClass: ".iModal-content",
      openButtons: [],
      closeButtons: ["closeInvite"],
    },
    {
      modal: aModal,
      contentClass: ".aModal-content",
      openButtons: [],
      closeButtons: ["closePlayers"],
    },
    {
      modal: dModal,
      contentClass: ".dModal-content",
      openButtons: ["openDeuda"],
      closeButtons: ["closeDeuda"],
    },
    {
      modal: cModal,
      contentClass: ".cModal-content",
      openButtons: [],
      closeButtons: ["closeCourt"],
    },
  ];

  modalConfigs.forEach(({ modal, contentClass, openButtons, closeButtons }) => {
    openButtons.forEach((id) => {
      const btn = document.getElementById(id);
      if (btn) btn.onclick = () => openModal(modal, contentClass);
    });
    closeButtons.forEach((id) => {
      const btn = document.getElementById(id);
      if (btn) btn.onclick = () => closeModal(modal, contentClass);
    });
  });

  // ── Profile ────────────────────────────────────────────────────────────────
  document.getElementById("updateProfile").addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch("./accion/savePerfil.php", { method: "POST", body: formData })
      .then((r) => r.json())
      .then((data) => {
        const ok = data.consultaResponse.codigoError === "0";
        Swal.fire({
          title: ok ? "Éxito!" : "Error",
          text: data.consultaResponse.detalleError,
          icon: ok ? "success" : "error",
        });
      })
      .catch(() => Swal.fire("Oops", "Algo salió mal en el servidor", "error"));
  });

  document.getElementById("profileImgInput").addEventListener("change", () => {
    const file = document.getElementById("profileImgInput").files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append("imgPerfilUser", file);
    formData.append("idUser", userId);
    fetch("./accion/saveImgPerfil.php", { method: "POST", body: formData })
      .then((r) => r.json())
      .then((data) => {
        if (data.consultaResponse?.codigoError === "0") {
          const ts = Date.now();
          document.getElementById("profileImg").src =
            `./accion/imgPerfilUser/${data.consultaResponse.newImg}?t=${ts}`;
          Swal.fire(
            "Imagen actualizada",
            "Tu nueva foto de perfil se ha subido",
            "success",
          );
        } else {
          Swal.fire(
            "Error",
            data.consultaResponse?.detalleError || "No se pudo subir la imagen",
            "error",
          );
        }
      })
      .catch(() => Swal.fire("Oops", "Error al subir la imagen", "error"));
  });

  // ── Calendar helpers ───────────────────────────────────────────────────────
  const calendarUtils = {};
  const modalState = {};
  let selectedHourCard = null;

  function populateCalendarCards(
    containerDiv,
    calendarData,
    onDaySelected,
    servicio,
    preSelectedDate,
  ) {
    const today = new Date();
    let selectedCalCard = null;
    let selectedCardData = null;
    const fragment = document.createDocumentFragment();

    calendarData.forEach((dayInfo, index) => {
      let { dia, estado } = dayInfo;
      const actualDate = new Date(today);
      actualDate.setDate(today.getDate() + index);

      // Rivals always available today
      if (
        Number(servicio) === 4 &&
        formatLocalDateToYMD(today) === formatLocalDateToYMD(actualDate)
      ) {
        estado = 1;
      }

      const dayCard = document.createElement("div");
      dayCard.className = "card";
      const dayNumber = actualDate.getDate();
      const monthName = actualDate.toLocaleDateString("es-ES", {
        month: "short",
      });
      dayCard.innerHTML = `
        <span class="day">${SHORT_DAYS[dia]}</span>
        <span class="date">${dayNumber}</span>
        <span class="month">${monthName}</span>`;

      if (estado === 1) {
        dayCard.style.backgroundColor = "red";
        dayCard.style.opacity = "0.6";
        dayCard.style.pointerEvents = "none";
      } else {
        dayCard.addEventListener("click", () => {
          if (selectedCalCard) resetCardStyle(selectedCalCard);
          styleSelectedCard(dayCard);
          selectedCalCard = dayCard;
          selectedCardData = {
            ...dayInfo,
            dayNumber,
            monthName,
            date: formatLocalDateToYMD(actualDate),
          };
          onDaySelected({
            ...dayInfo,
            fecha: formatLocalDateToYMD(actualDate),
          });
        });

        const isTarget = preSelectedDate
          ? formatLocalDateToYMD(actualDate) === preSelectedDate
          : !selectedCalCard;

        if (isTarget && !selectedCalCard) {
          styleSelectedCard(dayCard);
          selectedCalCard = dayCard;
          selectedCardData = {
            ...dayInfo,
            dayNumber,
            monthName,
            date: formatLocalDateToYMD(actualDate),
          };
          onDaySelected({
            ...dayInfo,
            fecha: formatLocalDateToYMD(actualDate),
          });
        }
      }
      fragment.appendChild(dayCard);
    });

    containerDiv.appendChild(fragment);

    function styleSelectedCard(card) {
      card.style.backgroundColor = "var(--primary)";
      card.style.color = "black";
      card.querySelectorAll(".day,.date,.month").forEach((el) => {
        el.style.color = "black";
        el.style.textShadow = "none";
      });
    }

    function resetCardStyle(card) {
      card.style.backgroundColor = "";
      card.style.color = "";
      card.querySelectorAll(".day,.date,.month").forEach((el) => {
        el.style.color = "";
        el.style.textShadow = "";
      });
    }

    return { getSelectedCardData: () => selectedCardData };
  }

  // ── Hours ──────────────────────────────────────────────────────────────────
  async function fetchHours({ servicio, profe, fecha }, container) {
    selectedHourCard = null;
    container.innerHTML = "<p>Cargando horarios...</p>";
    try {
      const res = await fetch("./accion/getHorarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ servicio, profe, fecha }).toString(),
      });
      const json = await res.json();
      container.innerHTML = "";
      if (json.consultaResponse.codigoError === "0") {
        generateHourCards(container, json.consultaResponse.datos);
      } else {
        container.innerHTML = "<p>Error al cargar horarios</p>";
      }
    } catch (err) {
      console.error(err);
      container.innerHTML = "<p>Error de conexión</p>";
    }
  }

  function generateHourCards(container, hourData) {
    container.innerHTML = "";
    hourData.forEach(({ hora, estado }) => {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `<span class="hour">${hora}</span>`;
      if (estado !== 0) {
        card.style.backgroundColor = "red";
        card.style.opacity = "0.6";
        card.style.pointerEvents = "none";
      } else {
        card.addEventListener("click", () => {
          if (selectedHourCard) selectedHourCard.classList.remove("selected");
          card.classList.add("selected");
          selectedHourCard = card;
        });
      }
      container.appendChild(card);
    });
  }

  // ── Service modal map ──────────────────────────────────────────────────────
  const serviceModalMap = {
    court: {
      modal: cModal,
      content: ".cModal-content",
      servicios: [
        { id: 1, label: "Cancha 1" },
        { id: 6, label: "Cancha 2" },
      ],
      profe: 0,
      container: document.getElementById("court-calendar"),
      hoursContainer: document.getElementById("court-hs"),
      confirmButtonId: "acceptCourt",
    },
    classes: {
      modal: clModal,
      content: ".clModal-content",
      servicios: [{ id: 2, label: "Clases" }],
      profe: null,
      container: document.getElementById("classes-calendar"),
      hoursContainer: document.getElementById("class-hs"),
      confirmButtonId: "acceptClasses",
    },
    training: {
      modal: tModal,
      content: ".tModal-content",
      servicios: [{ id: 3, label: "Entrenamiento" }],
      profe: 0,
      container: document.getElementById("training-calendar"),
      hoursContainer: document.getElementById("training-hs"),
      confirmButtonId: "acceptTraining",
    },
    rivals: {
      modal: rModal,
      content: ".rModal-content",
      servicios: [{ id: 4, label: "Partido" }],
      profe: 0,
      container: document.getElementById("rivals-calendar"),
      hoursContainer: document.getElementById("rivals-hs"),
      confirmButtonId: "acceptRivals",
    },
  };

  // ── Service switch ─────────────────────────────────────────────────────────
  function renderServiceSwitch(container, services, onChange) {
    container.innerHTML = "";
    services.forEach((s, index) => {
      const btn = document.createElement("button");
      btn.textContent = s.label;
      btn.className = index === 0 ? "active" : "";
      btn.onclick = () => {
        container
          .querySelectorAll("button")
          .forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
        onChange(s.id);
      };
      container.appendChild(btn);
    });
  }

  // ── Load calendar ──────────────────────────────────────────────────────────
  async function loadCalendarForService(key, servicio) {
    const { profe, container, hoursContainer } = serviceModalMap[key];
    container.innerHTML = "<p>Cargando...</p>";
    hoursContainer.innerHTML = "";
    selectedHourCard = null;

    try {
      const res = await fetch("./accion/getDias.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ servicio, profe }).toString(),
      });
      const json = await res.json();
      container.innerHTML = "";
      if (json.consultaResponse.codigoError === "0") {
        calendarUtils[key] = populateCalendarCards(
          container,
          json.consultaResponse.datos,
          (selectedDay) => {
            modalState[key].selectedDate = selectedDay.fecha;
            fetchHours({ ...selectedDay, servicio, profe }, hoursContainer);
          },
          servicio,
          modalState[key]?.selectedDate,
        );
      } else {
        container.innerHTML = "<p>Error al cargar los días</p>";
      }
    } catch (err) {
      console.error(err);
      container.innerHTML = "<p>Error de conexión</p>";
    }
  }

  // ── Open modals + service switch ───────────────────────────────────────────
  Object.entries(serviceModalMap).forEach(([key, config]) => {
    const openBtn = document.getElementById(`open${capitalize(key)}`);
    if (!openBtn) return;

    openBtn.onclick = async () => {
      openModal(config.modal, config.content);
      const firstService = config.servicios[0];
      modalState[key] = { servicio: firstService.id };

      const switchEl = config.modal.querySelector(".service-switch");
      if (config.servicios.length > 1 && switchEl) {
        renderServiceSwitch(switchEl, config.servicios, (newServicio) => {
          modalState[key].servicio = newServicio;
          loadCalendarForService(key, newServicio);
        });
      }

      await loadCalendarForService(key, firstService.id);
    };
  });

  // ── Reservations ───────────────────────────────────────────────────────────
  Object.entries(serviceModalMap).forEach(([key, config]) => {
    const btn = document.getElementById(config.confirmButtonId);
    if (!btn) return;

    btn.addEventListener("click", async () => {
      const selectedDay = calendarUtils[key]?.getSelectedCardData();
      if (!selectedDay)
        return Swal.fire("Error", "Por favor seleccione un día", "error");
      if (!selectedHourCard)
        return Swal.fire("Error", "Por favor selecciona una hora.", "error");

      const hora = selectedHourCard.querySelector(".hour").textContent;
      const formattedDate = selectedDay.date;

      const confirmed = await Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Reservar para el ${formatDateName(formattedDate)} a las ${hora}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, reservar",
        cancelButtonText: "Cancelar",
      });
      if (!confirmed.isConfirmed) return;

      try {
        const res = await fetch("../accion/putReserv.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            fecha: formattedDate,
            servicio: modalState[key].servicio,
            profe: config.profe,
            usuario: userId,
            hora,
          }).toString(),
        });
        const json = await res.json();
        if (json.consultaResponse?.codigoError === "0") {
          Swal.fire("Éxito", "Reserva realizada con éxito", "success");
          closeModal(config.modal, config.content);
        } else {
          Swal.fire("Error", json.consultaResponse.detalleError, "error");
        }
      } catch (err) {
        console.error(err);
        Swal.fire("Error", "Error de conexión al enviar la reserva.", "error");
      }
    });
  });

  // ── Classes – professor list ───────────────────────────────────────────────
  let selectedProfCard = null;

  document.getElementById("openClasses").addEventListener("click", async () => {
    const profListContainer = document.getElementById("profList");
    const config = serviceModalMap.classes;

    profListContainer.innerHTML = "<p>Cargando profesores...</p>";
    try {
      const res = await fetch("./accion/getProfesores.php");
      const data = await res.json();

      if (data.consultaResponse.codigoError !== "0") {
        profListContainer.innerHTML = "<p>Error al cargar profesores</p>";
        return;
      }

      const profesores = data.consultaResponse.datos;
      profListContainer.innerHTML = "";
      let firstCard = null;

      profesores.forEach((prof, index) => {
        const card = document.createElement("div");
        card.className = "profCard";
        card.innerHTML = `<img src="./accion/imgPerfilUser/${prof.imgperfil}" alt="${prof.nombre}" /><h3>${prof.nombre}</h3>`;

        let isFetching = false;
        card.addEventListener("click", async () => {
          if (isFetching) return;
          isFetching = true;
          if (selectedProfCard)
            selectedProfCard.classList.remove("selected-teacher");
          card.classList.add("selected-teacher");
          selectedProfCard = card;

          try {
            await handleProfessorSelection(prof.id, {
              ...config,
              container: document.getElementById("classes-calendar"),
              hoursContainer: document.getElementById("class-hs"),
            });
            document.getElementById("interested").onclick = () => {
              Swal.fire({
                title: "Genial!",
                html: `<p>Ponte en contacto con nuestro profesor por este número: ${prof.celular.replace(598, 0)}<img src='/img/whatsapp.png'></p>`,
                icon: "success",
              });
            };
          } finally {
            isFetching = false;
          }
        });

        profListContainer.appendChild(card);
        if (index === 0) firstCard = card;
      });

      firstCard?.click();
    } catch (err) {
      console.error("Error al cargar profesores", err);
      profListContainer.innerHTML = "<p>Error al cargar profesores</p>";
    }
  });

  async function handleProfessorSelection(
    profeId,
    { servicios, container, hoursContainer },
  ) {
    container.innerHTML = "<p>Cargando días</p>";
    hoursContainer.innerHTML = "";
    try {
      const res = await fetch("./accion/getDias.php", {
        method: "POST",
        headers: { "content-type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          servicio: servicios,
          profe: profeId,
        }).toString(),
      });
      const json = await res.json();
      container.innerHTML = "";
      if (json.consultaResponse.codigoError === "0") {
        calendarUtils.classes = populateCalendarCards(
          container,
          json.consultaResponse.datos,
          (selectedDay) =>
            fetchHours(
              { ...selectedDay, servicios, profe: profeId },
              hoursContainer,
            ),
          servicios,
        );
      } else {
        container.innerHTML = "<p>Error al cargar los días</p>";
      }
    } catch (err) {
      container.innerHTML = "<p>Error de conexión";
      console.error(err);
    }
  }

  // ── User reservations ──────────────────────────────────────────────────────
  const reserveContainer = document.getElementById("reserveContainer");
  let allReservations = [];

  const SERVICE_IMAGES = {
    1: "./img/resCancha.png",
    2: "./img/resClases.png",
    3: "./img/resEntrenar.png",
    4: "./img/resRivales.png",
    5: "./img/resChelada.png",
    6: "./img/resCancha.png",
  };

  async function fetchProfile(userIdToFetch) {
    const res = await fetch("./accion/getPerfil.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ idPerfil: userIdToFetch }).toString(),
    });
    const js = await res.json();
    if (js.consultaResponse?.codigoError === "0") {
      return {
        name: js.consultaResponse.nombre,
        img: `./accion/imgPerfilUser/${js.consultaResponse.imgperfil}`,
        id: userIdToFetch,
      };
    }
    return null;
  }

  async function loadUserReservations() {
    reserveContainer.innerHTML = "<p>Cargando tus reservas...</p>";

    const now = new Date();
    const sevenDays = new Date(now);
    sevenDays.setDate(now.getDate() + 7);

    const fmt = (d) => {
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, "0");
      const day = String(d.getDate()).padStart(2, "0");
      return `${y}/${m}/${day}`;
    };

    try {
      const res = await fetch("./accion/getHorasUser.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          fechaDesde: fmt(now),
          fechaHasta: fmt(sevenDays),
          idUser: userId,
        }).toString(),
      });
      const json = await res.json();

      if (json.consultaResponse?.codigoError !== "0") {
        reserveContainer.innerHTML = "<p>No tiene horas reservadas.</p>";
        return;
      }

      const datos = json.consultaResponse.datos;
      allReservations = datos;
      reserveContainer.innerHTML = "";

      for (const item of datos) {
        const card = document.createElement("div");
        card.className = "rCard";

        const [y, mo, d] = item.fecha.split("-").map(Number);
        const itemDate = new Date(y, mo - 1, d);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (itemDate < today) {
          card.style.backgroundColor = "#e0e0e0";
          card.style.color = "black";
        } else if (itemDate.getTime() === today.getTime()) {
          card.style.backgroundColor = "#003266";
          card.style.color = "var(--primary)";
          card.style.boxShadow = "0 0 20px var(--primary)";
        } else {
          card.style.backgroundColor = "#003266";
        }

        let displayName, displayImage, secondaryImage;

        if (item.servicio == 4) {
          const rivalId =
            item.idUserRival != 0 && item.idUserRival != userId
              ? item.idUserRival
              : item.idUserRival == userId
                ? item.idUsuario
                : null;
          if (rivalId) {
            const profile = await fetchProfile(rivalId);
            displayName = profile?.name || "";
            displayImage = profile?.img || SERVICE_IMAGES[4];
            secondaryImage = "./img/vs.png";
          } else {
            displayImage = SERVICE_IMAGES[4];
          }
        } else {
          displayImage = SERVICE_IMAGES[item.servicio] || "images/default.png";
        }

        const canchaP = document.createElement("p");
        canchaP.innerHTML = `<strong>${item.nombreServicio}</strong>`;

        const dateP = document.createElement("p");
        dateP.innerHTML = `<strong>${formatDateName(item.fecha)}</strong>`;

        const hourP = document.createElement("p");
        hourP.innerHTML = `<strong>${item.hora.split(":").slice(0, 2).join(":")}</strong>`;

        const imageWrapper = document.createElement("div");
        imageWrapper.className = "image-wrapper";

        if (secondaryImage) {
          const img2 = document.createElement("img");
          img2.src = secondaryImage;
          img2.className = "versus-icon";
          imageWrapper.appendChild(img2);
        }

        const img = document.createElement("img");
        img.src = displayImage;
        img.className = "service-icon";
        imageWrapper.appendChild(img);

        card.append(canchaP, dateP, imageWrapper);

        if (displayName) {
          const nameP = document.createElement("p");
          nameP.textContent = displayName;
          nameP.className = "rival-name";
          card.appendChild(nameP);
        }

        card.appendChild(hourP);

        const invitedIds = [
          item.idUsuario,
          item.invitado1,
          item.invitado2,
          item.invitado3,
        ].filter((id) => id !== "0" && id !== "");
        const playerImg = document.createElement("img");
        playerImg.src = `./img/${invitedIds.length}players.png`;
        playerImg.className = "players";
        card.appendChild(playerImg);

        // Cancel button (only within 1 hour of reservation creation)
        if (item.timeEstado) {
          const estadoDate = new Date(item.timeEstado.replace(" ", "T"));
          const nowMs = Date.now();
          if (
            nowMs >= estadoDate.getTime() &&
            nowMs <= estadoDate.getTime() + 3_600_000
          ) {
            const cancelButton = document.createElement("button");
            cancelButton.className = "cancel-button";
            cancelButton.dataset.id = item.id;
            cancelButton.innerHTML =
              "<img style='width:16px;' src='./img/cancelar.png'>";
            cancelButton.addEventListener("click", async () => {
              const id = cancelButton.dataset.id;
              const confirm = await Swal.fire({
                title: "¿Cancelar reserva?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cancelar",
                cancelButtonText: "No, volver",
                reverseButtons: true,
              });
              if (!confirm.isConfirmed) return;

              try {
                const isInvited = item.idUsuario != userId;
                const url = isInvited
                  ? "./accion/putCancelInvitado.php"
                  : "./accion/putReservCancel.php";
                const body = isInvited
                  ? new URLSearchParams({ idReserva: id, idInvitado: userId })
                  : new URLSearchParams({ idReserv: id });

                const res = await fetch(url, {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                  },
                  body: body.toString(),
                });
                const result = await res.json();

                if (result.consultaResponse?.codigoError === "0") {
                  Swal.fire({
                    title: "Cancelado",
                    text: "Tu reserva ha sido cancelada.",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                  });
                  await loadUserReservations();
                } else {
                  Swal.fire(
                    "Error",
                    result.consultaResponse.detalleError,
                    "error",
                  );
                }
              } catch (err) {
                console.error(err);
                Swal.fire(
                  "Error",
                  "Hubo un problema al conectar con el servidor.",
                  "error",
                );
              }
            });
            card.appendChild(cancelButton);
          }
        }

        reserveContainer.appendChild(card);
        card.addEventListener("click", (e) => {
          if (e.target.closest(".cancel-button")) return;
          debouncedOpenInviteModal(item);
        });
      }
    } catch (error) {
      console.error("Error de conexion:", error);
    }
  }

  document
    .getElementById("openHours")
    .addEventListener("click", loadUserReservations);

  // ── Invite modal ───────────────────────────────────────────────────────────
  let allReservationsRef = allReservations;
  let selectedReservationId = null;
  let selectedInviteUser = null;

  const debouncedOpenInviteModal = debounce(openInviteModal, 300);

  async function updateInviteResults(input, invitedIds) {
    const resultsDiv = document.getElementById("inviteResults");
    resultsDiv.innerHTML = "";
    selectedInviteUser = null;
    document.getElementById("addInviteBtn").disabled = true;

    if (!input || input.trim().length < 2) return;

    const res = await fetch("./accion/getPerfiles.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ filtroPerfil: input.trim() }).toString(),
    });
    const data = await res.json();

    if (data.consultaResponse?.codigoError === "0") {
      const options = data.consultaResponse.registros.filter(
        (p) => !invitedIds.includes(p.id) && p.id !== userId.toString(),
      );

      if (options.length === 0) {
        resultsDiv.innerHTML =
          "<p class='no-results'>No se encontraron usuarios.</p>";
        return;
      }

      options.forEach((p) => {
        const item = document.createElement("div");
        item.className = "search-result-item";
        item.innerHTML = `
          <img src="${getProfileImage(p.imgperfil)}" class="invite-profile-img" alt="${p.nombre}"
               onerror="this.src='./accion/imgPerfilUser/profile.png'">
          <span>${p.nombre}${p.usuario ? ` (${p.usuario})` : ""}</span>`;
        item.addEventListener("click", () => {
          resultsDiv
            .querySelectorAll(".search-result-item")
            .forEach((i) => i.classList.remove("selected"));
          item.classList.add("selected");
          selectedInviteUser = {
            id: p.id,
            nombre: p.nombre,
            imgperfil: p.imgperfil,
          };
          document.getElementById("addInviteBtn").disabled = false;
        });
        resultsDiv.appendChild(item);
      });
    }
  }

  async function openSlotInviteModal(reservationId) {
    const reservation = allReservations.find((r) => r.id === reservationId);
    const invitedIds = [
      reservation.idUsuario,
      reservation.invitado1,
      reservation.invitado2,
      reservation.invitado3,
    ];

    const searchInput = document.getElementById("inviteSearch");
    searchInput.value = "";
    document.getElementById("inviteResults").innerHTML = "";
    selectedInviteUser = null;
    document.getElementById("addInviteBtn").disabled = true;

    const debouncedSearch = debounce(
      (e) => updateInviteResults(e.target.value, invitedIds),
      400,
    );
    searchInput.removeEventListener("input", searchInput._debouncedSearch);
    searchInput._debouncedSearch = debouncedSearch;
    searchInput.addEventListener("input", debouncedSearch);

    document.getElementById("addInviteBtn").onclick = async () => {
      if (!selectedInviteUser) return;
      const res = await fetch("./accion/putConfirmInvitados.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          idReserva: reservationId,
          idInvitado: selectedInviteUser.id,
        }).toString(),
      });
      const result = await res.json();
      if (result.consultaResponse?.codigoError === "0") {
        Swal.fire({
          title: "Invitado agregado",
          icon: "success",
          timer: 1500,
          showConfirmButton: false,
        });
        closeModal(aModal, ".aModal-content");
        closeModal(iModal, ".iModal-content");
        await loadUserReservations();
        const updated = allReservations.find((r) => r.id === reservationId);
        if (updated) debouncedOpenInviteModal(updated);
      } else {
        Swal.fire("Error", result.consultaResponse.detalleError, "error");
      }
    };

    openModal(aModal, ".aModal-content");
  }

  async function openInviteModal(reservation) {
    selectedReservationId = reservation.id;
    const canInvite =
      reservation.idUsuario == userId || reservation.idUserRival == userId;
    const invitedIds = [
      reservation.idUsuario,
      reservation.invitado1,
      reservation.invitado2,
      reservation.invitado3,
    ];

    const uniqueSlots = [];
    const seen = new Set();
    for (const id of invitedIds) {
      if (id === "0" || id === "") {
        uniqueSlots.push("0");
      } else if (!seen.has(id)) {
        uniqueSlots.push(id);
        seen.add(id);
      }
    }

    const inviteListUl = document.getElementById("inviteListUl");
    inviteListUl.innerHTML = "";

    for (const id of uniqueSlots) {
      const li = document.createElement("li");
      li.className = "invite-list-item";
      const img = document.createElement("img");
      const span = document.createElement("span");

      if (id === "0") {
        img.src = "./img/emptySlot.png";
        img.className = "invite-profile-img";
        span.textContent = "lugar vacío";
        if (canInvite) {
          li.classList.add("clickable-slot");
          li.onclick = () => openSlotInviteModal(selectedReservationId);
        }
      } else {
        const profile = await fetchProfile(id);
        img.src = profile.img || "./img/profile.png";
        img.onerror = function () {
          this.src = "./img/profile.png";
        };
        img.className = "invite-profile-img";
        let label = profile.name;
        if (id === reservation.idUsuario.toString()) label += " (Organizador)";
        else if (id === userId.toString()) label += " (Tú)";
        span.textContent = label;
      }

      li.append(img, span);
      inviteListUl.appendChild(li);
    }

    openModal(iModal, ".iModal-content");
  }

  // ── Versus modal ───────────────────────────────────────────────────────────
  const versusContainer = document.getElementById("match-container");

  function isCancelable(timeEstado) {
    if (!timeEstado) return false;
    const t = new Date(timeEstado.replace(" ", "T")).getTime();
    const n = Date.now();
    return n >= t && n <= t + 3_600_000;
  }

  function checkDate(fechaStr) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const [y, m, d] = fechaStr.split("-");
    return new Date(y, m - 1, d) >= today;
  }

  function fetchVersusData() {
    fetch("./accion/getHsVs.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ idUser: userId, estado: 1 }).toString(),
    })
      .then((r) => r.json())
      .then((data) => {
        const consulta = data.consultaResponse || {};
        const datos = Array.isArray(consulta.datos) ? consulta.datos : [];
        const partidoActivo = consulta.partidoActivo === true;

        document.getElementById("versusIcon").src = datos.some((item) =>
          checkDate(item.fecha),
        )
          ? "./img/vs.gif"
          : "./img/vs_.gif";
        document.getElementById("partidoIcon").src = partidoActivo
          ? "./img/reserva.gif"
          : "./img/reserva_.gif";
      })
      .catch(console.error);
  }

  fetchVersusData();
  setInterval(fetchVersusData, 5000);

  document.getElementById("openVersus").addEventListener("click", async () => {
    try {
      const res = await fetch("./accion/getHsVs.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ idUser: userId, estado: 1 }).toString(),
      });
      const json = await res.json();

      if (json.consultaResponse?.codigoError !== "0") {
        versusContainer.innerHTML = "No hay partidos disponibles.";
        return;
      }

      versusContainer.innerHTML = "";

      for (const item of json.consultaResponse.datos) {
        const { idUsuario, fecha, hora, idReserva } = item;

        const perfilRes = await fetch("./accion/getPerfil.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ idPerfil: idUsuario }).toString(),
        });
        const perfil = (await perfilRes.json()).consultaResponse;

        const card = document.createElement("div");
        card.className = "vCard";
        card.innerHTML = `
          <div class="user-info">
            <img src="./accion/imgPerfilUser/${perfil.imgperfil}" alt="${perfil.nombre}" class="profile-img" />
            <div>
              <p><strong>${perfil.nombre}</strong></p>
              <p>${formatDateName(fecha)} - ${hora.split(":").slice(0, 2).join(":")}</p>
            </div>
          </div>`;

        const button = document.createElement("button");

        if (String(idUsuario) === String(userId)) {
          if (isCancelable(item.timeEstado)) {
            button.innerHTML = `<img src="./img/cancelar.png" alt="Cancelar" class="btn-icon" />`;
            button.className = "cancel-btn";
            button.addEventListener("click", async () => {
              const confirm = await Swal.fire({
                title: "¿Cancelar reserva?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cancelar",
                cancelButtonText: "No",
              });
              if (!confirm.isConfirmed) return;
              const res = await fetch("./accion/putReservCancel.php", {
                method: "POST",
                headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({ idReserv: idReserva }).toString(),
              });
              const json = await res.json();
              if (json.consultaResponse?.codigoError === "0") {
                Swal.fire(
                  "Cancelado",
                  "La reserva ha sido cancelada.",
                  "success",
                );
                card.remove();
                closeModal(vModal, ".vModal-content");
              } else {
                Swal.fire("Error", json.consultaResponse.detalleError, "error");
              }
            });
            card.appendChild(button);
          }
        } else {
          button.innerHTML = `<img src="./img/confirmar.png" alt="Confirmar" class="btn-icon" />`;
          button.className = "confirm-btn";
          button.addEventListener("click", async () => {
            const { value: message } = await Swal.fire({
              title: "¿Confirmar participación?",
              input: "text",
              inputLabel: "Escribe un mensaje (opcional)",
              showCancelButton: true,
              confirmButtonText: "Confirmar",
              cancelButtonText: "Cancelar",
            });
            if (message === undefined) return;
            await fetch("./accion/putConfirmVS.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: new URLSearchParams({
                idReserva,
                idRival: userId,
                mensaje: message,
              }).toString(),
            });
            Swal.fire(
              "Confirmado",
              "Tu participación ha sido confirmada.",
              "success",
            );
            card.remove();
            closeModal(vModal, ".vModal-content");
          });
          card.appendChild(button);
        }

        versusContainer.appendChild(card);
      }
    } catch (error) {
      console.error(error);
      Swal.fire(
        "Error",
        "Ocurrió un error al conectar con el servidor.",
        "error",
      );
    }
  });

  // ── Tournaments ────────────────────────────────────────────────────────────
  let selectedTorneoId = null;
  let selectedTorneoCategoria = null;

  // Show tournament button if active tournaments exist
  fetch("./accion/getTorneos.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "estado=1",
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.consultaResponse?.torneos?.length > 0) {
        document.getElementById("openTournament").classList.remove("hidden");
        document.getElementById("tournamentTitle").classList.remove("hidden");
      }
    })
    .catch(console.error);

  async function loadTorneos() {
    const cardsContainer = document.getElementById("tournamentCards");
    const confirmBtn = document.getElementById("acceptTournament");
    cardsContainer.innerHTML = "<p>Cargando torneos...</p>";
    confirmBtn.disabled = true;
    selectedTorneoId = selectedTorneoCategoria = null;

    try {
      const res = await fetch("./accion/getTorneos.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "estado=1",
      });
      const data = await res.json();
      const torneos = data.consultaResponse?.torneos ?? [];

      if (torneos.length === 0) {
        cardsContainer.innerHTML = "<p>No hay torneos disponibles.</p>";
        return;
      }

      cardsContainer.innerHTML = "";
      torneos.forEach((torneo) => {
        const [y, m, d] = torneo.fecha.split("-").map(Number);
        const fechaStr = new Date(y, m - 1, d).toLocaleDateString("es-ES", {
          day: "2-digit",
          month: "long",
          year: "numeric",
          timeZone: "America/Montevideo",
        });

        const card = document.createElement("div");
        card.className = "tCard";
        card.innerHTML = `
          <h3>${torneo.nombre}</h3>
          <p>📅 ${fechaStr}</p>
          <p>🏆 Categoría <strong>${torneo.categoria}</strong></p>
          <p>💰 Entrada: <strong>$ ${Number(torneo.entre).toLocaleString("es-UY")}</strong></p>`;
        card.addEventListener("click", () => {
          cardsContainer
            .querySelectorAll(".tCard")
            .forEach((c) => c.classList.remove("selected"));
          card.classList.add("selected");
          selectedTorneoId = torneo.id;
          selectedTorneoCategoria = torneo.categoria;
          confirmBtn.disabled = false;
        });
        cardsContainer.appendChild(card);
      });
    } catch (err) {
      console.error(err);
      cardsContainer.innerHTML = "<p>Error al cargar torneos.</p>";
    }
  }

  document
    .getElementById("openTournament")
    .addEventListener("click", loadTorneos);

  document
    .getElementById("acceptTournament")
    .addEventListener("click", async () => {
      if (!selectedTorneoId) return;

      if (String(userCategoria) !== String(selectedTorneoCategoria)) {
        const { isConfirmed } = await Swal.fire({
          icon: "warning",
          title: "Categoría diferente",
          html: `Este torneo es de categoría <strong>${selectedTorneoCategoria}</strong> y tu categoría es <strong>${userCategoria}</strong>.<br>¿Deseas inscribirte igual?`,
          showCancelButton: true,
          confirmButtonText: "Sí, inscribirme",
          cancelButtonText: "Cancelar",
        });
        if (!isConfirmed) return;
      }

      try {
        const res = await fetch("./accion/putTorneoAspirante.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            idTorneo: selectedTorneoId,
            idUsuario: userId,
          }).toString(),
        });
        const json = await res.json();
        if (json.consultaResponse?.codigoError === "0") {
          Swal.fire({
            icon: "success",
            title: "¡Inscripto!",
            text:
              json.consultaResponse.detalleError ||
              "Te has inscripto en el torneo.",
            timer: 2500,
            showConfirmButton: false,
          });
          closeModal(trModal, ".trModal-content");
        } else {
          Swal.fire(
            "Error",
            json.consultaResponse?.detalleError || "No se pudo inscribir.",
            "error",
          );
        }
      } catch (err) {
        console.error(err);
        Swal.fire("Error", "Error de conexión.", "error");
      }
    });

  // ── Deuda modal ────────────────────────────────────────────────────────────
  document
    .getElementById("openDeuda")
    ?.addEventListener("click", loadDeudaMovimientos);

  async function loadDeudaMovimientos() {
    const container = document.getElementById("deudaMovimientos");
    container.innerHTML = "<p>Cargando movimientos...</p>";
    try {
      const res = await fetch("./accion/getDeudaMovimientos.php");
      const data = await res.json();
      if (data.consultaResponse?.codigoError !== "0") {
        container.innerHTML = "<p>Error al cargar movimientos.</p>";
        return;
      }

      const movs = data.consultaResponse.movimientos;
      if (!movs.length) {
        container.innerHTML = "<p>Sin movimientos registrados.</p>";
        return;
      }

      const saldoFinal = movs.reduce(
        (acc, m) => acc + Number(m.debe) - Number(m.haber),
        0,
      );
      const fmtMoney = (n) =>
        `$ ${Number(n).toLocaleString("es-UY", { minimumFractionDigits: 2 })}`;

      container.innerHTML = `
        <div class="deuda-summary">
          <span>Saldo total</span>
          <span class="deuda-total">${fmtMoney(saldoFinal)}</span>
        </div>
        <table class="deuda-table">
          <thead><tr><th>Fecha</th><th>Origen</th><th>Debe</th><th>Haber</th></tr></thead>
          <tbody>
            ${movs
              .map(
                (m) => `
              <tr>
                <td>${m.fecha.slice(0, 10)}</td>
                <td><span class="deuda-badge ${m.tipo === "Chelada" ? "badge-chelada" : "badge-gopadel"}">${m.tipo}</span></td>
                <td class="${Number(m.debe) > 0 ? "deuda-debe" : ""}">${Number(m.debe) > 0 ? fmtMoney(m.debe) : "—"}</td>
                <td class="${Number(m.haber) > 0 ? "deuda-haber" : ""}">${Number(m.haber) > 0 ? fmtMoney(m.haber) : "—"}</td>
              </tr>`,
              )
              .join("")}
          </tbody>
        </table>`;
    } catch (err) {
      console.error(err);
      container.innerHTML = "<p>Error de conexión.</p>";
    }
  }

  function updateDeudaButton() {
    fetch("./accion/getDeudaUsuario.php")
      .then((r) => r.json())
      .then((data) => {
        if (data.consultaResponse?.codigoError !== "0") return;
        const saldo = Number(data.consultaResponse.saldo);
        const btn = document.getElementById("openDeuda");
        const points = document.getElementById("openPoints");
        if (saldo != 0) {
          btn.textContent = `Deuda $ ${saldo.toLocaleString("es-UY", { minimumFractionDigits: 2 })}`;
          btn.style.display = "";
          points.style.display = "none";
        } else {
          btn.style.display = "none";
          points.style.display = "";
        }
      })
      .catch(console.error);
  }

  updateDeudaButton();
  setInterval(updateDeudaButton, 60_000);

  // ── Points modal ───────────────────────────────────────────────────────────
  let puntosDisponibles = 0;

  document.getElementById("openPoints").addEventListener("click", () => {
    fetch("./accion/getPerfil.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `idPerfil=${encodeURIComponent(userId)}`,
    })
      .then((r) => r.json())
      .then((data) => {
        puntosDisponibles = parseInt(data.consultaResponse.puntos, 10);
        document.getElementById("puntosValue").innerHTML = `
          <h2>Tienes</h2>
          <span style="color:yellow;font-size:2rem;">${data.consultaResponse.puntos}</span><br>
          <h2>puntos para canjear...!!!</h2>`;
      })
      .catch(console.error);
  });

  document.getElementById("sendPoints").addEventListener("click", () => {
    const puntos = Number(document.getElementById("puntosInput").value);
    if (isNaN(puntos) || puntos <= 0) {
      return Swal.fire({
        title: "Error",
        text: "Ingresa un número válido de puntos.",
        icon: "error",
      });
    }
    if (puntos > puntosDisponibles) {
      return Swal.fire({
        title: "Error",
        text: `No puedes canjear más de ${puntosDisponibles} puntos.`,
        icon: "error",
      });
    }
    fetch("./accion/putCanje.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `puntos=${encodeURIComponent(puntos)}`,
    })
      .then((r) => r.json())
      .then((data) => {
        const ok = data.consultaResponse.codigoError == "0";
        Swal.fire({
          title: ok ? "Éxito!" : "Error",
          text: data.consultaResponse.detalleError,
          icon: ok ? "success" : "error",
        });
      });
  });

  // ── Input sanitizer ────────────────────────────────────────────────────────
  document.getElementById("puntosInput").addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
});

// ─── Admin ─────────────────────────────────────────────────────────────────────

const adminAccessBtn = document.getElementById("admin-access");

if (adminAccessBtn) {
  adminAccessBtn.addEventListener("click", () => {
    const styleAdminSwalButtons = () => {
      const popup = document.querySelector(".swal2-popup");
      if (!popup) return;

      const commonButtons = popup.querySelectorAll(
        ".swal2-actions .swal2-styled",
      );

      commonButtons.forEach((btn) => {
        btn.style.minWidth = "220px";
        btn.style.height = "44px";
        btn.style.display = "inline-flex";
        btn.style.alignItems = "center";
        btn.style.justifyContent = "center";
        btn.style.borderRadius = "10px";
        btn.style.fontWeight = "600";
        btn.style.fontSize = "16px";
        btn.style.margin = "6px";
        btn.style.color = "#fff";
      });
    };
    Swal.fire({
      title: "¿Qué desea administrar?",
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: "Reservas de jugadores",
      denyButtonText: "Restringir horarios",
      cancelButtonText: "Administrar canjes",
      didOpen: () => {
        const actions = document.querySelector(".swal2-actions");

        const extraButtons = [
          {
            id: "swal-deuda-btn",
            text: "Administrar deudas",
            color: "#6c757d",
            href: "/deuda.php",
          },
          {
            id: "swal-torneo-btn",
            text: "Administrar torneos",
            color: "#6c757d",
            href: "/torneo.php",
          },
          {
            id: "swal-cierre-btn",
            text: "Cierre de caja",
            color: "#1a6b3c",
            fn: abrirCierreCaja,
          },
        ];

        extraButtons.forEach(({ id, text, color, href, fn }) => {
          if (document.getElementById(id)) return;
          const btn = document.createElement("button");
          btn.id = id;
          btn.className = "swal2-styled";
          btn.textContent = text;
          btn.style.cssText = `background-color:${color};min-width:220px;height:44px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;font-weight:600;font-size:16px;margin:6px;color:#fff;`;
          btn.onclick = () => {
            Swal.close();
            href ? (window.location.href = href) : fn();
          };
          actions?.appendChild(btn);
        });
        styleAdminSwalButtons();
      },
    }).then((result) => {
      if (result.isConfirmed) window.location.href = "/admin.php";
      else if (result.isDenied) window.location.href = "/restrict.php";
      else if (result.dismiss === Swal.DismissReason.cancel)
        window.location.href = "/exchange.php";
    });
  });
}

// ─── Cierre de caja ────────────────────────────────────────────────────────────

async function abrirCierreCaja() {
  Swal.fire({
    title: "Calculando cierre...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  try {
    const res = await fetch("./accion/getCierreCaja.php");
    const data = await res.json();

    if (data.error) {
      Swal.fire("Error", data.error, "error");
      return;
    }

    if (data.pendientes > 0) {
      Swal.fire({
        icon: "warning",
        title: "No se puede cerrar",
        html: `Hay <strong>${data.pendientes}</strong> hora(s) sin confirmar o sin medio de pago en el período.<br>Por favor resuelva los pendientes antes de realizar el cierre.`,
      });
      return;
    }

    const fmt = (n) =>
      `$ ${Number(n).toLocaleString("es-UY", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    const t = data.totales;

    const tableHtml = `
      <p style="font-size:0.82rem;color:#9ca3af;margin-bottom:14px;text-align:left;">
        Período: <strong>${formatFechaCierre(data.fechaDesde)}</strong> → <strong>${formatFechaCierre(data.fechaHasta)}</strong>
      </p>
      <table style="width:100%;border-collapse:collapse;font-size:0.92rem;text-align:left;">
        <thead>
          <tr style="background:#0d2137;color:#90caf9;">
            <th style="padding:9px 12px;">Forma de pago</th>
            <th style="padding:9px 12px;text-align:right;">Alquileres</th>
            <th style="padding:9px 12px;text-align:right;">Cobros deuda</th>
            <th style="padding:9px 12px;text-align:right;">Total</th>
          </tr>
        </thead>
        <tbody>
          ${buildRowCierre("Efectivo", "EFECTIVO", data, fmt)}
          ${buildRowCierre("Transferencia", "TRANS", data, fmt)}
          ${buildRowCierre("Mercado Pago", "MERCPAGO", data, fmt)}
          ${buildRowCierre("Débito", "DEBITO", data, fmt)}
        </tbody>
        <tfoot>
          <tr style="background:#061425;font-weight:700;color:#4fc3f7;">
            <td style="padding:11px 12px;border-top:2px solid #1e3a5f;" colspan="3">TOTAL</td>
            <td style="padding:11px 12px;border-top:2px solid #1e3a5f;text-align:right;">${fmt(t.TOTAL)}</td>
          </tr>
        </tfoot>
      </table>
      <div style="margin-top:16px;text-align:left;">
        <label style="font-size:0.82rem;color:#9ca3af;">Observaciones (opcional)</label>
        <textarea id="cc-obs"
          style="width:100%;margin-top:6px;padding:8px 10px;background:#0d1b2a;border:1px solid #1e3a5f;border-radius:6px;color:#e8eaf0;font-size:0.88rem;resize:vertical;"
          rows="2" placeholder="Notas del operador..."></textarea>
      </div>`;

    const { isConfirmed } = await Swal.fire({
      title: "Cierre de caja",
      html: tableHtml,
      confirmButtonText: "Confirmar cierre",
      confirmButtonColor: "#1a6b3c",
      cancelButtonText: "Cancelar",
      showCancelButton: true,
      width: "640px",
      background: "#111c2d",
      color: "#e8eaf0",
    });

    if (!isConfirmed) return;

    const observaciones = document.getElementById("cc-obs")?.value ?? "";
    Swal.fire({
      title: "Guardando...",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    const saveRes = await fetch("./accion/putCierreCaja.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        fechaDesde: data.fechaDesde,
        fechaHasta: data.fechaHasta,
        efectivo: t.EFECTIVO,
        transferencia: t.TRANS,
        mercadopago: t.MERCPAGO,
        debito: t.DEBITO,
        observaciones,
      }),
    });
    const saveData = await saveRes.json();

    saveData.success
      ? Swal.fire(
          "¡Cierre realizado!",
          `El cierre de caja #${saveData.id} fue registrado correctamente.`,
          "success",
        )
      : Swal.fire(
          "Error",
          saveData.error ?? "No se pudo guardar el cierre.",
          "error",
        );
  } catch (err) {
    Swal.fire("Error", "Error inesperado: " + err.message, "error");
  }
}

function buildRowCierre(label, key, data, fmt) {
  const keyMap = {
    EFECTIVO: "efectivo",
    TRANS: "transferencia",
    MERCPAGO: "mercadopago",
    DEBITO: "debito",
  };
  const k = keyMap[key];
  const vPagos = data.totalesPagos[k] ?? 0;
  const vCobros = data.totalesCobros[k] ?? 0;
  const vTotal = data.totales[key] ?? 0;
  const opacity = vTotal > 0 ? "" : "opacity:0.45;";
  return `
    <tr style="border-bottom:1px solid #1a2a40;${opacity}">
      <td style="padding:8px 12px;">${label}</td>
      <td style="padding:8px 12px;text-align:right;">${fmt(vPagos)}</td>
      <td style="padding:8px 12px;text-align:right;">${fmt(vCobros)}</td>
      <td style="padding:8px 12px;text-align:right;font-weight:600;">${fmt(vTotal)}</td>
    </tr>`;
}

function formatFechaCierre(str) {
  if (!str) return "";
  const d = new Date(str.replace(" ", "T"));
  return `${d.toLocaleDateString("es-UY", { day: "2-digit", month: "2-digit", year: "numeric" })} ${d.toLocaleTimeString("es-UY", { hour: "2-digit", minute: "2-digit" })}`;
}
