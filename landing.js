document.addEventListener("DOMContentLoaded", () => {
  const cover = document.getElementById("cover");
  const loader = document.getElementById("loader");
  document.body.style.overflow = "hidden";

  window.onload = function () {
    loader.style.display = "none"; // Hide loader
    cover.style.display = "none"; // Hide cover
    document.body.style.overflow = "auto";
  };

  const modal = document.getElementById("myModal");
  const modal2 = document.getElementById("myModal2");
  const openProfile = document.getElementById("openProfile");
  const openProfile2 = document.getElementById("openProfile2");
  const openReserve = document.getElementById("openReserve");
  const openReserve2 = document.getElementById("openReserve2");
  const closeProfile = document.getElementById("closeProfile");
  const closeReserve = document.getElementById("closeReserve");
  const acceptReserve = document.getElementById("acceptReserve");
  const calendar = document.getElementById("calendar");

  function openModal() {
    modal.style.display = "block";
    setTimeout(() => {
      modal.querySelector(".modal-content").classList.add("show");
    }, 10);
  }
  function openModal2() {
    modal2.style.display = "block";
    setTimeout(() => {
      modal2.querySelector(".modal-content2").classList.add("show");
    }, 10);
  }

  openReserve.onclick = openModal;
  openReserve2.onclick = openModal;
  openProfile.onclick = openModal2;
  openProfile2.onclick = openModal2;

  function closeModal() {
    modal.querySelector(".modal-content").classList.remove("show");
    setTimeout(() => {
      modal.style.display = "none";
    }, 350);
  }
  function closeModal2() {
    modal2.querySelector(".modal-content2").classList.remove("show");
    setTimeout(() => {
      modal2.style.display = "none";
    }, 350);
  }
  closeProfile.onclick = closeModal2;
  closeReserve.onclick = closeModal;
  acceptReserve.onclick = closeModal;

  const today = new Date();
  let selectedCard = null; // Track the currently selected card
  let selectedCardData = null;

  // Create a document fragment to minimize reflows
  const fragment = document.createDocumentFragment();

  // Loop to create cards for the next 10 days
  for (let i = 0; i < 10; i++) {
    const nextDay = new Date(today);
    nextDay.setDate(today.getDate() + i);

    const dayCard = document.createElement("div");
    dayCard.className = "card";

    // Extract day, date, and month
    const dayName = nextDay.toLocaleDateString("es-ES", { weekday: "short" });
    const dayNumber = nextDay.getDate();
    const monthName = nextDay.toLocaleDateString("es-ES", { month: "short" });

    // Set the content in stacked format
    dayCard.innerHTML = `
        <span class="day">${dayName}</span>
        <span class="date">${dayNumber}</span>
        <span class="month">${monthName}</span>
    `;

    // Check if this card represents today
    if (i === 0) {
      // Set the background color and increase font weight for today
      dayCard.style.backgroundColor = "#1021b8"; // Change to your desired color
      dayCard.querySelector(".day").style.textShadow = "1px 0 0 white"; // Increase font weight
      dayCard.style.color = "white";
      dayCard.querySelector(".date").style.textShadow = "1px 0 0 white";

      // Set selected card to today
      selectedCard = dayCard;
      selectedCardData = {
        day: dayName,
        date: dayNumber,
        month: monthName,
      };
    }

    // Add click event listener
    dayCard.addEventListener("click", () => {
      // Reset previously selected card's styles
      if (selectedCard) {
        selectedCard.style.backgroundColor = ""; // Reset background color
        selectedCard.querySelector(".day").style.textShadow = ""; // Reset font weight
        selectedCard.querySelector(".date").style.textShadow = "";
        selectedCard.style.color = "";
      }

      // Set the background color and increase font weight of the clicked card
      dayCard.style.backgroundColor = "#1021b8"; // Change to your desired color
      dayCard.querySelector(".day").style.textShadow = "1px 0 0 white"; // Increase font weight
      dayCard.style.color = "white";
      dayCard.querySelector(".date").style.textShadow = "1px 0 0 white";

      // Update the selected card
      selectedCard = dayCard;
      selectedCardData = {
        day: dayName,
        date: dayNumber,
        month: monthName,
      };
    });

    // Append each dayCard to the fragment
    fragment.appendChild(dayCard);
  }

  // Finally, append the fragment to the calendar
  calendar.appendChild(fragment);

  // Function to toggle hour card selection
  function toggleCardSelection(card, hour) {
    if (selectedCards.includes(card)) {
      // If already selected, remove selection
      card.classList.remove("selected");
      selectedCards.splice(selectedCards.indexOf(card), 1); // Remove from array
    } else {
      // If not selected, add selection
      card.classList.add("selected");
      selectedCards.push(card); // Add to array
    }

    // Optionally store selected data if needed
    // selectedCardData = selectedCards.map(c => c.innerText);
  }

  const selectedCards = []; // Array to track selected cards

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
        background-color: #1021b8; /* Change to your desired color */
        color: white;
    }
    .card.selected .hour {
        text-shadow: 1px 0 0 white; /* Increase font weight */
    }`;

  document.head.appendChild(style);
  const calendar1 = document.getElementById("court-hs"); // First container
  const calendar2 = document.getElementById("practice-hs"); // Second container
  const calendar3 = document.getElementById("cantine-hs"); // Third container

  createHourCards(calendar1, new Date().getHours(), 8); // Current hour
  createHourCards(calendar2, new Date().getHours(), 8); // Next 8 hours
  createHourCards(calendar3, new Date().getHours(), 8); // Following 8 hours

  document.getElementById("editProfile").addEventListener("click", function () {
    const displayElements = document.querySelectorAll(".profile-detail span");
    const inputElements = document.querySelectorAll(".profile-detail input");

    displayElements.forEach((span, index) => {
      span.classList.add("hidden");
      inputElements[index].classList.remove("hidden");
    });

    document.getElementById("editProfile").classList.add("hidden");
    document.getElementById("saveProfile").classList.remove("hidden");
  });

  document.getElementById("saveProfile").addEventListener("click", function () {
    const displayElements = document.querySelectorAll(".profile-detail span");
    const inputElements = document.querySelectorAll(".profile-detail input");

    displayElements.forEach((span, index) => {
      span.textContent = inputElements[index].value;
      span.classList.remove("hidden");
      inputElements[index].classList.add("hidden");
    });

    document.getElementById("editProfile").classList.remove("hidden");
    document.getElementById("saveProfile").classList.add("hidden");
  });
});
