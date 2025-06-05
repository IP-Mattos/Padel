//======================================>
//DOM READY & PAGE LOAD HANDLING
//======================================>
document.addEventListener("DOMContentLoaded", () => {
  const cover = document.getElementById("cover");
  const loader = document.getElementById("loader");
  document.body.style.overflow = "hidden";

  window.onload = function () {
    loader.style.display = "none"; // Hide loader
    cover.style.display = "none"; // Hide cover
    document.body.style.overflow = "auto";
  };

  document.querySelectorAll(".read-more-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const moreText = this.previousElementSibling;
      const isHidden =
        moreText.style.display === "none" || !moreText.style.display;

      moreText.style.display = isHidden ? "inline" : "none";
      this.textContent = isHidden ? "Leer menos" : "Leer más";
    });
  });

  //=======================================>
  //SLIDER SETUP & NAVIGATION
  //=======================================>
  let items = document.querySelectorAll(".slider .list .item");
  let prevBtn = document.getElementById("prev");
  let nextBtn = document.getElementById("next");
  let lastPosition = items.length - 1;
  let firstPosition = 0;
  let active = 0;
  let intervalId;

  const setSlider = () => {
    let oldActive = document.querySelector(".slider .list .item.active");
    if (oldActive) oldActive.classList.remove("active");
    items[active].classList.add("active");
    //
    nextBtn.classList.remove("d-none");
    prevBtn.classList.remove("d-none");
    if (active === lastPosition) nextBtn.classList.add("d-none");
    if (active === firstPosition) prevBtn.classList.add("d-none");
  };

  const startAutoSlide = () => {
    intervalId = setInterval(() => {
      active = (active + 1) % items.length;
      setSlider();
    }, 4000); // Cambia la imagen cada 5 segundos
  };

  const stopAutoSlide = () => {
    clearInterval(intervalId);
  };

  nextBtn.onclick = () => {
    stopAutoSlide();
    active = active + 1;
    if (active > lastPosition) active = firstPosition;
    setSlider();
    startAutoSlide();
  };

  prevBtn.onclick = () => {
    stopAutoSlide();
    active = active - 1;
    if (active < firstPosition) active = lastPosition;
    setSlider();
    startAutoSlide();
  };

  setSlider();
  startAutoSlide();

  // set diameter
  const setDiameter = () => {
    let slider = document.querySelector(".slider");
    let widthSlider = slider.offsetWidth;
    let heightSlider = slider.offsetHeight;
    let diameter = Math.sqrt(
      Math.pow(widthSlider, 2) + Math.pow(heightSlider, 2)
    );
    document.documentElement.style.setProperty("--diameter", diameter + "px");
  };
  setDiameter();
  window.addEventListener("resize", () => {
    setDiameter();
  });

  //===========================================>
  //MODAL & FORM SWITCHING HANDLING
  //===========================================>

  // Get the modal
  const modal = document.getElementById("myModal");

  // Get the button that opens the modal
  const openModalButton = document.getElementById("openModal");
  const openModalButton2 = document.getElementById("openModal2");

  // Get the <span> element that closes the modal
  const span = document.getElementsByClassName("close")[0];

  // Get the button and form elements
  const formOneButton = document.getElementById("formOneButton");
  const formTwoButton = document.getElementById("formTwoButton");
  const returnButton = document.getElementById("returnButton");
  const returnButtonAction = document.getElementById("returnButtonAction");
  const formOne = document.getElementById("formOne");
  const formTwo = document.getElementById("formTwo");
  const buttonContainer = document.getElementById("buttonContainer");

  // Function to open the modal
  function openModal() {
    modal.style.display = "block";
    setInitialContent(); // Set initial content on open
    setTimeout(() => {
      modal.querySelector(".modal-content").classList.add("show"); // Add the show class for sliding effect
    }, 10); // Small timeout to ensure the display block is set
  }

  // Function to set initial content
  function setInitialContent() {
    formOne.classList.add("hidden");
    formTwo.classList.add("hidden");
    returnButton.classList.add("hidden"); // Hide the return button
    buttonContainer.style.display = "inline-block";
  }

  // Event listeners for the form switching buttons
  formOneButton.onclick = function () {
    formOne.classList.remove("hidden"); // Show Form One
    formTwo.classList.add("hidden"); // Hide Form Two
    returnButton.classList.remove("hidden"); // Show Return button
    buttonContainer.style.display = "none";
  };

  formTwoButton.onclick = function () {
    formTwo.classList.remove("hidden"); // Show Form Two
    formOne.classList.add("hidden"); // Hide Form One
    returnButton.classList.remove("hidden"); // Show Return button
    buttonContainer.style.display = "none";
  };

  // Return button action
  returnButtonAction.onclick = function () {
    setInitialContent(); // Reset to initial content
  };

  // When the user clicks the button, open the modal
  openModalButton.onclick = openModal;
  openModalButton2.onclick = openModal;

  // When the user clicks on <span> (x), close the modal
  span.onclick = function () {
    modal.querySelector(".modal-content").classList.remove("show"); // Remove the show class
    setTimeout(() => {
      modal.style.display = "none"; // Hide after animation
    }, 300); // Match this duration with the CSS transition duration
  };

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function (event) {
    if (event.target == modal) {
      modal.querySelector(".modal-content").classList.remove("show");
      setTimeout(() => {
        modal.style.display = "none";
      }, 300);
    }
  };

  //=====================================>
  //HAMBURGER MENU TOGGLE
  //=====================================>

  const menuToggle = document.getElementById("menu-toggle");
  const menu = document.querySelector(".menu");

  menuToggle.addEventListener("change", () => {
    if (menuToggle.checked) {
      menu.classList.add("open"); // Open menu
    } else {
      menu.classList.remove("open"); // Close menu
    }
  });

  const menuItems = document.querySelectorAll(".menu a");
  menuItems.forEach((item) => {
    item.addEventListener("click", () => {
      menuToggle.checked = false; // Uncheck the menu toggle
      menu.classList.remove("open"); // Close menu
    });
  });

  //==========================================>
  //SANITIZE INPUT
  //==========================================>

  const sanitizeInput = (id) => {
    const input = document.getElementById(id);
    input.addEventListener("input", function () {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  };
  ["cedula", "celular", "cedulaRegist", "phoneRegist", "userInput"].forEach(
    sanitizeInput
  );

  //=========================================>
  //ACCORDION FUNCTIONALITY
  //=========================================>

  document.querySelectorAll(".accordion").forEach((button) => {
    button.addEventListener("click", () => {
      const panel = button.nextElementSibling;
      const icon = button.querySelector(".accordion-icon");

      button.classList.toggle("active");

      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
        panel.classList.remove("open");
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
        panel.classList.add("open");
      }

      icon.textContent = button.classList.contains("active") ? "−" : "+";
    });
  });

  //========================================>
  //LOGIN FLOW
  //========================================>

  const loginModal = document.getElementById("loginModal");
  const cancelLogin = document.getElementById("cancelLogin");
  const verifyButton = document.getElementById("verifyCode");
  let clickCount = 0;

  document
    .getElementById("loginForm")
    .addEventListener("submit", function (event) {
      event.preventDefault();
      document.getElementById("loader2").style.display = "block";

      var formData = new FormData(this);

      fetch("./accion/loginUserFast.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          document.getElementById("loader2").style.display = "none";
          if (data.confirmacionResponse.codigoError === "0") {
            const token = data.confirmacionResponse.token;
            document.getElementById("cookie").value = token;
            if (data.confirmacionResponse.usSMS === true) {
              loginModal.style.display = "block";
            } else {
              window.location.href = "/landing.php";
            }
          } else {
            const errorMensaje =
              data.confirmacionResponse && data.confirmacionResponse.mensaje
                ? data.confirmacionResponse.mensaje
                : "Sus credenciales son incorrectas, intente de nuevo";
            Swal.fire({
              title: "Error",
              text: errorMensaje,
              icon: "error",
            });
          }
        })
        .catch((error) => {
          alert(error, "Ocurrió un error, intente de nuevo");
        });
    });

  cancelLogin.onclick = function () {
    loginModal.style.display = "none";
  };

  verifyButton.onclick = function (event) {
    event.preventDefault();
    var cookie = document.getElementById("cookie").value;
    clickCount++;

    var formData = new FormData(document.getElementById("verifyForm"));

    fetch("./accion/getValidateCodigo6.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.confirmacionResponse.codigoError === "0") {
          let currentDate = new Date();
          currentDate.setTime(currentDate.getTime() + 7 * 24 * 60 * 60 * 1000);
          let expires = "expires=" + currentDate.toUTCString();
          document.cookie = `goCookToken=${cookie}; ${expires}; path=/`;
          window.location.href = "/landing.php";
        } else {
          const errorMensaje =
            data.confirmacionResponse && data.confirmacionResponse.mensaje
              ? data.confirmacionResponse.mensaje
              : "Código incorrecto, intente otra vez";
          Swal.fire({
            title: "Error",
            text: errorMensaje,
            icon: "error",
          });
        }
      });

    if (clickCount >= 5) {
      Swal.fire({
        title: "Ups!",
        text: "Ha llegado a su límite de intentos, ingrese sus credenciales de nuevo e intente otra vez",
        icon: "error",
      }).then(function () {
        console.log("Alert closed");
        window.location.reload();
      });
    }
  };

  //==========================================>
  //REGISTRATION HANDLING
  //==========================================>

  document
    .getElementById("registrationForm")
    .addEventListener("submit", function (event) {
      event.preventDefault(); // Prevent the default form submission
      document.getElementById("loader2").style.display = "block";
      const formData = new FormData(this); // Get form data

      // Send the form data via AJAX (Fetch API)
      fetch(this.action, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json()) // Parse the JSON response
        .then((data) => {
          document.getElementById("loader2").style.display = "none";
          // Check if the response contains confirmacionResponse and codigoError
          if (
            data &&
            data.confirmacionResponse &&
            data.confirmacionResponse.codigoError === "0" // Success: codigoError is 0
          ) {
            // Get the 'detalle' message for success
            const mensaje = data.confirmacionResponse.mensaje;
            const token = data.confirmacionResponse.token;

            // Display the success message on the screen
            Swal.fire({
              title: "Perfecto",
              text: mensaje,
              icon: "success",
            }).then(function () {
              document.getElementById("cookie").value = token;
              loginModal.style.display = "block";
            });

            // Optionally, hide or reset the form
            // document.getElementById('formTwo').classList.add('hidden');
          } else {
            // Error: 'codigoError' is not 0, show error message
            const errorMensaje =
              data.confirmacionResponse && data.confirmacionResponse.mensaje
                ? data.confirmacionResponse.mensaje
                : "Ocurrió un error desconocido.";

            Swal.fire({
              title: "Oops!",
              text: errorMensaje,
              icon: "error",
            });
          }
        })
        .catch((error) => {
          // Handle errors like network issues, JSON parsing issues, etc.
          console.error("Error:", error);
          Swal.fire({
            title: "Oops!",
            text: "Hubo un problema, por favor intente otra vez",
            icon: "error",
          });
        });
    });

  //=======================================>
  //PWA INSTALL PROMPT
  //=======================================>

  // Show the install button

  if (loggedIn !== 1) {
    console.log("accessed install logic");
    const installButton = document.getElementById("installButton");
    const installModal = document.getElementById("installModal");
    const closeInstall = document.getElementById("closeInstall");
    const modalContent = installModal.querySelector(".imodal-content");

    const isiOS = /iphone|ipod|ipad/i.test(navigator.userAgent);
    const isInStandaloneMode = window.navigator.standalone === true;

    if (isiOS && !isInStandaloneMode) {
      installModal.style.display = "flex";
      setTimeout(() => {
        modalContent.classList.add("show");
      }, 10);

      function closeModal() {
        modalContent.classList.remove("show");
        setTimeout(() => {
          installModal.style.display = "none";
        }, 350);
      }

      closeInstall.onclick = closeModal;

      modalContent.innerHTML = "";

      modalContent.innerHTML = "<p>Instalá la APP aquí:</p>";

      let button = document.createElement("button");
      let closeButton = closeInstall;

      button.textContent = "Como instalar";
      button.id = "installIphone";

      modalContent.appendChild(button);
      modalContent.appendChild(closeButton);

      button.addEventListener("click", function () {
        modalContent.innerHTML = "";
        modalContent.style.alignItems = "flex-start";
        modalContent.innerHTML = `<h2>COMO INSTALAR LA APP EN TU TELÉFONO</h2>
      <p>Al agregar el acceso directo en el inicio de su teléfono,
      podrá acceder al servicio como cualquier otra aplicación.</p>
      <p>1 - Toque en <strong>Menú</strong><img src="./img/instructivo_descarga-1.png"></p>
      <p>2 - Selecione <strong>Agregar a inicio</strong><img src="./img/instructivo_descarga-2.png"></p>
      <p>3 - En el inicio se creará un ícono con el acceso a la Aplicación</p>`;

        let closeButton = closeInstall;
        modalContent.appendChild(closeButton);
      });
    } else {
      let deferredPrompt; // Will hold the event to trigger the install prompt

      // Listen for the beforeinstallprompt event
      window.addEventListener("beforeinstallprompt", (e) => {
        // Prevent the default install prompt
        e.preventDefault();

        // Save the event so it can be triggered later
        deferredPrompt = e;

        installModal.style.display = "flex";
        setTimeout(() => {
          modalContent.classList.add("show");
        }, 10);

        function closeModal() {
          modalContent.classList.remove("show");
          setTimeout(() => {
            installModal.style.display = "none";
          }, 350);
        }

        closeInstall.onclick = closeModal;

        // Add an event listener to the install button
        installButton.addEventListener("click", () => {
          // Show the install prompt when the button is clicked
          deferredPrompt.prompt();

          // Wait for the user to respond to the prompt
          deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === "accepted") {
              console.log("User accepted the install prompt");
            } else {
              console.log("User dismissed the install prompt");
              closeModal();
            }
            // Reset the deferredPrompt variable, as the prompt has been shown
            deferredPrompt = null;
          });
        });
      });
    }
  }

  //=============================================>
  //SERVICE WORKER REGISTRATION
  //=============================================>
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker
      .register("sw.js")
      .then((registration) => {
        console.log("SW Registered", registration);
      })
      .catch((error) => {
        console.log("Error:", error);
      });
  }
});
