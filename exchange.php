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
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="exchange.css" />
    <title>Administración — Canjes</title>
</head>

<body>
    <header>
        <a href="landing.php" class="back-link" aria-label="Volver">
            <img src="img/return.png" alt="" class="logo-image" />
        </a>
        <span class="header-title">Canjes pendientes</span>
    </header>

    <main id="container" class="container"></main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="exchange.js"></script>
</body>

</html>