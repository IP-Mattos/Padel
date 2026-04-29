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
    <title>Administración — Deudas</title>
</head>

<body>
    <header>
        <a href="landing.php" class="logo-link" aria-label="Volver">
            <img src="img/return.png" alt="" class="logo-image" />
        </a>
    </header>

    <script src="deuda.js"></script>
</body>

</html>