<?php
session_start();

if (isset($_SESSION['userId']) && $_SESSION['userId'] == true) {
  $loggedIn = true;
} else {
  $loggedIn = false;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <link rel="manifest" href="manifest.json">
  <link rel="apple-touch-icon" href="favicon.ico">
  <link rel="stylesheet" href="style.css" />
  <title>GO Padel</title>
</head>
<div id="loader" class="loader"></div>
<div id="loader2" class="loader2-container">
  <div class="loader2"></div>
</div>
<div id="cover"></div>

<body>
  <header>
    <div class="logo">
      <div class="logo">
        <a href="#home">
          <img src="img/logo.jpg" alt="Padel Pro-Florida Logo" class="logo-image" />
          <h1>Padel Pro-Florida</h1>
        </a>
      </div>
      <label for="menu-toggle" class="menu-icon">
        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          width="24" height="24" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
        </svg>
      </label>
      <label for="login-toggle" class="<?php if ($loggedIn) {
        echo 'hidden';
      } else {
        echo 'login-icon';
      } ?>">
        <svg width="19" height="23" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" fill="none" id="openModal">
          <circle cx="12" cy="8" r="5" />
          <path d="M3,21 h18 C 21,12 3,12 3,21" />
        </svg>
      </label>
      <a id="landing" href="/landing.php" class="<?php if (!$loggedIn) {
        echo 'hidden';
      } ?>">
        <svg width="19" height="23" viewBox="0 0 24 24" stroke="#03ff03" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" fill="none">
          <circle cx="12" cy="8" r="5" />
          <path d="M3,21 h18 C 21,12 3,12 3,21" />
        </svg>
      </a>
    </div>

    <input class="hidden" type="checkbox" id="menu-toggle" />

    <nav class="menu">
      <ul>
        <li><a href="#home">Inicio</a></li>
        <li><a href="#services">Servicios</a></li>
        <li><a href="#about">Sobre Nosotros</a></li>
        <li><a href="#contact">Contactos</a></li>
        <li class="reservas <?php if (!$loggedIn)
          echo 'hidden' ?>"><a href="/landing.php">Reservas</a></li>
          <li class="ingresar <?php if ($loggedIn)
          echo 'hidden' ?>"><a href="#login" id="openModal2">Ingresar</a></li>

        </ul>
      </nav>
    </header>

    <main>
      <section class="slider" id="home">
        <div class="list">
          <div class="item active">
            <div class="image" style="--url: url('img/image-1.jpg')"></div>
            <div class="content">
              <img src="/img/logot1.png" alt="">
            </div>
          </div>
          <div class="item">
            <div class="image" style="--url: url('img/image-2.jpg')"></div>
            <div class="content">
              <img src="/img/logot1.png" alt="">
            </div>
          </div>
          <div class="item">
            <div class="image" style="--url: url('img/image-3.jpg')"></div>
            <div class="content">
              <img src="/img/logot1.png" alt="">
            </div>
          </div>
        </div>
        <div class="arrows">
          <button id="prev">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 12h14M5 12l4-4m-4 4 4 4" />
            </svg>
          </button>
          <button id="next">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 12H5m14 0-4 4m4-4-4-4" />
            </svg>
          </button>
        </div>
      </section>

      <div id="myModal" class="modal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <div id="modalBody">
            <h3>Bienvenido a GO Padel!</h3>
            <div id="formOne" class="hidden">
              <h2>Login</h2>
              <form id="loginForm" action="./accion/loginUserFast.php" method="POST">
                <label for="user">Cédula:</label>
                <input type="tel" id="cedula" name="cedula" maxlength="8" />
                <label for="password">Celular:</label>
                <input type="tel" id="celular" name="celular" maxlength="9" />
                <button id="submitPassword" type="submit">Envíar</button>
              </form>
            </div>
            <div id="formTwo" class="hidden">
              <h2>Registro</h2>
              <form id="registrationForm" action="./accion/putUser.php" method="POST">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="nombre" required />
                <label for="cedulaRegist">Cédula:</label>
                <input type="tel" id="cedulaRegist" name="cedula" maxlength="8" required />
                <label for="phoneRegist">Celular:</label>
                <input type="tel" id="phoneRegist" name="celular" maxlength="9" required />
                <button id="submitRegist" type="submit">Envíar</button>
              </form>
            </div>
          </div>
          <div id="buttonContainer">
            <button id="formOneButton">Acceder</button>
            <button id="formTwoButton">Registro</button>
          </div>
          <div id="returnButton" class="hidden">
            <button id="returnButtonAction" class="cancel">Cancelar</button>
          </div>
        </div>
      </div>

      <div id="loginModal" class="loginModal">
        <div class="loginModal-content">
          <form id="verifyForm" action="./accion/getValidateCodigo6.php" method="POST">
            <p>Ingrese el código de confirmación que recibió a WhatsApp:</p>
            <input type="tel" name="userInput" id="userInput" placeholder="□□□□□□" maxlength="6" required />
            <input type="hidden" id="cookie">
            <button id="verifyCode">Verificar</button>
            <button id="cancelLogin">Cancelar</button>
          </form>
        </div>
      </div>

      <div id="installModal" class="install-modal">
        <div class="imodal-content">
          <span id="closeInstall" class="close">&times;</span>
          <p>Instalá la APP aquí:</p>
          <button id="installButton">Instalar</button>
        </div>
      </div>

      <section class="Services" id="services">
        <h2>Nuestro Servicio</h2>
        <div class="bento-grid">
          <div class="bento-item">
            <img src="./img/image-5.jpg" alt="Imagen 1" />
            <div class="content">
              <h3>Alquiler de canchas</h3>
              <p>
                Disfrutá y divertite en nuestras canchas con amigos y familia!
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/image-7.jpg" alt="Imagen 2" />
            <div class="content">
              <h3>Clases particulares</h3>
              <p>
                Aprende de este hermoso deporte de la mano de nuestros
                profesores!
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/image-6.jpg" alt="Imagen 3" />
            <div class="content">
              <h3>Escuela y escuelita</h3>
              <p>
                Para los peques tambien hay lugar! Clases pensadas para chicos
                de hasta 12 años y para adolescentes de 13 a 18
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/image-5.jpg" alt="Imagen 4" />
            <div class="content">
              <h3>La chelada</h3>
              <p>
                Contamos con un espacio de cantina ideal para descansar y
                compartir con familia y amigos, con buena música y una cerveza
                bien fría para alentar a tu equipo!
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/image-4.jpg" alt="Imagen 5" />
            <div class="content">
              <h3>Familia GO Padel</h3>
              <p>
                Unite a nuestra familia y disfruta de beneficios y descuentos
                unicos!
              </p>
            </div>
          </div>
          <!-- Add more bento items as needed -->
        </div>
      </section>

      <section class="about" id="about">
        <h2>Sobre Nosotros</h2>
        <div class="about_us">
          <img src="img/Padel.svg" alt="Svg de Padel" />
          <p>
            Go Padel Florida impulsa la práctica del padel desde un ambiente
            familiar y ameno. Nuestra misión es promover este emocionante
            deporte, atendiendo a los ritmos y evolución de cada jugador,
            alcanzando su máximo potencial y asegurando un espacio de disfrute
            para toda la familia, incluso para los más chiquitos!
          </p>
        </div>
      </section>

      <!-- <section class="faq">
        <h2>Preguntas Frecuentes</h2>

        <button class="accordion">
          <h3>Pregunta 1</h3>
          <span class="accordion-icon">+</span>
        </button>
        <div class="panel">
          <p>respuesta 1</p>
        </div>

        <button class="accordion">
          <h3>Pregunta 2</h3>
          <span class="accordion-icon">+</span>
        </button>
        <div class="panel">
          <p>respuesta 2</p>
        </div>

        <button class="accordion">
          <h3>Pregunta 3</h3>
          <span class="accordion-icon">+</span>
        </button>
        <div class="panel">
          <p>respuesta 3</p>
        </div>
      </section> -->

      <section class="facilities">
        <h2>Nuestras Instalaciones</h2>
        <div class="facility-grid">
          <div class="facility-item">
            <img src="img/image-2.jpg" alt="Cancha de Pádel" />
            <h3>Canchas de Última Generación</h3>
          </div>
          <!-- Añade más items según sea necesario -->
        </div>
      </section>

      <section class="map">
        <h2>Encuéntranos Aquí</h2>
        <div class="mapa">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.5635198085356!2d-56.21132889999999!3d-34.0832717!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95a11d3d8d7fa78b%3A0xb41f99b31af84754!2sGoPadel%20Pro!5e1!3m2!1ses!2suy!4v1725075048231!5m2!1ses!2suy"
            width="600" height="450" style="border: 0" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </section>
      <footer id="contact">
        <div class="social-links">
          <h2>SIGUENOS EN</h2>
          <a href="https://www.facebook.com/people/Go-Padel-Florida/61564071791688/" target="_blank">
            <img src="./img/facebook.png" alt="fb">
          </a>
          <a href="https://www.instagram.com/gopadelflorida/" target="_blank">
            <img src="./img/instagram.png" alt="insta">
          </a>
        </div>
        <hr>
        <div class="copyright">
          <p>MCN Service © 2025</p>
        </div>
      </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="index.js"></script>
  </body>

  </html>