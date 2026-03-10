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
    <title>Administración</title>
</head>

<body>
    <header>
        <div class="logo">
            <a href="landing.php">
                <img src="img/return.png" alt="volver" class="logo-image" />
            </a>
        </div>
    </header>
</body>
<script src="deuda.js"></script>

</html>