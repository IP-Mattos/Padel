<?php
session_start();
//display error

    require_once "./../api/utils.php";

    $token = $_COOKIE['goCookToken'];
    $idUsuario = $_POST['idUsuario'];
    $monto = $_POST['monto'];
    $origen = $_POST['origen'];
    $detalle = isset($_POST['detalle']) ? $_POST['detalle'] : "";

    // Validar datos requeridos
    if(empty($idUsuario) || empty($monto) || empty($origen)){
        $arrReturn = array();
        array_push($arrReturn, ["Status" => 400, "descrip" => 'Faltan datos requeridos']);
        $datos = json_encode($arrReturn);
        echo $datos;
        exit();
    }


    $ret = putDeudaCobro($token, $idUsuario, $monto, $origen, $detalle);


?>