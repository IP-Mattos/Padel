<?php
session_start();
require_once "./../api/utils.php";

$token = isset($_COOKIE['goCookToken']) ? $_COOKIE['goCookToken'] : "";

$id = isset($_POST['id']) ? trim($_POST['id']) : "";
$idTorneo = isset($_POST['idTorneo']) ? trim($_POST['idTorneo']) : "";
$idUsuario = isset($_POST['idUsuario']) ? trim($_POST['idUsuario']) : "";
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : "0";

    if ($idTorneo === "" || $idUsuario === "") {
        $arrReturn = array();
        array_push($arrReturn, ["Status" => 400, "descrip" => "Faltan datos requeridos: idTorneo, idUsuario"]);
        echo json_encode($arrReturn);
        exit();
    }

    $ret = putTorneoAspirante($token, $idTorneo, $idUsuario, $estado, $id);
    exit();

?>