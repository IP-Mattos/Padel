<?php
session_start();

if (isset($_SESSION['userId']) && $_SESSION['userId'] == true) {
  $loggedIn = true;
} else {
  if ($_COOKIE['goCookToken'] != "") {
    $token = $_COOKIE['goCookToken'];
    header('location:./accion/loginUserToken.php');
  } else {
    $token = $_COOKIE['goCookToken'];
    $loggedIn = false;
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="app-version" content="1.0.2">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <link rel="manifest" href="manifest.json">
  <link rel="apple-touch-icon" href="favicon.ico">
  <link rel="stylesheet" href="style.css" />
  <title>GO Padel</title>
  <style>
    #floating-image {
      position: fixed;
      top: 200%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 9999;
      animation: fadeIn 1s ease-in-out;
    }

    #floating-image img {
      width: 300px;
      height: auto;
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
      border-radius: 8px;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.8);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .youtube-button {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #1f2937;
      position: relative;
    }

    .youtube-icon {
      transition: color 0.3s ease;
    }

    .youtube-button:hover .youtube-icon {
      color: #ff0000;
    }

    .live-badge {
      background-color: #ff0000;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: bold;
      animation: pulse 1.5s infinite;
      white-space: nowrap;
    }

    .live-badge.hidden {
      display: none;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.6;
      }
    }

    .menu ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 12px;
    }

    .menu li {
      margin: 0;
      padding: 0;
    }

    .menu li a {
      padding: 8px;
    }

    .menu a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 8px;
      transition: background-color 0.3s ease, transform 0.2s ease;
      color: #1f2937;
      text-decoration: none;
    }

    .menu a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: scale(1.05);
    }

    .menu svg {
      width: 24px;
      height: 24px;
    }

    @media (max-width: 768px) {
      .menu a {
        width: auto;
        height: auto;
        padding: 12px 16px;
        justify-content: flex-start;
        border-radius: 10px;
      }

      .menu svg,
      .youtube-icon {
        width: 22px;
        height: 22px;
      }

      nav.menu {
        background-color: #121317 !important;
      }

      nav.menu ul li a {
        color: #edf2fa !important;
      }

      nav.menu ul li a:hover {
        background-color: rgba(255, 255, 255, 0.08);
      }
    }
  </style>
  <script>
    window.addEventListener('load', () => {
      const img = document.getElementById('floating-image');
      setTimeout(() => {
        img.style.display = 'none';
      }, 5000); // Oculta después de 5 segundos

      // Verificar estado de transmisión en vivo de YouTube
      checkYouTubeLiveStatus();
    });

    function checkYouTubeLiveStatus() {
      // Esta función verifica si hay transmisión en vivo
      // Puedes reemplazar con una llamada a tu servidor que verifique la API de YouTube
      fetch('./api/checkYouTubeLive.php')
        .then(response => response.json())
        .then(data => {
          if (data.isLive) {
            document.getElementById('live-badge').classList.remove('hidden');
          }
        })
        .catch(error => console.log('No se pudo verificar estado en vivo'));
    }
  </script>


</head>
<div id="loader" class="loader"></div>
<div id="loader2" class="loader2-container">
  <div class="loader2"></div>
</div>
<div id="cover"></div>

