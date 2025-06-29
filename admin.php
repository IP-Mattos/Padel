<?php
session_start();

if (isset($_SESSION['userId']) && $_SESSION['isAdmin'] === "1") {
  $userId = $_SESSION['userId'];
  $isadmin = $_SESSION['isAdmin'];
} else {
  // Redirect to login page if no token is found
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
  <link rel="stylesheet" href="admin.css" />
  <title>Administración</title>
</head>
<body>
    <header>
      <div class="logo">
        <a href="landing.php">
          <img src="img/return.png" alt="volver" class="logo-image" />
        </a>
      </div>
        <button id="prevDay"><img src="./img/izquierda.png" alt=""></button>
        <p id="currentDate" style="background-color: var(--primary-color); color: black; padding:10px 20px; border-radius: 8px;"><strong>Hoy</strong></p>
        <input type="text" id="datePicker" />
        <button id="nextDay"><img src="./img/derecha.png" alt=""></button>  
</header>

    <div id="container" class="container"></div>

</body>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="admin.js"></script>
<script>const userId = <?php echo $_SESSION['userId']; ?>;</script>
</html>