<?php
if (isset($_COOKIE['goCookToken'])) {
  $token = $_COOKIE['goCookToken'];
} else {
  // Redirect to login page if no token is found
  header("Location: index.php");
  exit();
}

if (isset($_GET['acredit'])) {
  if ($_GET['acredit'] == 0) {
    header("Location: index.php");
  }
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
        <li><a href="#reserves" id="openReserve2">Reserva</a></li>
        <li><a href="#profile" id="openProfile2">Perfil</a></li>
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
      <h2>Bienvenido! Qué haremos hoy?</h2>
      <div class="bento-grid">
        <div class="bento-item" id="openProfile" style="cursor: pointer">
          <img src="./img/perfil.png" alt="Imagen 1" />
        </div>
        <div class="bento-item" id="openReserve" style="cursor: pointer">
          <img src="./img/reserva.png" alt="Imagen 2" />
        </div>
        <div class="bento-item" style="cursor: pointer;">
          <img src="./img/socios.png" alt="Imagen 3" />
        </div>
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
          <h2>Guillermo</h2>
        </div>
        <div class="profile-detail">
          <div class="row">
            <h3>Nombre</h3>
            <span>Guillermo Nicrosi</span>
            <input type="text" value="Guillermo Nicrosi" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Cédula</h3>
            <a>50143868</a>
          </div>
          <hr />
          <div class="row">
            <h3>Celular</h3>
            <span>091234567</span>
            <input type="text" value="091234567" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Dirección</h3>
            <span>Hector V. Guillén 904</span>
            <input type="text" value="Hector V. Guillén 904" class="hidden" />
          </div>
          <hr />
          <div class="row">
            <h3>Saldo</h3>
            <a>$2000</a>
          </div>
          <hr />
          <button id="editProfile">Editar</button>
          <button id="saveProfile" class="hidden">Guardar</button>
        </div>
        <button id="closeProfile">Cerrar</button>
      </div>
    </div>
  </main>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="landing.js"></script>

</html>