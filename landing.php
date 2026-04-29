<?php
session_start();

$dateFromSession = $_SESSION["userFechNac"];

$formattedDate = date('Y-m-d', strtotime($dateFromSession));

if (isset($_SESSION['userId'])) {
  $userId = $_SESSION['userId'];
  $userStars = $_SESSION['misEstrellas'];
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
  <title>Reservas</title>
</head>
<div id="loader" class="loader"></div>
<div id="cover"></div>

<body>
  <header>
    <div class="logo">
      <div class="logo">
        <a href="./index.php">
          <?php
          if ($_SESSION['soySocio'] > "0") {
            echo '<img src="img/logoSocio.gif" alt="Padel Pro-Florida Logo" class="logo-image" />';
          } else {
            echo '<img src="img/logo.jpg" alt="Padel Pro-Florida Logo" class="logo-image" />';
          }
          ?>
          <h1>INICIO</h1>
        </a>
      </div>
    </div>

    <nav class="menu">
      <ul>
        <?php if ($_SESSION['isAdmin'] === "1") { ?>
          <li><a id="admin-access" style="cursor: pointer;"><img style="width: 30px;" src="./img/llave.png"
                alt="admin"></a></li>
        <?php } ?>
        <li>
          <button id="openDeuda" class="deuda-btn"
            style="<?php echo $_SESSION['userDeuda'] != '0' ? '' : 'display:none;' ?>">
            Deuda $ <?php echo number_format($_SESSION['userDeuda'], 2, ',', '.'); ?>
          </button>
        </li>
        <li>
          <a id="openPoints" style="<?php echo $_SESSION['userDeuda'] == '0' ? '' : 'display:none;' ?>">
            <img style="width:30px;height:30px;" src="./img/puntos.png" alt="Puntos">
          </a>
        </li>
        <!-- <li>
          <?php if ($_SESSION['misEstrellas'] === "1") { ?>
            <img id="stars" style="width: 50px;" src="./img/1star.png" alt="1">
          <?php } ?>
          <?php if ($_SESSION['misEstrellas'] === "2") { ?>
            <img id="stars" style="width: 50px;" src="./img/2stars.png" alt="2">
          <?php } ?>
          <?php if ($_SESSION['misEstrellas'] === "3") { ?>
            <img id="stars" style="width: 50px;" src="./img/3stars.png" alt="3">
          <?php } ?>
        </li> -->
        <li><a id="openHours"><img style="width: 40px" id="partidoIcon" src="./img/reserva.png"></a></li>
        <?php if ($_SESSION['userImgPerfil'] != "") { ?>
          <li><a href="#profile" id="openProfile2"><img style="width: 30px; height: 30px; border-radius: 50%;"
                src="./accion/imgPerfilUser/<?php echo $_SESSION['userImgPerfil'] ?>" alt=""></a></li>
        <?php } else { ?>
          <li><a href="#profile" id="openProfile2"><img style="width: 30px; height: 30px; border-radius: 50%;"
                src="./img/profile.png" alt=""></a></li>
        <?php } ?>
      </ul>
    </nav>
  </header>
  <main>
    <section class="Services" id="home">
      <div class="bento-grid">
        <div class="bento-item" id="openCourt">
          <img src="./img/resCanchaNew.png" alt="Imagen 1" />
        </div>
        <h3>Reservas</h3>
        <div class="bento-item hidden" id="openTournament">
          <img src="./img/torneo.png" alt="Imagen 8" />
        </div>
        <!-- <h3>Cantina</h3> -->
        <div class="bento-item" id="openMembers" onclick="window.location.href = '/soySocio'">
          <img src="./img/sociosNew.png" alt="Imagen 6" />
        </div>
        <h3>Socios</h3>
        <h3 id="tournamentTitle" class="hidden">Torneos</h3>
        <div class="bento-item" id="openClasses">
          <img src="./img/resClasesNew.png" alt="Imagen 2">
        </div>
        <h3>Clases</h3>
        <div class="bento-item hidden" id="openTraining"><!-- escondido -->
          <img src="./img/resEntrenar.png" alt="Imagen 3"><!-- escondido -->
        </div><!-- escondido -->
        <!--<h3 class="hidden">Entrenar</h3> escondido -->
        <div class="bento-item" id="openRivals">
          <img src="./img/resRivales.png" alt="Imagen 4">
        </div>
        <h3>Oponentes</h3>
        <div class="bento-item hidden" id="openCantine">
          <img src="./img/resChelada.png" alt="Imagen 5">
        </div>
        <div class="bento-item" id="openProfile">
          <img src="./img/perfil.png" alt="Imagen 7" />
        </div>
        <h3>Perfil</h3>
        <!-- Add more bento items as needed -->
      </div>
    </section>
    <div id="versusModal" class="vModal">
      <div class="vModal-content">
        <span id="closeVersus" class="close">&times;</span>
        <h2>En busca de rivales</h2>
        <div id="match-container" class="match-container"></div>
      </div>
    </div>
    <div id="hoursModal" class="hModal">
      <div class="hModal-content">
        <h2>Tus reservas</h2>
        <span id="closeHours" class="close">&times;</span>
        <div class="reserve-cards" id="reserveContainer"></div>
      </div>
    </div>
    <div id="inviteModal" style="display: none;" class="iModal">
      <div class="iModal-content">
        <span id="closeInvite" class="close">&times;</span>
        <h2>Jugadores</h2>
        <ul id="inviteListUl"></ul>
      </div>
    </div>

    <div id="deudaModal" class="dModal">
      <div class="dModal-content">
        <span id="closeDeuda" class="close">&times;</span>
        <h2>Estado de cuenta</h2>
        <div id="deudaMovimientos"></div>
      </div>
    </div>

    <div id="slotInviteModal" class="aModal">
      <div class="aModal-content">
        <span id="closePlayers" class="close">&times;</span>
        <h3>Invitar jugador</h3>
        <input id="inviteSearch" placeholder="Nombre, cédula o teléfono..." autocomplete="off" />
        <div id="inviteResults"></div>
        <button id="addInviteBtn" disabled>Agregar</button>
      </div>
    </div>
    <button id="openVersus" class="float-vs"><img id="versusIcon" src="./img/vs.png" alt="VS"></button>
    <div id="courtModal" class="cModal">
      <div class="cModal-content">
        <img src="./img/resCanchaNew.png" alt="" class="service-ico">
        <span id="closeCourt" class="close">&times;</span>
        <h2>Reserva</h2>
        <div class="service-switch" id="court-service-switch"></div>

        <div class="calendar" id="court-calendar"></div>
        <div class="hs" id="court-hs"></div>
        <div class="buttons">
          <button id="acceptCourt">Confirmar</button>
        </div>
      </div>
    </div>
    <div id="classesModal" class="clModal">
      <div class="clModal-content">
        <img src="./img/resClases.png" alt="" class="service-ico">
        <span id="closeClasses" class="close">&times;</span>
        <div class="teachContainer" id="profList"></div>
        <div class="calendar" id="classes-calendar"></div>
        <div class="hs" id="class-hs"></div>
        <div class="buttons">
          <?php if ($_SESSION['profesor'] === "1") { ?>
            <button id="acceptClasses">Confirmar</button>
          <?php } else { ?>
            <button id="interested">Me interesa</button>
          <?php } ?>
        </div>
      </div>
    </div>
    <div id="trainingModal" class="tModal">
      <div class="tModal-content">
        <img src="./img/resEntrenar.png" alt="" class="service-ico">
        <span id="closeTraining" class="close">&times;</span>
        <h2>Reserva de entrenamiento</h2>
        <div class="calendar" id="training-calendar"></div>
        <div class="hs" id="training-hs"></div>
        <div class="buttons">
          <button id="acceptTraining">Confirmar</button>
        </div>
      </div>
    </div>
    <div id="rivalsModal" class="rModal">
      <div class="rModal-content">
        <img src="./img/resRivales.png" alt="" class="service-ico">
        <span id="closeRivals" class="close">&times;</span>
        <h2>Crear partido</h2>
        <div class="calendar" id="rivals-calendar"></div>
        <div class="hs" id="rivals-hs"></div>
        <div class="buttons">
          <button id="acceptRivals">Confirmar</button>
        </div>
      </div>
    </div>
    <div id="cantineModal" class="caModal">
      <div class="caModal-content">
        <img src="./img/resChelada.png" alt="" class="service-ico">
        <span id="closeCantine" class="close">&times;</span>
        <h2>Reservando cantina</h2>
        <div class="calendar" id="cantine-calendar">
          <p style="color: white;">Próximamente</p>
        </div>
        <div class="hs" id="cantine-hs"></div>
        <!-- <div class="buttons">
          <button id="acceptCantine">Confirmar</button>
        </div> -->
      </div>
    </div>
    <div id="membersModal" class="sModal">
      <div class="sModal-content">
        <img src="./img/socios.png" alt="" class="service-ico">
        <span id="closeMembers" class="close">&times;</span>

        <div class="buttons">
          <button id="acceptMembers">Confirmar</button>
        </div>
      </div>
    </div>
    <div id="tournamentModal" class="trModal">
      <div class="trModal-content">
        <span id="closeTournament" class="close">&times;</span>
        <h2>Torneos</h2>
        <div id="tournamentCards" class="tournament-cards"></div>
        <div class="buttons">
          <button id="acceptTournament" disabled>Inscribirme</button>
        </div>
      </div>
    </div>
    <div id="profileModal" class="pModal">
      <div class="pModal-content">
        <span id="closeProfile" class="close">&times;</span>
        <div class="profile">
          <label for="profileImgInput" style="position:relative;">
            <img class="edit-profile" src="./img/pencil.png" alt="">
            <div class="img">
              <?php if ($_SESSION['userImgPerfil'] != "") { ?>
                <img id="profileImg" src="./accion/imgPerfilUser/<?php echo $_SESSION['userImgPerfil'] ?>" alt="perfil" />
              <?php } else { ?>
                <img id="profileImg" src="./img/profile.png" alt="perfil" />
              <?php } ?>
            </div>
          </label>
          <input type="file" id="profileImgInput" style="display: none" accept="image/*">
          <?php if ($_SESSION['misEstrellas'] === "1") { ?>
            <img style="width: 100px; margin-top:10px;" src="./img/1star.png" alt="1">
          <?php } ?>
          <?php if ($_SESSION['misEstrellas'] === "2") { ?>
            <img style="width: 100px; margin-top:10px;" src="./img/2stars.png" alt="2">
          <?php } ?>
          <?php if ($_SESSION['misEstrellas'] === "3") { ?>
            <img style="width: 100px; margin-top:10px;" src="./img/3stars.png" alt="3">
          <?php } ?>
        </div>
        <form method="POST" action="./accion/savePerfil.php" id="updateProfile">
          <input type="text" name="idUser" value="<?php echo $_SESSION["userId"] ?>" hidden>
          <h3>Nombre</h3>
          <input type="text" name="nombre" value="<?php echo $_SESSION["userNombre"] ?>">
          <h3>Mail</h3>
          <input type="text" name="mail" value="<?php echo $_SESSION["userMail"] ?>">
          <h3>Nick</h3>
          <input type="text" name="usuario" value="<?php echo $_SESSION["userUser"] ?>">
          <h3>Categoría</h3>
          <!-- <input type="text" name="categoria" value="<?php echo $_SESSION["userCategoria"] ?>"> -->
          <select name="categoria">
            <option value="<?php echo $_SESSION["userCategoria"] ?>" selected><?php echo $_SESSION["userCategoria"] ?>
            </option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
          </select>
          <h3>Fecha de nacimiento</h3>
          <input type="date" name="fechnac" value="<?php echo $formattedDate ?>">
          <h3>Frase</h3>
          <input type="textarea" name="frase" value="<?php echo $_SESSION["userFrase"] ?>">
          <h3>Categorías en las que juego</h3>
          <ul class="radio-list">
            <li>
              <input type="radio" name="mascategorias" id="0" value="0" <?php if ($_SESSION["userMasCategoria"] == 0)
                echo 'checked' ?>>
                <label for="0">Misma categoría</label>
              </li>
              <li>
                <input type="radio" name="mascategorias" id="1" value="1" <?php if ($_SESSION["userMasCategoria"] == 1)
                echo 'checked' ?>>
                <label for="1">Categorías contiguas</label>
              </li>
              <li>
                <input type="radio" name="mascategorias" id="2" value="2" <?php if ($_SESSION["userMasCategoria"] == 2)
                echo 'checked' ?>>
                <label for="2">Todas las categorías</label>
              </li>
            </ul>
            <button id="updateButton">Actualizar</button>
          </form>
        </div>
      </div>
      <div id="pointsModal" class="ptModal">
        <div class="ptModal-content">
          <div class="points">
            <span id="closePoints" class="close">&times;</span>
            <img style="width:30px;height:30px;" src="./img/puntos.png" alt="Puntos">
            <p id="puntosValue"><b><?php echo ($_SESSION['userPuntos']); ?></b> 💪🥳
          </p>

          <input type="text" id="puntosInput" placeholder="Introduce puntos a canjear...">
          <button style="font-size:20px;" id="sendPoints">Canjear</button>
        </div>
      </div>
    </div>
  </main>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="landing.js?test"></script>
<script>
  const userId = <?php echo $_SESSION['userId']; ?>;
  const userStars = <?php echo $_SESSION['misEstrellas']; ?>;
  const userCategoria = <?php echo (int) $_SESSION['userCategoria']; ?>;
</script>
<script src="push-manager.js"></script>

</html>