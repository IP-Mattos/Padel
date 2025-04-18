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
  const openReserve = document.getElementById("openReserve");
  const openCantine = document.getElementById("openCantine");
  const openTraining = document.getElementById("openTraining");
  const openClasses = document.getElementById("openClasses");
  const openRivals = document.getElementById("openRivals");
  const openMembers = document.getElementById("openMembers");

  const closeReserve = document.getElementById("closeReserve");
  const closeProfile = document.getElementById("closeProfile");
  const closeCantine = document.getElementById("closeCantine");
  const closeTraining = document.getElementById("closeTraining");
  const closeClasses = document.getElementById("closeClasses");
  const closeRivals = document.getElementById("closeRivals");
  const closeMembers = document.getElementById("closeMembers");
  const acceptReserve = document.getElementById("acceptReserve");
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

  openReserve.onclick = () => openModal(cModal, ".cModal-content");
  openProfile.onclick = () => openModal(pModal, ".pModal-content");
  openProfile2.onclick = () => openModal(pModal, ".pModal-content");
  openCantine.onclick = () => openModal(caModal, ".caModal-content");
  openTraining.onclick = () => openModal(tModal, ".tModal-content");
  openClasses.onclick = () => openModal(clModal, ".clModal-content");
  openRivals.onclick = () => openModal(rModal, ".rModal-content");
  openMembers.onclick = () => openModal(sModal, ".sModal-content");

  closeReserve.onclick = () => closeModal(cModal, ".cModal-content");
  closeProfile.onclick = () => closeModal(pModal, ".pModal-content");
  closeCantine.onclick = () => closeModal(caModal, ".caModal-content");
  closeTraining.onclick = () => closeModal(tModal, ".tModal-content");
  closeClasses.onclick = () => closeModal(clModal, ".clModal-content");
  closeRivals.onclick = () => closeModal(rModal, ".rModal-content");
  closeMembers.onclick = () => closeModal(sModal, ".sModal-content");
  acceptReserve.onclick = () => closeModal(cModal, ".cModal-content");
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

  function populateCalendarCards(containerDiv) {
    const today = new Date();
    let selectedCard = null; // Track the currently selected card
    let selectedCardData = null;

    // Create a document fragment to minimize reflows
    const fragment = document.createDocumentFragment();

    // Loop to create cards for the next 12 days
    for (let i = 0; i < 12; i++) {
      const nextDay = new Date(today);
      nextDay.setDate(today.getDate() + i);

      const dayCard = document.createElement("div");
      dayCard.className = "card";

      const dayName = nextDay.toLocaleDateString("es-ES", { weekday: "short" });
      const dayNumber = nextDay.getDate();
      const monthName = nextDay.toLocaleDateString("es-ES", { month: "short" });

      dayCard.innerHTML = `
        <span class="day">${dayName}</span>
        <span class="date">${dayNumber}</span>
        <span class="month">${monthName}</span>
      `;

      // If today, style it specially
      if (i === 0) {
        styleSelectedCard(dayCard);
        selectedCard = dayCard;
        selectedCardData = { day: dayName, date: dayNumber, month: monthName };
      }

      // Add click listener to handle selection
      dayCard.addEventListener("click", () => {
        if (selectedCard) resetCardStyle(selectedCard);

        styleSelectedCard(dayCard);
        selectedCard = dayCard;
        selectedCardData = { day: dayName, date: dayNumber, month: monthName };
      });

      fragment.appendChild(dayCard);
    }

    containerDiv.appendChild(fragment);

    // Utility: Apply selected styles
    function styleSelectedCard(card) {
      card.style.backgroundColor = "var(--primary-color)";
      card.querySelector(".day").style.textShadow = "1px 0 0 black";
      card.querySelector(".date").style.textShadow = "1px 0 0 black";
      card.style.color = "black";
    }

    // Utility: Reset selected styles
    function resetCardStyle(card) {
      card.style.backgroundColor = "";
      card.querySelector(".day").style.textShadow = "";
      card.querySelector(".date").style.textShadow = "";
      card.style.color = "";
    }

    // Return selected card data and toggle function if needed
    return {
      getSelectedCardData: () => selectedCardData,
      toggleCardSelection,
    };
  }

  const courtCalendarUtils = populateCalendarCards(courtCalendar);
  const cantineCalendarUtils = populateCalendarCards(cantineCalendar);
  const trainingCalendarUtils = populateCalendarCards(trainingCalendar);
  const rivalsCalendarUtils = populateCalendarCards(rivalsCalendar);

  //================================================>
  //HOURS
  //================================================>

  const selectedCards = []; // Array to track selected cards

  function toggleCardSelection(card, hour) {
    if (selectedCards.includes(card)) {
      card.classList.remove("selected");
      selectedCards.splice(selectedCards.indexOf(card), 1);
    } else {
      card.classList.add("selected");
      selectedCards.push(card);
    }
  }

  function createHourCards(container, startHour, numHours) {
    const now = new Date();
    now.setHours(startHour); // Set the starting hour

    for (let i = 0; i < numHours; i++) {
      const nextHour = new Date(now);
      nextHour.setHours(startHour + i); // Increment hour

      const hourCard = document.createElement("div");
      hourCard.className = "card";
      hourCard.tabIndex = 0; // Make the card focusable

      // Format the hour period as HH - HH
      const hourString = `${nextHour
        .getHours()
        .toString()
        .padStart(2, "0")} - ${nextHour.getHours() + 1}`.padStart(2, "0");

      // Set the content
      hourCard.innerHTML = `
            <span class="hour">${hourString}</span>
        `;

      // Add click event listener
      hourCard.addEventListener("click", () => {
        toggleCardSelection(hourCard, hourString);
      });

      // Optionally, handle keyboard events for accessibility
      hourCard.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault(); // Prevent scrolling for space key
          toggleCardSelection(hourCard, hourString);
        }
      });

      container.appendChild(hourCard);
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
  const courths = document.getElementById("court-hs"); // First container
  const cantinehs = document.getElementById("cantine-hs");
  const traininghs = document.getElementById("training-hs");
  const rivalshs = document.getElementById("rivals-hs");

  createHourCards(courths, new Date().getHours(), 9); // Current hour
  createHourCards(cantinehs, new Date().getHours(), 9);
  createHourCards(traininghs, new Date().getHours(), 9);
  createHourCards(rivalshs, new Date().getHours(), 9);
});
