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
        <a href="/">
          <img src="img/logo.jpg" alt="Padel Pro-Florida Logo" class="logo-image" />
          <h1>INICIO</h1>
        </a>
      </div>
    </div>

    <nav class="menu">
      <ul>        
        <?php if($_SESSION['isAdmin'] === "1"){ ?>
        <li><a href="admin.php"><img style="width: 30px;" src="./img/llave.png" alt="admin"></a></li>
        <?php } ?>
        <li>
          <?php if($_SESSION['misEstrellas'] === "1"){ ?> 
          <img id="stars" style="width: 50px;" src="./img/1star.png" alt="1">
          <?php } ?>
          <?php if($_SESSION['misEstrellas'] === "2"){ ?> 
          <img id="stars" style="width: 50px;" src="./img/2stars.png" alt="2">
          <?php } ?>
          <?php if($_SESSION['misEstrellas'] === "3"){ ?> 
          <img id="stars" style="width: 50px;" src="./img/3stars.png" alt="3">
          <?php } ?>
        </li>
        <li><a id="openHours"><img style="width: 30px" src="./img/reserva.png"></a></li>
        <li><a href="#profile" id="openProfile2"><img style="width: 30px; border-radius: 50%;" src="./accion/imgPerfilUser/<?php echo $_SESSION['userImgPerfil'] ?>" alt=""></a></li>
      </ul>
    </nav>
  </header>
  <main>
    <section class="Services" id="home">
      <div class="bento-grid">
        <div class="bento-item" id="openCourt">
          <img src="./img/resCancha.png" alt="Imagen 1" />
        </div>
        <h3>Canchas</h3>
        <div class="bento-item" id="openClasses">
          <img src="./img/resClases.png" alt="Imagen 2">
        </div>
        <h3>Clases</h3>
        <div class="bento-item" id="openTraining">
          <img src="./img/resEntrenar.png" alt="Imagen 3">
        </div>
        <h3>Entrenar</h3>
        <div class="bento-item" id="openRivals">
          <img src="./img/resRivales.png" alt="Imagen 4">
        </div>
        <h3>Oponentes</h3>
        <div class="bento-item" id="openCantine">
          <img src="./img/resChelada.png" alt="Imagen 5">
        </div>
        <h3>Cantina</h3>
        <div class="bento-item" id="openMembers">
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
        <h2>Invitar usuarios</h2>
        <div class="searchContent">
          <input
            type="text"
            id="inviteSearch"
            placeholder="Buscar usuario..."
            autocomplete="off"
          />
          <select id="inviteDropdown"></select>
          <button id="addInviteBtn">Agregar</button>
          <div id="inviteList">
            <h4>Invitados</h4>
            <ul id="inviteListUl"></ul>
          </div>
        </div>
      </div>
    </div>
    <button id="openVersus" class="float-vs"><img id="versusIcon" src="./img/vs.png" alt="VS"></button>
    <div id="courtModal" class="cModal">
      <div class="cModal-content">
        <img src="./img/resCancha.png" alt="" class="service-ico">
        <span id="closeCourt" class="close">&times;</span>
        <h2>Reservando cancha</h2>
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
          <button id="acceptClasses">Confirmar</button>
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
      <h class="rModal-content">
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
        <div class="calendar" id="cantine-calendar"><p style="color: white;">Próximamente</p></div>
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
        <h1>¡Hazte socio y vive la experiencia!</h1>

  <p>En Familia Go Padel, vivir el deporte es solo el comienzo. Ser socio te da acceso a beneficios exclusivos que transforman cada partida en una experiencia única. Descubre por qué unirte es la mejor elección:</p>

  <h2>Ventajas de ser socio</h2>
  <ul>
    <li>Más juego, menos costo: Socios FULL disfrutan pádel sin costo de 7:00 a 17:00 y tarifas reducidas en otros horarios. Socios Amigo también obtienen descuentos significativos.</li>
    <li>Duchas gratuitas: Relájate después de jugar. Socios FULL disfrutan de este beneficio sin costo.</li>
    <li>Descuentos en productos: Pelotas, greps y más con hasta un 20% de descuento.</li>
    <li>Clases a precios especiales: Aprende y mejora con tarifas exclusivas para socios.</li>
  </ul>

  <h2>Comparativa de beneficios</h2>
  <table>
    <tr>
      <th>Beneficio</th>
      <th>Socio FULL</th>
      <th>Socio Amigo</th>
      <th>No socios</th>
    </tr>
    <tr>
      <td>Pádel 7:00 - 17:00</td>
      <td>Sin Costo</td>
      <td>$200</td>
      <td>$250</td>
    </tr>
    <tr>
      <td>Pádel 17:00 - 21:00</td>
      <td>$150</td>
      <td>$200</td>
      <td>$250</td>
    </tr>
    <tr>
      <td>Pádel 22:00 - 24:00</td>
      <td>$150</td>
      <td>$200</td>
      <td>$250</td>
    </tr>
    <tr>
      <td>Duchas</td>
      <td>Sin Costo</td>
      <td>$50</td>
      <td>$50</td>
    </tr>
    <tr>
      <td>Clases Principiante</td>
      <td>$100</td>
      <td>$150</td>
      <td>$200</td>
    </tr>
    <tr>
      <td>Clases Particulares</td>
      <td>$200</td>
      <td>$300</td>
      <td>$350</td>
    </tr>
    <tr>
      <td>Productos</td>
      <td>20% OFF</td>
      <td>10% OFF</td>
      <td>0% OFF</td>
    </tr>
  </table>

  <h2>Únete hoy</h2>
  <p>Ser socio te conecta con la magia del deporte, la naturaleza y una comunidad vibrante. Relájate bajo las estrellas, disfruta de música en vivo y crea recuerdos únicos. ¡Hazte parte de Familia Go Padel y vive la diferencia!</p>           
        <div class="buttons">
          <button id="acceptMembers">Confirmar</button>
        </div>
      </div>
    </div>
    <div id="profileModal" class="pModal">
      <div class="pModal-content">
        <span id="closeProfile" class="close">&times;</span>
        <div class="profile">
          <label for="profileImgInput">
            <div class="img">
              <img id="profileImg" src="./accion/imgPerfilUser/<?php echo $_SESSION['userImgPerfil'] ?>" alt="perfil" />
            </div>
          </label>
          <input type="file" id="profileImgInput" style="display: none" accept="image/*">
          <?php if($_SESSION['misEstrellas'] === "1"){ ?> 
          <img style="width: 100px; margin-top:10px;" src="./img/1star.png" alt="1">
          <?php } ?>
          <?php if($_SESSION['misEstrellas'] === "2"){ ?> 
          <img style="width: 100px; margin-top:10px;" src="./img/2stars.png" alt="2">
          <?php } ?>
          <?php if($_SESSION['misEstrellas'] === "3"){ ?> 
          <img style="width: 100px; margin-top:10px;" src="./img/3stars.png" alt="3">
          <?php } ?>
        </div>
        <div class="profile-detail" id="profileDetail">
          <div class="row">
            <h3>Nombre</h3>
            <span id="profileNombre"><?php echo $_SESSION['userNombre'] ?></span>
          </div>
          <hr />
          <div class="row">
            <h3>Nick</h3>
            <span id="profileNick"><?php echo $_SESSION["userUser"] ?></span>
          </div>
          <hr />
          <div class="row">
            <h3>Mail</h3>
            <span id="profileMail"><?php echo $_SESSION["userMail"] ?></span>
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
          </div>
          <hr />
          <div class="row">
            <h3>Categoría</h3>
            <span id="profileCategory"><?php echo $_SESSION['userCategoria'] ?></span>
          </div>
          <hr />
          <div class="row">
            <h3>Fecha de nacimiento</h3>
            <span id="profileBirth"><?php echo $_SESSION['userFechNac'] ?></span>
          </div>
          <hr />
          <div class="row">
            <h3>Frase</h3>
            <span id="profileFrase"><?php echo $_SESSION['userFrase'] ?></span>
          </div>
          <hr />
          <div class="row">
            <h3>Categorías en las que juego</h3>
            <?php
            if ($_SESSION["userMasCategoria"] == 0) {
              echo '<span id="profileMasCat">Misma categoría</span>';
            } else if ($_SESSION["userMasCategoria"] == 1) {
              echo '<span id="profileMasCat">Categorías contiguas</span>';
            } else {
              echo '<span id="profileMasCat">Todas las categorías</span>';
            }
            ?>
          </div>
          <hr />
          <button id="editProfile">Editar</button>
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
          <input type="text" name="categoria" value="<?php echo $_SESSION["userCategoria"] ?>">
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
            <button id="cancelUpdate">Cancelar</button>
          </form>
        </div>
      </div>
    </main>
  </body>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="landing.js"></script>
  <script>
    const userId = <?php echo $_SESSION['userId']; ?>;
    const userStars = <?php echo $_SESSION['misEstrellas']; ?>
</script>

</html>