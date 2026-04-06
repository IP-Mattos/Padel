<?php
session_start();
require_once "./../api/utils.php";

$token = isset($_COOKIE['goCookToken']) ? $_COOKIE['goCookToken'] : "";

$id = isset($_POST['id']) ? trim($_POST['id']) : "";
$categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : "";
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : "";
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : "";
$entre = isset($_POST['entre']) ? trim($_POST['entre']) : "0";
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : "0";

if ($categoria === "" || $fecha === "" || $nombre === "") {
    $arrReturn = array();
    array_push($arrReturn, ["Status" => 400, "descrip" => "Faltan datos requeridos: categoria, fecha, nombre"]);
    echo json_encode($arrReturn);
    exit();
}

$ret = putTorneos($token, $id, $categoria, $fecha, $nombre, $entre, $estado);
?>