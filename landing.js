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

  const openProfile = document.getElementById("openProfile");
  const openProfile2 = document.getElementById("openProfile2");
  // const openCourt = document.getElementById("openCourt");
  const openCantine = document.getElementById("openCantine");
  const openTraining = document.getElementById("openTraining");
  const openClasses = document.getElementById("openClasses");
  const openRivals = document.getElementById("openRivals");
  const openMembers = document.getElementById("openMembers");

  const closeCourt = document.getElementById("closeCourt");
  const closeProfile = document.getElementById("closeProfile");
  const closeCantine = document.getElementById("closeCantine");
  const closeTraining = document.getElementById("closeTraining");
  const closeClasses = document.getElementById("closeClasses");
  const closeRivals = document.getElementById("closeRivals");
  const closeMembers = document.getElementById("closeMembers");
  const acceptCourt = document.getElementById("acceptCourt");
  const acceptCantine = document.getElementById("acceptCantine");
  const acceptTraining = document.getElementById("acceptTraining");
  const acceptRivals = document.getElementById("acceptRivals");
  const acceptMembers = document.getElementById("acceptMembers");

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
    }, 350);
  }

  // openCourt.onclick = () => openModal(cModal, ".cModal-content");
  openProfile.onclick = () => openModal(pModal, ".pModal-content");
  openProfile2.onclick = () => openModal(pModal, ".pModal-content");
  openCantine.onclick = () => openModal(caModal, ".caModal-content");
  openTraining.onclick = () => openModal(tModal, ".tModal-content");
  openClasses.onclick = () => openModal(clModal, ".clModal-content");
  openRivals.onclick = () => openModal(rModal, ".rModal-content");
  openMembers.onclick = () => openModal(sModal, ".sModal-content");

  closeCourt.onclick = () => closeModal(cModal, ".cModal-content");
  closeProfile.onclick = () => closeModal(pModal, ".pModal-content");
  closeCantine.onclick = () => closeModal(caModal, ".caModal-content");
  closeTraining.onclick = () => closeModal(tModal, ".tModal-content");
  closeClasses.onclick = () => closeModal(clModal, ".clModal-content");
  closeRivals.onclick = () => closeModal(rModal, ".rModal-content");
  closeMembers.onclick = () => closeModal(sModal, ".sModal-content");
  acceptCourt.onclick = () => closeModal(cModal, ".cModal-content");
  acceptCantine.onclick = () => closeModal(caModal, ".caModal-content");
  acceptTraining.onclick = () => closeModal(tModal, ".tModal-content");
  acceptRivals.onclick = () => closeModal(rModal, ".rModal-content");
  acceptMembers.onclick = () => closeModal(sModal, ".sModal-content");

  //===================================>
  //PROFILE MODAL
  //===================================>

  document.getElementById("editProfile").addEventListener("click", function () {
    const rows = document.querySelectorAll(".profile-detail .row");

    rows.forEach((row) => {
      const span = row.querySelector("span");
      const input = row.querySelector("input");

      if (span && input) {
        span.classList.add("hidden");
        input.classList.remove("hidden");
      }
    });

    document.getElementById("editProfile").classList.add("hidden");
    document.getElementById("saveProfile").classList.remove("hidden");
  });

  document.getElementById("saveProfile").addEventListener("click", function () {
    const rows = document.querySelectorAll(".profile-detail .row");

    rows.forEach((row) => {
      const span = row.querySelector("span");
      const input = row.querySelector("input");

      if (span && input) {
        span.textContent = input.value;
        span.classList.remove("hidden");
        input.classList.add("hidden");
      }
    });

    document.getElementById("editProfile").classList.remove("hidden");
    document.getElementById("saveProfile").classList.add("hidden");
  });

  //==================================>
  //CLASSES MODAL
  //==================================>

  const teachCards = document.querySelectorAll(".teachCard");
  const teachContainer = document.querySelector(".teachContainer");
  const classContainer = document.querySelector(".classContainer");
  const teachTitle = document.getElementById("selectTeach");
  const classTitle = document.getElementById("selectClass");
  const classSections = {
    1: document.querySelector(".class1cards"),
    2: document.querySelector(".class2cards"),
    3: document.querySelector(".class3cards"),
  };
  const backBtn = document.getElementById("backToTeachers");

  //hide all class card sections initially
  Object.values(classSections).forEach(
    (section) => (section.style.display = "none")
  );
  classContainer.style.display = "none";

  teachCards.forEach((card) => {
    card.addEventListener("click", () => {
      const teacher = card.getAttribute("data-teacher");

      //hide teacher selection
      teachContainer.style.display = "none";

      //show class container
      classContainer.style.display = "grid";

      //show the class title
      classTitle.style.display = "block";

      //hide the teacher title
      teachTitle.style.display = "none";

      //show back button
      backBtn.style.display = "block";

      //hide all class cards and only show the selected one
      Object.entries(classSections).forEach(([key, section]) => {
        section.style.display = key === teacher ? "grid" : "none";
      });
    });
  });

  backBtn.addEventListener("click", () => {
    //show teacher selection
    teachContainer.style.display = "grid";

    //hide class container
    classContainer.style.display = "none";

    //hide class title
    classTitle.style.display = "none";

    //show teacher title
    teachTitle.style.display = "block";

    //hide back button
    backBtn.style.display = "none";

    //hide all class card sections
    Object.values(classSections).forEach((section) => {
      section.style.display = "none";
    });
  });

  //===================================================>
  //CALENDARS
  //===================================================>

  const courtCalendar = document.getElementById("court-calendar");
  const cantineCalendar = document.getElementById("cantine-calendar");
  const trainingCalendar = document.getElementById("training-calendar");
  const rivalsCalendar = document.getElementById("rivals-calendar");

  const courths = document.getElementById("court-hs"); // First container
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
    },
  };

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  const calendarUtils = {};
  const selectedCards = []; // Array to track selected cards

  Object.entries(serviceModalMap).forEach(
    ([key, { modal, content, servicio, profe, container, hoursContainer }]) => {
      document.getElementById(`open${capitalize(key)}`).onclick = async () => {
        openModal(modal, content);
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
        generateHourCards(container, datos);
      } else {
        container.innerHTML = "<p>Error al cargar horarios</p>";
      }
    } catch (err) {
      console.error("Error fetching hours:", err);
      container.innerHTML = "<p>Error de conexión</p>";
    }
  }

  function generateHourCards(container, hourData) {
    container.innerHTML = "";

    hourData.forEach(({ hora, estado }) => {
      const card = document.createElement("div");
      card.className = "card";

      card.innerHTML = `<span class="hour">${hora}</span>`;

      if (estado === 1) {
        card.style.backgroundColor = "red";
        card.style.opacity = "0.6";
        card.style.pointerEvents = "none";
      } else {
        card.addEventListener("click", () => toggleCardSelection(card));
        card.addEventListener("keydown", (event) => {
          if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            toggleCardSelection(card);
          }
        });
      }
      container.appendChild(card);
    });
  }

  function toggleCardSelection(card) {
    if (selectedCards.includes(card)) {
      card.classList.remove("selected");
      selectedCards.splice(selectedCards.indexOf(card), 1);
    } else {
      card.classList.add("selected");
      selectedCards.push(card);
    }
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
});
