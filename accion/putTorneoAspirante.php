<?php
session_start();
require_once "./../api/utils.php";

$token = isset($_COOKIE['goCookToken']) ? $_COOKIE['goCookToken'] : "";

$accion = isset($_POST['accion']) ? strtolower(trim($_POST['accion'])) : "insert";
$id = isset($_POST['id']) ? trim($_POST['id']) : "";
$idTorneo = isset($_POST['idTorneo']) ? trim($_POST['idTorneo']) : "";
$idUsuario = isset($_POST['idUsuario']) ? trim($_POST['idUsuario']) : "";
$estado = isset($_POST['estado']) ? trim($_POST['estado']) : "0";

if ($accion === "insert") {
    if ($idTorneo === "" || $idUsuario === "") {
        $arrReturn = array();
        array_push($arrReturn, ["Status" => 400, "descrip" => "Faltan datos requeridos: idTorneo, idUsuario"]);
        echo json_encode($arrReturn);
        exit();
    }

    $ret = putTorneoAspirante($token, "insert", $idTorneo, $idUsuario, 0, $id);
    exit();
}

if ($accion === "update") {
    if ($estado === "") {
        $arrReturn = array();
        array_push($arrReturn, ["Status" => 400, "descrip" => "Falta estado para actualizar"]);
        echo json_encode($arrReturn);
        exit();
    }

    if ($id === "" && ($idTorneo === "" || $idUsuario === "")) {
        $arrReturn = array();
        array_push($arrReturn, ["Status" => 400, "descrip" => "En update enviar id o idTorneo + idUsuario"]);
        echo json_encode($arrReturn);
        exit();
    }

    $ret = putTorneoAspirante($token, "update", $idTorneo, $idUsuario, $estado, $id);
    exit();
}

$arrReturn = array();
array_push($arrReturn, ["Status" => 400, "descrip" => "accion invalida. Use insert o update"]);
echo json_encode($arrReturn);
?>