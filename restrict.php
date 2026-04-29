<?php
session_start();

if (empty($_SESSION['userId']) || $_SESSION['isAdmin'] !== "1") {
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="restrict.css" />
  <title>Administración</title>
</head>

<body>
  <header>
    <a href="landing.php" class="back-link" aria-label="Volver">
      <img src="img/return.png" alt="" class="logo-image" />
    </a>

    <nav class="date-nav">
      <button id="prevDay" aria-label="Día anterior">
        <img src="./img/izquierda.png" alt="" />
      </button>
      <button id="currentDate" class="date-display">Hoy</button>
      <button id="nextDay" aria-label="Día siguiente">
        <img src="./img/derecha.png" alt="" />
      </button>
    </nav>

    <input type="text" id="datePicker" aria-hidden="true" />
  </header>

  <div class="tabs" role="tablist">
    <button class="tab active" data-servicio="1" role="tab" aria-selected="true">Cancha 1</button>
    <button class="tab" data-servicio="6" role="tab" aria-selected="false">Cancha 2</button>
  </div>

  <main id="container" class="container"></main>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>const userId = <?php echo (int) $_SESSION['userId']; ?>;</script>
  <script src="restrict.js"></script>
</body>

</html>