<body>
  <header>
    <!-- mostrar imagenes como promos -->
    <div id="floating-image">
      <!-- <img src="./img/promo.png" alt="Promo Activa"> -->
    </div>

    <div class="logo">
      <div class="logo">
        <a href="#home">
          <img src="img/logo.jpg" alt="Padel Pro-Florida Logo" class="logo-image" />
          <h1>BIENVENIDO A GO PADEL</h1>
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


      <a id="landing" href="/irLanding.php" class="<?php if (!$loggedIn) {
        echo 'hidden';
      } ?>">
        <?php if ($_SESSION['userImgPerfil'] !== "") { ?>
          <img style="width: 30px; height:30px; border-radius: 50%;"
            src="./accion/imgPerfilUser/<?php echo $_SESSION['userImgPerfil'] ?>" alt="">
        <?php } else { ?>
          <img style="width: 30px; height:30px; border-radius: 50%;" src="./img/profile.png" alt="">
        <?php } ?>
      </a>
    </div>

    <input class="hidden" type="checkbox" id="menu-toggle" />

    <nav class="menu">
      <ul>
        <li><a href="#home" title="Inicio">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
            </svg>
          </a></li>
        <li><a href="#services" title="Servicios">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11.083 5.104c.35-.8 1.485-.8 1.834 0l1.752 4.022a1 1 0 0 0 .84.597l4.463.342c.9.069 1.255 1.2.556 1.771l-3.33 2.723a1 1 0 0 0-.337 1.016l1.03 4.119c.214.858-.71 1.552-1.474 1.106l-3.913-2.281a1 1 0 0 0-1.008 0L7.583 20.8c-.764.446-1.688-.248-1.474-1.106l1.03-4.119A1 1 0 0 0 6.8 14.56l-3.33-2.723c-.698-.571-.342-1.702.557-1.771l4.462-.342a1 1 0 0 0 .84-.597l1.753-4.022Z" />
            </svg>
          </a></li>
        <li><a href="#contact" title="Contactos">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21.6 12a1 1 0 0 1-.093.583L19.2 18.35A1 1 0 0 1 18.23 19H5.77a1 1 0 0 1-.97-.65L2.493 12.583A1 1 0 0 1 2.4 12a9.6 9.6 0 0 1 19.2 0Z" />
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 6h.01M17 6h.01" />
            </svg>
          </a></li>
        <li><a id="youtube-btn" href="http://www.youtube.com/@GoPadelFlorida" target="_blank" class="youtube-button"
            title="YouTube">
            <svg class="youtube-icon" width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
            </svg>
          </a></li>
        <li class="reservas <?php if (!$loggedIn)
          echo 'hidden' ?>"><a href="/landing.php" title="Reservas">
              <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 4v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1Z" />
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 2v2m4-2v2m4-2v2M3 10h18" />
              </svg>
            </a></li>
          <li class="ingresar <?php if ($loggedIn)
          echo 'hidden' ?>"><a href="#login" id="openModal2" title="Ingresar">
              <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7h.01M12 7h.01M8 7h.01M16 11h.01M12 11h.01M8 11h.01M16 15h.01M12 15h.01M8 15h.01" />
              </svg>
            </a></li>
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

      <!-- <section class="about" id="about">
        <h2>Sobre Nosotros</h2>
        <div class="about_us">
          <img src="img/Padel.svg" alt="Svg de Padel" />
          <p>
            Imagina un rincón especial donde la adrenalina del deporte se mezcla con la tranquilidad de un entorno natural. Este lugar cuenta con modernas canchas de pádel rodeadas de árboles frondosos, creando un ambiente fresco y relajante para practicar tu deporte favorito al aire libre. Aquí, cada golpe se siente diferente.
          </p>
          <br>
          <p>
            Después de un emocionante juego, puedes dirigirte al área social, un espacio para la convivencia y la relajación. Bajo una iluminación cálida que cuelga entre ramas, se encuentran mesas de madera rústica donde amigos, familia y amantes del deporte se reúnen para compartir momentos únicos. 
            La experiencia no sería completa sin la música. Sesiones de música llenan el ambiente de energía y emoción, creando el escenario ideal para relajarte después del deporte o simplemente disfrutar de una velada bajo las estrellas.
          </p>
          <br>
          <p>
            Este lugar no es solo para deportistas, sino para toda persona que busque desconectar, conectar y celebrar. Es un refugio donde el esfuerzo físico se transforma en camaradería, y donde cada noche termina con risas, brindis y buenos recuerdos. Ven a nuestro oasis de bienestar y diversión. ¡Ven y descubre la magia de practicar pádel mientras compartes la vida con quienes más importan!
          </p>
        </div>
      </section> -->

      <section class="Services" id="services">
        <h2>Nuestro Servicio</h2>
        <div class="bento-grid">
          <div class="bento-item">
            <img src="./img/reserva.png" alt="Imagen 1" />
            <div class="content">
              <h3>Alquiler de cancha / Reservas de horas</h3>
              <p>
                ¡Únete a nuestro grupo selecto de amantes del pádel y disfruta de beneficios exclusivos!
                <span class="more-text">
                  Regístrate ahora para reservar tus horas en nuestras canchas y accede a promociones especiales, eventos
                  privados y la oportunidad de compartir tu pasión por el deporte con una comunidad vibrante. No pierdas
                  la oportunidad de asegurar tu lugar y vivir una experiencia única. ¡La competencia y diversión te
                  esperan!
                </span>
                <button class="read-more-btn">Leer más</button>
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/practica.png" alt="Imagen 2" />
            <div class="content">
              <h3>Clases y prácticas</h3>
              <p>
                Clases personalizadas o grupales de pádel son ideales para quienes buscan mejorar su técnica de manera
                específica y efectiva.
                <span class="more-text">
                  En sesiones individuales, el entrenador se concentra en las necesidades particulares de cada persona,
                  ajustando los ejercicios a su nivel y objetivos. Por otro lado, las clases grupales no solo permiten
                  trabajar habilidades en conjunto, sino que también fomentan la camaradería y la competencia amistosa.
                  Cada sesión está diseñada para abordar aspectos cruciales del juego, desde el saque hasta las
                  estrategias defensivas y ofensivas, permitiendo que cada participante alcance su máximo potencial
                  mientras disfruta del aprendizaje en compañía.
                </span>
                <button class="read-more-btn">Leer más</button>
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/clases.png" alt="Imagen 3" />
            <div class="content">
              <h3>Escuela</h3>
              <p>
                Para niñas y niños mayores de 10 años, nuestra escuelita de pádel ofrece un espacio dinámico donde
                aprender y divertirse mientras desarrollan habilidades deportivas.
                <span class="more-text">
                  Con entrenadores dedicados, los estudiantes perfeccionan su técnica en un ambiente seguro y amigable,
                  fomentando trabajo en equipo, disciplina y amor por el deporte.
                </span>
                <button class="read-more-btn">Leer más</button>
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/chelada.png" alt="Imagen 4" />
            <div class="content">
              <h3>Chelada</h3>
              <p>
                Diseñado para ofrecer una experiencia sensorial inolvidable, combina naturaleza y sabor en un entorno
                mágico.
                <span class="more-text">
                  Rodeado de árboles frondosos y flores coloridas, cuenta con mesas rústicas de madera dispuestas bajo
                  luces cálidas que cuelgan entre las ramas. Aquí se sirven cervezas artesanales elaboradas localmente,
                  cada una con una historia y un toque especial que invita a la exploración. Música, áreas para juegos al
                  aire libre y opciones gastronómicas y variedad en cervezas. Un lugar para conectar, relajarse y celebrar
                  la vida.
                </span>
                <button class="read-more-btn">Leer más</button>
              </p>
            </div>
          </div>
          <div class="bento-item">
            <img src="./img/familia.png" alt="Imagen 5" />
            <div class="content">
              <h3>Familia GO Padel</h3>
              <p>
                En nuestra casa, el espíritu deportivo no solo se vive, sino que se respira.
                <span class="more-text">
                  Este espacio está diseñado para ser mucho más que un lugar de práctica; es un refugio para quienes
                  comparten una pasión auténtica por el pádel y buscan un entorno donde la competencia se transforme en
                  camaradería y el esfuerzo en celebración. Aquí, cada socio encuentra una comunidad que inspira, que
                  conecta y que hace que cada momento sea especial.
                </span>
                <button class="read-more-btn">Leer más</button>
              </p>
            </div>
          </div>
          <!-- Add more bento items as needed -->
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

    <!-- incluir versionControl script -->
    <script src="versionControl.js"></script>
    <!-- fin versionControl script -->

    <script src="index.js"></script>
    <script>
      const loggedIn = <?php if ($loggedIn) {
          echo $loggedIn;
        } else {
          echo 0;
        }
        ; ?>
  </script>
</body>

</html>