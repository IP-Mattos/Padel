<?php
require_once "../api/utils.php";

$token = isset($_COOKIE['goCookToken']) ? $_COOKIE['goCookToken'] : "";

$estado = isset($_POST['estado']) ? trim($_POST['estado']) : "";
$fechaDesde = isset($_POST['fechaDesde']) ? trim($_POST['fechaDesde']) : "";
$fechaHasta = isset($_POST['fechaHasta']) ? trim($_POST['fechaHasta']) : "";

$ret = getTorneos($token, $estado, $fechaDesde, $fechaHasta);
?>