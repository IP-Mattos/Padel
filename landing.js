document.addEventListener("DOMContentLoaded", () => {
  //============================================>
  //LOADER
  //============================================>
  const cover = document.getElementById("cover");
  const loader = document.getElementById("loader");
  document.body.style.overflow = "hidden";

  window.onload = function () {
    loader.style.display = "none"; // Hide loader
    cover.style.display = "none"; // Hide cover
    document.body.style.overflow = "auto";
  };

  //============================================>
  //MODALS
  //============================================>
  const cModal = document.getElementById("courtModal");
  const pModal = document.getElementById("profileModal");
  const caModal = document.getElementById("cantineModal");
  const tModal = document.getElementById("trainingModal");
  const clModal = document.getElementById("classesModal");
  const rModal = document.getElementById("rivalsModal");
  const sModal = document.getElementById("membersModal");
  const hModal = document.getElementById("hoursModal");

  const modalConfigs = [
    {
      name: "court",
      modal: cModal,
      contentClass: ".cModal-content",
      openButtons: [],
      closeButtons: ["closeCourt"],
    },
    {
      name: "profile",
      modal: pModal,
      contentClass: ".pModal-content",
      openButtons: ["openProfile", "openProfile2"],
      closeButtons: ["closeProfile"],
    },
    {
      name: "cantine",
      modal: caModal,
      contentClass: ".caModal-content",
      openButtons: ["openCantine"],
      closeButtons: ["closeCantine"],
    },
    {
      name: "training",
      modal: tModal,
      contentClass: ".tModal-content",
      openButtons: ["openTraining"],
      closeButtons: ["closeTraining"],
    },
    {
      name: "classes",
      modal: clModal,
      contentClass: ".clModal-content",
      openButtons: ["openClasses"],
      closeButtons: ["closeClasses"],
    },
    {
      name: "rivals",
      modal: rModal,
      contentClass: ".rModal-content",
      openButtons: ["openRivals"],
      closeButtons: ["closeRivals"],
    },
    {
      name: "members",
      modal: sModal,
      contentClass: ".sModal-content",
      openButtons: ["openMembers"],
      closeButtons: ["closeMembers"],
    },
    {
      name: "hours",
      modal: hModal,
      contentClass: ".hModal-content",
      openButtons: ["openHours"],
      closeButtons: ["closeHours"],
    },
  ];

  modalConfigs.forEach(({ modal, contentClass, openButtons, closeButtons }) => {
    //Openers
    openButtons.forEach((btnId) => {
      const btn = document.getElementById(btnId);
      if (btn) {
        btn.onclick = () => openModal(modal, contentClass);
      }
    });

    //Closers
    closeButtons.forEach((btnId) => {
      const btn = document.getElementById(btnId);
      if (btn) {
        btn.onclick = () => closeModal(modal, contentClass);
      }
    });
  });

  function openModal(modal, contentClass) {
    modal.style.display = "flex";
    setTimeout(() => {
      modal.querySelector(contentClass).classList.add("show");
    }, 10);
  }

  function closeModal(modal, contentClass) {
    modal.querySelector(contentClass).classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
      selectedCard = null;
    }, 350);
  }

  //===================================>
  //PROFILE MODAL
  //===================================>

  const editProfile = document.getElementById("editProfile");
  const profileDetail = document.getElementById("profileDetail");
  const updateProfile = document.getElementById("updateProfile");
  const cancelUpdate = document.getElementById("cancelUpdate");

  editProfile.addEventListener("click", () => {
    profileDetail.classList.add("hidden");
    updateProfile.style.display = "flex";
  });

  cancelUpdate.addEventListener("click", (e) => {
    e.preventDefault();
    updateProfile.style.display = "none";
    profileDetail.classList.remove("hidden");
  });

  updateProfile.addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("./accion/savePerfil.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.consultaResponse.codigoError === "0") {
          Swal.fire({
            title: "Éxito!",
            text: data.consultaResponse.detalleError,
            icon: "success",
          });

          // OPTIONAL: Update the visible profile data with the new values
          document.getElementById("profileNombre").innerText =
            formData.get("nombre");
          document.getElementById("profileNick").innerText =
            formData.get("usuario");
          document.getElementById("profileMail").innerText =
            formData.get("mail");
          document.getElementById("profileCategory").innerText =
            formData.get("categoria");
          document.getElementById("profileBirth").innerText =
            formData.get("fechnac");
          document.getElementById("profileFrase").innerText =
            formData.get("frase");

          // For the radio option (categorías)
          const masCategoriasValue = formData.get("mascategorias");
          const masCategoriasText =
            masCategoriasValue === "0"
              ? "Misma categoría"
              : masCategoriasValue === "1"
              ? "Categorías contiguas"
              : "Todas las categorías";
          document.getElementById("profileMasCat").innerText =
            masCategoriasText;

          // Optionally hide form and show updated profile
          updateProfile.style.display = "none";
          profileDetail.classList.remove("hidden");
        } else {
          Swal.fire({
            title: "Error",
            text: data.consultaResponse.detalleError,
            icon: "error",
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire("Oops", "Algo salió mal en el servidor", "error");
      });
  });

  const profileImgInput = document.getElementById("profileImgInput");
  const profileImg = document.getElementById("profileImg");

  profileImgInput.addEventListener("change", () => {
    const file = profileImgInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("imgPerfilUser", file); // Sending the image file
    formData.append("idUser", userId); // Sending the user ID from the PHP session

    fetch("./accion/saveImgPerfil.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (
          data.consultaResponse &&
          data.consultaResponse.codigoError === "0"
        ) {
          const timestamp = new Date().getTime();
          const newImg = data.consultaResponse.newImg;
          profileImg.src = `./accion/imgPerfilUser/${newImg}?t=${timestamp}`;

          Swal.fire(
            "Imagen actualizada",
            "Tu nueva foto de perfil se ha subido",
            "success"
          );
        } else {
          Swal.fire(
            "Error",
            data.consultaResponse?.detalleError || "No se pudo subir la imagen",
            "error"
          );
        }
      })
      .catch((err) => {
        console.error("Fetch error:", err);
        Swal.fire("Oops", "Error al subir la imagen", "error");
      });
  });

  //==================================>
  //CLASSES MODAL
  //==================================>

  document.getElementById("teachContainer");

  let selectedProfCard = null;

  document.getElementById("openClasses").addEventListener("click", async () => {
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
        card.innerHTML = `<img src="./accion/imgPerfilUser/${prof.imgperfil}" alt="${prof.nombre}" />
                          <h3>${prof.nombre}</h3>`;

        let isFetchingClassData = false;

        card.addEventListener("click", async () => {
          if (isFetchingClassData) return;

          isFetchingClassData = true;

          if (selectedProfCard) {
            selectedProfCard.classList.remove("selected-teacher");
          }
          card.classList.add("selected-teacher");
          selectedProfCard = card;

          try {
            await handleProfessorSelection(prof.id, {
              ...config,
              container: classCalendar,
              hoursContainer: classhs,
            });
          } finally {
            isFetchingClassData = false;
          }
        });

        profListContainer.appendChild(card);

        if (index === 0) {
          firstCard = card;
        }
      });
      if (firstCard) {
        firstCard.click();
      }
    } catch (err) {
      console.error("Error al cargar profesores", err);
      profListContainer.innerHTML = "<p>Error al cargar profesores</p>";
    }
  });

  async function handleProfessorSelection(profeId, config) {
    const { servicio, container, hoursContainer } = config;

    container.innerHTML = "<p>Cargando días</p>";
    hoursContainer.innerHTML = "";

    const formData = new URLSearchParams();
    formData.append("servicio", servicio);
    formData.append("profe", profeId);

    try {
      const res = await fetch("./accion/getDias.php", {
        method: "POST",
        headers: { "content-type": "application/x-www-form-urlencoded" },
        body: formData.toString(),
      });

      const json = await res.json();
      container.innerHTML = "";

      if (json.consultaResponse.codigoError === "0") {
        const datos = json.consultaResponse.datos;

        calendarUtils.classes = populateCalendarCards(
          container,
          datos,
          (selectedDay) => {
            fetchHours(
              {
                ...selectedDay,
                servicio,
                profe: profeId,
              },
              hoursContainer
            );
          }
        );
      } else {
        container.innerHTML = "<p>Error al cargar los días</p>";
        console.error(json.consultaResponse.detalleError);
      }
    } catch (err) {
      container.innerHTML = "<p>Error de conexión";
      console.error("Error en handleProfessorSelection:", err);
    }
  }

  //===================================================>
  //CALENDARS
  //===================================================>

  const courtCalendar = document.getElementById("court-calendar");
  const classCalendar = document.getElementById("classes-calendar");
  const cantineCalendar = document.getElementById("cantine-calendar");
  const trainingCalendar = document.getElementById("training-calendar");
  const rivalsCalendar = document.getElementById("rivals-calendar");

  const profListContainer = document.getElementById("profList");

  const courths = document.getElementById("court-hs"); // First container
  const classhs = document.getElementById("class-hs");
  const cantinehs = document.getElementById("cantine-hs");
  const traininghs = document.getElementById("training-hs");
  const rivalshs = document.getElementById("rivals-hs");

  const shortDays = {
    lunes: "lun",
    martes: "mar",
    miercoles: "mié",
    jueves: "jue",
    viernes: "vie",
    sabado: "sáb",
    domingo: "dom",
  };

  const serviceModalMap = {
    court: {
      modal: cModal,
      content: ".cModal-content",
      servicio: 1,
      profe: 0,
      container: courtCalendar,
      hoursContainer: courths,
      confirmButtonId: "acceptCourt",
    },
    classes: {
      modal: clModal,
      content: ".clModal-content",
      servicio: 2,
      profe: null,
      container: classCalendar,
      hoursContainer: classhs,
      confirmButtonId: "acceptClasses",
    },
    training: {
      modal: tModal,
      content: ".tModal-content",
      servicio: 3,
      profe: 0,
      container: trainingCalendar,
      hoursContainer: traininghs,
      confirmButtonId: "acceptTraining",
    },
    rivals: {
      modal: rModal,
      content: ".rModal-content",
      servicio: 4,
      profe: 0,
      container: rivalsCalendar,
      hoursContainer: rivalshs,
      confirmButtonId: "acceptRivals",
    },
    cantine: {
      modal: caModal,
      content: ".caModal-content",
      servicio: 5,
      profe: 0,
      container: cantineCalendar,
      hoursContainer: cantinehs,
      confirmButtonId: "acceptCantine",
    },
  };

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  const calendarUtils = {};

  Object.entries(serviceModalMap).forEach(
    ([key, { modal, content, servicio, profe, container, hoursContainer }]) => {
      document.getElementById(`open${capitalize(key)}`).onclick = async () => {
        openModal(modal, content);

        if (key === "classes") return;

        container.innerHTML = "<p>Cargando...</p>";
        hoursContainer.innerHTML = "";

        try {
          //Create the URL-encoded payload
          const formData = new URLSearchParams();
          formData.append("servicio", servicio);
          formData.append("profe", profe);

          const res = await fetch("./accion/getDias.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString(),
          });

          const json = await res.json();
          container.innerHTML = "";

          if (json.consultaResponse.codigoError === "0") {
            const datos = json.consultaResponse.datos;
            calendarUtils[key] = populateCalendarCards(
              container,
              datos,
              (selectedDay) => {
                fetchHours(
                  {
                    ...selectedDay,
                    servicio,
                    profe,
                  },
                  hoursContainer
                );
              }
            );
          } else {
            container.innerHTML = "<p>Error al cargar los días</p>";
          }
        } catch (err) {
          container.innerHTML = "<p>Error de conexión</p>";
          console.error(err);
        }
      };
    }
  );

  function populateCalendarCards(containerDiv, calendarData, onDaySelected) {
    const today = new Date(); // Get the current date to use as a base for date calculations
    let selectedCard = null; // Will store the currently selected card (if any)
    let selectedCardData = null; // Will store metadata about the selected card

    const fragment = document.createDocumentFragment(); // Optimize performance with fragment

    // Loop over each day received from the backend
    calendarData.forEach((dayInfo, index) => {
      const { dia, estado } = dayInfo;

      // Calculate the actual date based on today's date + current index
      const actualDate = new Date(today);
      actualDate.setDate(today.getDate() + index);

      // Extract date and month in readable format
      const dayNumber = actualDate.getDate();
      const monthName = actualDate.toLocaleDateString("es-ES", {
        month: "short",
      });

      // Create a card element to represent the day
      const dayCard = document.createElement("div");
      dayCard.className = "card";

      // Set the inner HTML with weekday, date, and month
      dayCard.innerHTML = `
        <span class="day">${shortDays[dia]}</span>
        <span class="date">${dayNumber}</span>
        <span class="month">${monthName}</span>
      `;

      // Check if the day is blocked (estado === 1)
      if (estado === 1) {
        // Visually mark as unavailable
        dayCard.style.backgroundColor = "red";
        dayCard.style.opacity = "0.6";
        dayCard.style.pointerEvents = "none"; // Prevent clicks
      } else {
        // Make it selectable
        dayCard.addEventListener("click", () => {
          if (selectedCard) resetCardStyle(selectedCard); // Unselect previous
          styleSelectedCard(dayCard); // Apply selected styling
          selectedCard = dayCard;

          // Store full data about the selected day
          selectedCardData = {
            ...dayInfo,
            dayNumber,
            monthName,
            date: actualDate.toISOString().split("T")[0], // Format: YYYY-MM-DD
          };
          onDaySelected({
            ...dayInfo,
            fecha: actualDate.toISOString().split("T")[0],
          });
        });

        // Auto-select the first available day
        if (!selectedCard) {
          styleSelectedCard(dayCard);
          selectedCard = dayCard;
          selectedCardData = {
            ...dayInfo,
            dayNumber,
            monthName,
            date: actualDate.toISOString().split("T")[0],
          };
          onDaySelected({
            ...dayInfo,
            fecha: actualDate.toISOString().split("T")[0],
          });
        }
      }

      // Add the card to the document fragment
      fragment.appendChild(dayCard);
    });

    // Inject all generated cards into the calendar container
    containerDiv.appendChild(fragment);

    // === Helper functions for styling ===

    // Apply selected styles to a card
    function styleSelectedCard(card) {
      card.style.backgroundColor = "var(--primary-color)";
      card.querySelector(".day").style.textShadow = "1px 0 0 black";
      card.querySelector(".date").style.textShadow = "1px 0 0 black";
      card.style.color = "black";
    }

    // Reset card styles back to default
    function resetCardStyle(card) {
      card.style.backgroundColor = "";
      card.querySelector(".day").style.textShadow = "";
      card.querySelector(".date").style.textShadow = "";
      card.style.color = "";
    }

    // Return a way to access the selected card’s data
    return {
      getSelectedCardData: () => selectedCardData,
    };
  }

  //================================================>
  //HOURS
  //================================================>

  let selectedCard = null; // To store the selected card

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

    hourData.forEach(({ hora, estado, idUsuario, timeEstado, idReserva }) => {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `<span class="hour">${hora}</span>`;

      const isReservedByUser = idUsuario && idUsuario === userId;
      const isCancelable = isReservedByUser && isWithinLastHour(timeEstado);

      if (estado === 1) {
        card.style.backgroundColor = "red";
        if (isCancelable) {
          card.style.opacity = "1";
          card.style.pointerEvents = "auto";
        } else {
          card.style.opacity = "0.6";
          card.style.pointerEvents = "none";
        }
      } else {
        card.addEventListener("click", () => toggleCardSelection(card));
        card.addEventListener("keydown", (event) => {
          if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            toggleCardSelection(card);
          }
        });
      }

      if (isCancelable) {
        const cancelBtn = document.createElement("span");
        cancelBtn.className = "cancel-btn";
        cancelBtn.innerText = "✖";
        cancelBtn.title = "Cancelar reserva";
        cancelBtn.style.marginLeft = "3px";
        cancelBtn.style.cursor = "pointer";
        cancelBtn.style.color = "darkred";
        cancelBtn.onclick = async (e) => {
          e.stopPropagation();
          const confirm = await Swal.fire({
            title: "Cancelar reserva?",
            text: `Deseas cancelar tu reserva de las ${hora}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, cancelar",
            cancelButtonText: "No",
          });
          if (!confirm.isConfirmed) return;

          try {
            const formData = new URLSearchParams();
            formData.append("idReserv", idReserva);

            const res = await fetch("./accion/putReservCancel.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: formData.toString(),
            });

            const json = await res.json();

            if (json.consultaResponse?.codigoError === "0") {
              Swal.fire(
                "Cancelada",
                "Tu reserva fue cancelada",
                "success"
              ).then(() => {
                fetchHours(fetchParams, fetchParams.container); // re-fetch updated hours
              });
            } else {
              Swal.fire("Error", "No se pudo cancelar tu reserva", "error");
            }
          } catch (err) {
            console.error("Error cancelando reserva:", err);
            Swal.fire("Error", "Problema al conectar con el servidor", "error");
          }
        };
        card.appendChild(cancelBtn);
      }
      container.appendChild(card);
    });
  }

  function isWithinLastHour(fechaString) {
    if (!fechaString || typeof fechaString !== "string") return false;

    try {
      // Convert from "YYYY-MM-DD HH:mm:ss" to a valid ISO string
      const isoString = fechaString.replace(" ", "T");
      const fechaReserva = new Date(isoString);

      const now = new Date();
      const oneHourAgo = new Date(now.getTime() - 60 * 60 * 1000);

      return fechaReserva >= oneHourAgo && fechaReserva <= now;
    } catch (e) {
      console.error("Error parsing fechaString:", fechaString, e);
      return false;
    }
  }

  function toggleCardSelection(card) {
    if (selectedCard) {
      // Deselect the currently selected card
      selectedCard.classList.remove("selected");
    }

    // Select the new card
    card.classList.add("selected");
    selectedCard = card; // Store the new selected card
  }

  const style = document.createElement("style");

  style.textContent = `
      .card.selected {
        background-color: var(--primary-color); /* Change to your desired color */
        color: black;
      }
      .card.selected .hour {
        text-shadow: 1px 0 0 black; /* Increase font weight */
      }`;

  document.head.appendChild(style);

  //====================================>
  //RESERVING
  //====================================>

  Object.entries(serviceModalMap).forEach(([key, config]) => {
    const { confirmButtonId, servicio, profe } = config;

    const confirmBtn = document.getElementById(confirmButtonId);
    if (!confirmBtn) return;

    confirmBtn.addEventListener("click", async () => {
      const selectedDay = calendarUtils[key]?.getSelectedCardData();
      if (!selectedDay) {
        Swal.fire("Error", "Por favor seleccione un día", "error");
        return;
      }

      if (!selectedCard) {
        Swal.fire("Error", "Por favor selecciona una hora.", "error");
        return;
      }

      // Show confirmation modal
      const confirmation = await Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Deseas reservar para el ${selectedDay.date} a las ${
          selectedCard.querySelector(".hour").textContent
        }?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, reservar",
        cancelButtonText: "Cancelar",
      });

      // If user cancels
      if (!confirmation.isConfirmed) {
        return;
      }

      const selectedHour = selectedCard.querySelector(".hour").textContent;

      const formData = new URLSearchParams();

      formData.append("fecha", selectedDay.date); // format YYYY-MM-DD
      formData.append("servicio", servicio);
      formData.append("profe", profe);
      formData.append("usuario", userId);
      formData.append("hora", selectedHour);

      try {
        const res = await fetch("../accion/putReserv.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: formData.toString(),
        });

        const json = await res.json();

        if (json.consultaResponse?.codigoError === "0") {
          Swal.fire("Éxito", "Reserva realizada con éxito", "success");
          closeModal(config.modal, config.content);
        } else {
          Swal.fire("Error", "Error al realizar la reserva", "error");
          console.error("Respuesta del servidor:", json);
        }
      } catch (err) {
        console.error("Error al enviar reserva:", err);
        Swal.fire("Error", "Error de conexión al enviar la reserva.", "error");
      }
    });
  });
  //================================================================>
  //USER HOURS
  //================================================================>

  const container = document.getElementById("reserveContainer");
  const serviceImages = {
    1: "./img/resCancha.png",
    2: "./img/resClases.png",
    3: "./img/resEntrenar.png",
    4: "./img/resRivales.png",
    5: "./img/resChelada.png",
    // Add more services as needed
  };

  document.getElementById("openHours").addEventListener("click", async (e) => {
    container.innerHTML = "<p>Cargando tus reservas...</p>";

    let now = new Date();

    let sevenDays = new Date(now);
    sevenDays.setDate(now.getDate() + 7);

    let sevenDaysBack = new Date(now);
    sevenDaysBack.setDate(now.getDate() - 7);

    function formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are 0-based
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}/${month}/${day}`;
    }

    fechaHasta = formatDate(sevenDays);
    fechaDesde = formatDate(sevenDaysBack);

    const formData = new URLSearchParams();
    formData.append("fechaDesde", fechaDesde);
    formData.append("fechaHasta", fechaHasta);
    formData.append("idUser", userId);

    try {
      const res = await fetch("./accion/getHorasUser.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString(),
      });

      const json = await res.json();

      if (json.consultaResponse?.codigoError === "0") {
        const datos = json.consultaResponse.datos;
        datos.reverse();
        container.innerHTML = "";

        datos.forEach((item) => {
          const card = document.createElement("div");
          card.className = "rCard";

          // Parse the date properly in local time
          const [year, month, day] = item.fecha.split("-").map(Number);
          const itemDate = new Date(year, month - 1, day); // Local time

          const today = new Date();
          today.setHours(0, 0, 0, 0);

          if (itemDate < today) {
            card.style.backgroundColor = "#e0e0e0";
            card.style.color = "black";
          } else if (itemDate.getTime() === today.getTime()) {
            card.style.backgroundColor = "#003266";
            card.style.color = "var(--primary-color)";
          } else {
            card.style.backgroundColor = "#003266";
          }

          const imageUrl = serviceImages[item.servicio] || "images/default.png";

          function formatDateName(dateStr) {
            const date = new Date(dateStr);
            const day = date.getDate();
            const month = date.toLocaleString("es-ES", { month: "long" });
            return `${day} de ${month}`;
          }

          const formattedDate = formatDateName(item.fecha);

          card.innerHTML = `          
          <p><strong>${formattedDate}</strong></p>
          <img src="${imageUrl}" alt="Servicio ${
            item.servicio
          }" class="service-icon" />
          <p><strong>${item.hora.split(":").slice(0, 2).join(":")}</strong></p>
          `;

          container.appendChild(card);
        });
      } else {
        container.innerHTML = "<p>Hubo un error al cargar las horas</p>";
      }
    } catch (error) {
      console.error("Error de conexion:", error);
    }
  });
});
