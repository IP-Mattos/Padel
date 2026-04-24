<?php
session_start();

if (isset($_SESSION['userId']) && $_SESSION['isAdmin'] === "1") {
  $userId = $_SESSION['userId'];
} else {
  header("Location: landing.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="admin.css" />
  <title>Administración — GO Padel</title>
</head>

<body>

  <header>
    <a href="landing.php" class="back-btn">
      <img src="img/return.png" alt="Volver" />
    </a>
    <div class="date-nav">
      <button id="prevDay" class="nav-btn" aria-label="Día anterior">
        <img src="./img/izquierda.png" alt="" />
      </button>
      <button id="currentDate">Hoy</button>
      <input type="text" id="datePicker" />
      <button id="nextDay" class="nav-btn" aria-label="Día siguiente">
        <img src="./img/derecha.png" alt="" />
      </button>
    </div>
    <div class="tabs">
      <button id="tab-normal" class="tab active">Cancha 1</button>
      <button id="tab-serv6" class="tab">Cancha 2</button>
    </div>
  </header>

  <main>
    <div id="container" class="container"></div>
  </main>

  <!-- Payments Modal -->
  <div id="paymentsModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-content">
      <button class="close-modal" aria-label="Cerrar">&times;</button>
      <h2>Registrar Pagos</h2>
      <form id="paymentsForm">
        <div id="paymentRows"></div>
        <button type="submit" class="save-btn">Guardar Pagos</button>
      </form>
    </div>
  </div>

  <!-- User Search Modal -->
  <div id="userSearchModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-content">
      <button class="close-search-modal" aria-label="Cerrar">&times;</button>
      <h2>Buscar Usuario</h2>
      <input type="text" id="userSearchInput" placeholder="Nombre, cédula o teléfono…" autocomplete="off" />
      <div id="userSearchResults"></div>
      <button id="confirmUserSelect" class="save-btn" disabled>Confirmar selección</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="admin.js"></script>
  <script>const userId = <?php echo (int) $_SESSION['userId']; ?>;</script>

</body>

</html>