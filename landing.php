<?php
session_start();
if (isset($_SESSION['userId'])) {
  $userId = $_SESSION['userId'];
} else {
  // Redirect to login page if no token is found
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <link rel="stylesheet" href="landing.css" />
  <title>Responsive Navbar</title>
</head>
<div id="loader" class="loader"></div>
<div id="cover"></div>

<body>
  <header>
    <div class="logo">
      <div class="logo">
        <a href="/">
          <img src="img/logo.jpg" alt="Padel Pro-Florida Logo" class="logo-image" />
          <h1>Padel Pro-Florida</h1>
        </a>
      </div>
    </div>

    <nav class="menu">
      <ul>
        <li><a href="#profile" id="openProfile2"><svg width="23" height="23" viewBox="0 0 24 24" stroke="#03ff03"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none">
              <circle cx="12" cy="8" r="5" />
              <path d="M3,21 h18 C 21,12 3,12 3,21" />
            </svg></a></li>
        <li>
          <form action="logout.php" method="POST">
            <button type="submit" name="logout" id="logout"><img src="./img/logout.png" alt="Logout"></button>
          </form>
        </li>
      </ul>
    </nav>
  </header>
  <main>
    <section class="Services" id="home">
      <div class="bento-grid">
        <div class="bento-item" id="openReserve">
          <img src="./img/resCancha.png" alt="Imagen 1" />
        </div>
        <h3>Canchas</h3>
        <div class="bento-item">
          <img src="./img/resClases.png" alt="Imagen 2">
        </div>
        <h3>Clases</h3>
        <div class="bento-item">
          <img src="./img/resEntrenar.png" alt="Imagen 3">
        </div>
        <h3>Entrenar</h3>
        <div class="bento-item">
          <img src="./img/resRivales.png" alt="Imagen 4">
        </div>
        <h3>Oponentes</h3>
        <div class="bento-item">
          <img src="./img/resChelada.png" alt="Imagen 5">
        </div>
        <h3>Cantina</h3>
        <div class="bento-item">
          <img src="./img/socios.png" alt="Imagen 6" />
        </div>
        <h3>Socios</h3>
        <div class="bento-item" id="openProfile">
          <img src="./img/perfil.png" alt="Imagen 7" />
        </div>
        <h3>Perfil</h3>
        <!-- Add more bento items as needed -->
      </div>
    </section>
    <div id="myModal" class="modal">
      <div class="modal-content">
        <span id="close" class="close">&times;</span>
        <div class="calendar" id="calendar"></div>
        <div class="court" id="court">
          <div class="court-img"><img src="./img/padel-court.jpg" /></div>
          <div class="court-hs" id="court-hs"></div>
        </div>
        <div class="practice" id="practice">
          <div class="practice-img">
            <img src="./img/padel-machine.avif" />
          </div>
          <div class="practice-hs" id="practice-hs"></div>
        </div>
        <div class="cantine" id="cantine">
          <div class="cantine-img">
            <img src="./img/padel-cantine.webp" />
          </div>
          <div class="cantine-hs" id="cantine-hs"></div>
        </div>
        <div class="buttons">
          <button id="closeReserve">Cancelar</button>
          <button id="acceptReserve">Aceptar</button>
        </div>
      </div>
    </div>
    <div id="myModal2" class="modal2">
      <div class="modal-content2">
        <span id="close2" class="close2">&times;</span>
        <div class="profile">
          <img src="./img/profile.png" alt="perfil" />
        </div>
        <div class="profile-detail">
          <div class="row">
            <h3>Nombre</h3>
            <span><?php echo $_SESSION['userNombre'] ?></span>
            <input type="text" value="Guillermo Nicrosi" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Cédula</h3>
            <span><?php echo $_SESSION['userCi'] ?></span>
          </div>
          <hr />
          <div class="row">
            <h3>Celular</h3>
            <span><?php
            function formatPhoneNumber($number)
            {
              $numStr = (string) $number;

              $formatted = '(' . substr($numStr, 0, 3) . ') ' . substr($numStr, 3, 2) . ' ' . substr($numStr, 5, 3) . ' ' . substr($numStr, 8);
              return $formatted;
            }
            $formattedNumber = formatPhoneNumber($_SESSION['userCel']);
            echo $formattedNumber;
            ?>
            </span>
            <input type="text" value="091234567" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Categoría</h3>
            <span><?php echo $_SESSION['userCategoria'] ?></span>
            <input type="text" value="Hector V. Guillén 904" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Fecha de nacimiento</h3>
            <span><?php echo $_SESSION['userFechNac'] ?></span>
            <input type="text" value="Hector V. Guillén 904" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Frase</h3>
            <span><?php echo $_SESSION['userFrase'] ?></span>
            <input type="text" value="Hector V. Guillén 904" class="hidden" />
          </div>
          <hr />
          <button id="editProfile">Editar</button>
          <button id="saveProfile" class="hidden">Guardar</button>
        </div>
      </div>
    </div>
  </main>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="landing.js"></script>

</html>