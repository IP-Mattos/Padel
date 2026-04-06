<?php
require_once "../api/utils.php";

$token = isset($_COOKIE['goCookToken']) ? $_COOKIE['goCookToken'] : "";
$idTorneo = isset($_POST['idTorneo']) ? trim($_POST['idTorneo']) : "";

if ($idTorneo === "") {
    $arrReturn = array();
    array_push($arrReturn, ["Status" => 400, "descrip" => "Falta idTorneo"]);
    echo json_encode($arrReturn);
    exit();
}

$ret = getTorneoAspirantes($token, $idTorneo);
?>