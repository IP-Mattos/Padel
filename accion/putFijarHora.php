<?php
session_start();
//display error

    require_once "./../api/utils.php";

    $token = $_COOKIE['goCookToken'];
    $idUsuario = $_POST['idUsuario'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $servicio = $_POST['servicio'];
    $accion = $_POST['accion'];

    //echo "idUsuario: ". $idUsuario. "<br>" . "dia: ". $dia. "<br>". "hora: ". $hora. "<br>". "servicio: ". $servicio. "<br>". "accion: ". $accion;
    // Validar datos requeridos
    if(empty($idUsuario) || empty($dia) || empty($hora) || empty($servicio) || empty($accion)){
        $arrReturn = array();
        array_push($arrReturn, ["Status" => 400, "descrip" => 'Faltan datos requeridos']);
        $datos = json_encode($arrReturn);
        echo $datos;
        exit();
    }

        // Validar token


    $ret = putHoraFija($token, $idUsuario, $dia, $hora, $servicio, $accion);


?>