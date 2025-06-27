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
      <a id="landing" href="/landing.php" class="<?php if (!$loggedIn) {
        echo 'hidden';
      } ?>">
      <?php if($_SESSION['userImgPerfil'] !== "") { ?>
        <img style="width: 30px; height:30px; border-radius: 50%;" src="./accion/imgPerfilUser/<?php echo $_SESSION['userImgPerfil'] ?>" alt="">
      <?php }else{ ?>
        <img style="width: 30px; height:30px; border-radius: 50%;" src="./img/profile.png" alt="">
      <?php } ?>
      </a>
    </div>

    <input class="hidden" type="checkbox" id="menu-toggle" />

    <nav class="menu">
      <ul>
        <li><a href="#home">Inicio</a></li>
        <li><a href="#services">Servicios</a></li>
        <!-- <li><a href="#about">Sobre Nosotros</a></li> -->
        <li><a href="#contact">Contactos</a></li>
        <li class="reservas <?php if (!$loggedIn)
          echo 'hidden' ?>"><a href="/landing.php">Reservas</a></li>
          <li class="ingresar <?php if ($loggedIn)
          echo 'hidden' ?>"><a href="#login" id="openModal2">Ingresar</a></li>
          <?php if($loggedIn){ ?>
          <li>
            <form style="text-align: center;" action="logout.php" method="POST">
              <button type="submit" name="logout" id="logout">Salir</button>
            </form>
          </li>
            <?php }?>
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
                  Regístrate ahora para reservar tus horas en nuestras canchas y accede a promociones especiales, eventos privados y la oportunidad de compartir tu pasión por el deporte con una comunidad vibrante. No pierdas la oportunidad de asegurar tu lugar y vivir una experiencia única. ¡La competencia y diversión te esperan!
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
                Clases personalizadas o grupales de pádel son ideales para quienes buscan mejorar su técnica de manera específica y efectiva. 
                <span class="more-text">
                En sesiones individuales, el entrenador se concentra en las necesidades particulares de cada persona, ajustando los ejercicios a su nivel y objetivos. Por otro lado, las clases grupales no solo permiten trabajar habilidades en conjunto, sino que también fomentan la camaradería y la competencia amistosa. Cada sesión está diseñada para abordar aspectos cruciales del juego, desde el saque hasta las estrategias defensivas y ofensivas, permitiendo que cada participante alcance su máximo potencial mientras disfruta del aprendizaje en compañía.
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
                Para niñas y niños mayores de 10 años, nuestra escuelita de pádel ofrece un espacio dinámico donde aprender y divertirse mientras desarrollan habilidades deportivas. 
                <span class="more-text">
                Con entrenadores dedicados, los estudiantes perfeccionan su técnica en un ambiente seguro y amigable, fomentando trabajo en equipo, disciplina y amor por el deporte.
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
                Diseñado para ofrecer una experiencia sensorial inolvidable, combina naturaleza y sabor en un entorno mágico.
                <span class="more-text"> 
                Rodeado de árboles frondosos y flores coloridas, cuenta con mesas rústicas de madera dispuestas bajo luces cálidas que cuelgan entre las ramas. Aquí se sirven cervezas artesanales elaboradas localmente, cada una con una historia y un toque especial que invita a la exploración. Música, áreas para juegos al aire libre y opciones gastronómicas y variedad en cervezas. Un lugar para conectar, relajarse y celebrar la vida.
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
                Este espacio está diseñado para ser mucho más que un lugar de práctica; es un refugio para quienes comparten una pasión auténtica por el pádel y buscan un entorno donde la competencia se transforme en camaradería y el esfuerzo en celebración. Aquí, cada socio encuentra una comunidad que inspira, que conecta y que hace que cada momento sea especial.
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
    <script src="index.js"></script>
    <script>
      const loggedIn = <?php if($loggedIn){
        echo $loggedIn;
      }else{
        echo 0;
      }; ?>
    </script>
  </body>

  </html>