<?php
session_start();

if (!isset($_SESSION['userId'])) {
  header("Location: index.php");
  exit();
}

$userId = $_SESSION['userId'];
$formattedDate = date('Y-m-d', strtotime($_SESSION["userFechNac"]));
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
      <a href="./index.php">
        <?php if ($_SESSION['soySocio'] > "0"): ?>
          <img src="img/logoSocio.gif" alt="Padel Pro-Florida Logo" class="logo-image" />
        <?php else: ?>
          <img src="img/logo.jpg" alt="Padel Pro-Florida Logo" class="logo-image" />
        <?php endif; ?>
        <h1>INICIO</h1>
      </a>
    </div>

    <nav class="menu">
      <ul>
        <?php if ($_SESSION['isAdmin'] === "1"): ?>
          <li><a id="admin-access" style="cursor:pointer;"><img style="width:28px;" src="./img/llave.png" alt="admin"></a>
          </li>
        <?php endif; ?>

        <li>
          <button id="openDeuda" class="deuda-btn" style="<?= $_SESSION['userDeuda'] != '0' ? '' : 'display:none;' ?>">
            Deuda $ <?= number_format($_SESSION['userDeuda'], 2, ',', '.') ?>
          </button>
        </li>

        <li>
          <a id="openPoints" style="<?= $_SESSION['userDeuda'] == '0' ? '' : 'display:none;' ?>">
            <img style="width:28px;height:28px;" src="./img/puntos.png" alt="Puntos">
          </a>
        </li>

        <li><a id="openHours"><img style="width:36px;" id="partidoIcon" src="./img/reserva.png"></a></li>

        <li>
          <a href="#profile" id="openProfile2">
            <?php if ($_SESSION['userImgPerfil'] != ""): ?>
              <img style="width:32px;height:32px;border-radius:50%;object-fit:cover;"
                src="./accion/imgPerfilUser/<?= $_SESSION['userImgPerfil'] ?>" alt="Perfil">
            <?php else: ?>
              <img style="width:32px;height:32px;border-radius:50%;" src="./img/profile.png" alt="Perfil">
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="Services" id="home">
      <div class="bento-grid">
        <div class="bento-item" id="openCourt">
          <img src="./img/resCanchaNew.png" alt="Reservas" />
        </div>
        <h3>Reservas</h3>

        <div class="bento-item hidden" id="openTournament">
          <img src="./img/torneo.png" alt="Torneos" />
        </div>
        <h3 id="tournamentTitle" class="hidden">Torneos</h3>

        <div class="bento-item" id="openMembers" onclick="window.location.href='/soySocio'">
          <img src="./img/sociosNew.png" alt="Socios" />
        </div>
        <h3>Socios</h3>

        <div class="bento-item" id="openClasses">
          <img src="./img/resClasesNew.png" alt="Clases">
        </div>
        <h3>Clases</h3>

        <div class="bento-item hidden" id="openTraining">
          <img src="./img/resEntrenar.png" alt="Entrenar">
        </div>

        <div class="bento-item" id="openRivals">
          <img src="./img/resRivales.png" alt="Oponentes">
        </div>
        <h3>Oponentes</h3>

        <div class="bento-item hidden" id="openCantine">
          <img src="./img/resChelada.png" alt="Cantina">
        </div>

        <div class="bento-item" id="openProfile">
          <img src="./img/perfil.png" alt="Perfil" />
        </div>
        <h3>Perfil</h3>
      </div>
    </section>

    <!-- Floating VS button -->
    <button id="openVersus" class="float-vs">
      <img id="versusIcon" src="./img/vs.png" alt="VS">
    </button>

    <!-- Versus Modal -->
    <div id="versusModal" class="vModal">
      <div class="vModal-content">
        <span id="closeVersus" class="close">&times;</span>
        <h2>En busca de rivales</h2>
        <div id="match-container" class="match-container"></div>
      </div>
    </div>

    <!-- Hours Modal -->
    <div id="hoursModal" class="hModal">
      <div class="hModal-content">
        <h2>Tus reservas</h2>
        <span id="closeHours" class="close">&times;</span>
        <div class="reserve-cards" id="reserveContainer"></div>
      </div>
    </div>

    <!-- Invite Modal -->
    <div id="inviteModal" class="iModal">
      <div class="iModal-content">
        <span id="closeInvite" class="close">&times;</span>
        <h2>Jugadores</h2>
        <ul id="inviteListUl"></ul>
      </div>
    </div>

    <!-- Slot Invite Modal -->
    <div id="slotInviteModal" class="aModal">
      <div class="aModal-content">
        <span id="closePlayers" class="close">&times;</span>
        <h3>Invitar jugador</h3>
        <input id="inviteSearch" placeholder="Nombre, cédula o teléfono..." autocomplete="off" />
        <div id="inviteResults"></div>
        <button id="addInviteBtn" disabled>Agregar</button>
      </div>
    </div>

    <!-- Deuda Modal -->
    <div id="deudaModal" class="dModal">
      <div class="dModal-content">
        <span id="closeDeuda" class="close">&times;</span>
        <h2>Estado de cuenta</h2>
        <div id="deudaMovimientos"></div>
      </div>
    </div>

    <!-- Court Modal -->
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

    <!-- Classes Modal -->
    <div id="classesModal" class="clModal">
      <div class="clModal-content">
        <img src="./img/resClases.png" alt="" class="service-ico">
        <span id="closeClasses" class="close">&times;</span>
        <div class="teachContainer" id="profList"></div>
        <div class="calendar" id="classes-calendar"></div>
        <div class="hs" id="class-hs"></div>
        <div class="buttons">
          <?php if ($_SESSION['profesor'] === "1"): ?>
            <button id="acceptClasses">Confirmar</button>
          <?php else: ?>
            <button id="interested">Me interesa</button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Training Modal -->
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

    <!-- Rivals Modal -->
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

    <!-- Cantine Modal -->
    <div id="cantineModal" class="caModal">
      <div class="caModal-content">
        <img src="./img/resChelada.png" alt="" class="service-ico">
        <span id="closeCantine" class="close">&times;</span>
        <h2>Reservando cantina</h2>
        <div class="calendar" id="cantine-calendar">
          <p style="color:white;">Próximamente</p>
        </div>
        <div class="hs" id="cantine-hs"></div>
      </div>
    </div>

    <!-- Members Modal -->
    <div id="membersModal" class="sModal">
      <div class="sModal-content">
        <img src="./img/socios.png" alt="" class="service-ico">
        <span id="closeMembers" class="close">&times;</span>
        <div class="buttons">
          <button id="acceptMembers">Confirmar</button>
        </div>
      </div>
    </div>

    <!-- Tournament Modal -->
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

    <!-- Profile Modal -->
    <div id="profileModal" class="pModal">
      <div class="pModal-content">
        <span id="closeProfile" class="close">&times;</span>
        <div class="profile">
          <label for="profileImgInput" style="position:relative;cursor:pointer;">
            <img class="edit-profile" src="./img/pencil.png" alt="Editar foto">
            <div class="img">
              <?php if ($_SESSION['userImgPerfil'] != ""): ?>
                <img id="profileImg" src="./accion/imgPerfilUser/<?= $_SESSION['userImgPerfil'] ?>" alt="perfil" />
              <?php else: ?>
                <img id="profileImg" src="./img/profile.png" alt="perfil" />
              <?php endif; ?>
            </div>
          </label>
          <input type="file" id="profileImgInput" style="display:none;" accept="image/*">

          <?php if ($_SESSION['misEstrellas'] === "1"): ?>
            <img style="width:100px;margin-top:10px;" src="./img/1star.png" alt="1 estrella">
          <?php elseif ($_SESSION['misEstrellas'] === "2"): ?>
            <img style="width:100px;margin-top:10px;" src="./img/2stars.png" alt="2 estrellas">
          <?php elseif ($_SESSION['misEstrellas'] === "3"): ?>
            <img style="width:100px;margin-top:10px;" src="./img/3stars.png" alt="3 estrellas">
          <?php endif; ?>
        </div>

        <form method="POST" action="./accion/savePerfil.php" id="updateProfile">
          <input type="hidden" name="idUser" value="<?= $_SESSION['userId'] ?>">

          <h3>Nombre</h3>
          <input type="text" name="nombre" value="<?= htmlspecialchars($_SESSION['userNombre']) ?>">

          <h3>Mail</h3>
          <input type="text" name="mail" value="<?= htmlspecialchars($_SESSION['userMail']) ?>">

          <h3>Nick</h3>
          <input type="text" name="usuario" value="<?= htmlspecialchars($_SESSION['userUser']) ?>">

          <h3>Categoría</h3>
          <select name="categoria">
            <option value="<?= $_SESSION['userCategoria'] ?>" selected><?= $_SESSION['userCategoria'] ?></option>
            <?php for ($i = 1; $i <= 8; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
          </select>

          <h3>Fecha de nacimiento</h3>
          <input type="date" name="fechnac" value="<?= $formattedDate ?>">

          <h3>Frase</h3>
          <input type="text" name="frase" value="<?= htmlspecialchars($_SESSION['userFrase']) ?>">

          <h3>Categorías en las que juego</h3>
          <ul class="radio-list">
            <?php
            $masOpts = [0 => "Misma categoría", 1 => "Categorías contiguas", 2 => "Todas las categorías"];
            foreach ($masOpts as $val => $label):
              ?>
              <li>
                <input type="radio" name="mascategorias" id="mascat<?= $val ?>" value="<?= $val ?>"
                  <?= $_SESSION['userMasCategoria'] == $val ? 'checked' : '' ?>>
                <label for="mascat<?= $val ?>"><?= $label ?></label>
              </li>
            <?php endforeach; ?>
          </ul>

          <button id="updateButton" type="submit">Actualizar</button>
        </form>
      </div>
    </div>

    <!-- Points Modal -->
    <div id="pointsModal" class="ptModal">
      <div class="ptModal-content">
        <span id="closePoints" class="close">&times;</span>
        <div class="points">
          <img style="width:36px;height:36px;" src="./img/puntos.png" alt="Puntos">
          <p id="puntosValue"><b><?= $_SESSION['userPuntos'] ?></b> 💪🥳</p>
          <input type="text" id="puntosInput" placeholder="Introduce puntos a canjear...">
          <button id="sendPoints">Canjear</button>
        </div>
      </div>
    </div>
  </main>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="landing.js"></script>
<script>
  const userId = <?= (int) $_SESSION['userId'] ?>;
  const userStars = <?= (int) $_SESSION['misEstrellas'] ?>;
  const userCategoria = <?= (int) $_SESSION['userCategoria'] ?>;
</script>
<script src="push-manager.js"></script>

</html>