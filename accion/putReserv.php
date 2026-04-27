<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require_once "../api/utils.php";

$fechHora = date('Y-m-d H:i:s');

$codigoError = "1";



$token = $_COOKIE['goCookToken'];

$fecha = $_POST['fecha'];

$servicio = $_POST['servicio'];

$profe = $_POST['profe'];

$usuario = $_POST['usuario'];

$hora = $_POST['hora'];







$ret = putReservHoras($token, $fecha, $servicio, $profe, $usuario, $hora);

$meses = [
       1 => 'enero',
       'febrero',
       'marzo',
       'abril',
       'mayo',
       'junio',
       'julio',
       'agosto',
       'septiembre',
       'octubre',
       'noviembre',
       'diciembre'
];

$ts = strtotime($fecha);
$mes = $meses[(int) date('n', $ts)];
$fechaF = date('j', $ts) . ' de ' . $mes;
sendPushToUser(193, "🎾 GO Padel", $_SESSION['userNombre'] . " ha reservado para el " . $fechaF . " a las " . substr($hora, 0, 5) . ".", '/admin.php');

?>