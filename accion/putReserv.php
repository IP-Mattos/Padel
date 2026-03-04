<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $fecha =$_POST['fecha'];
       $servicio = $_POST['servicio'];
       $profe = $_POST['profe'];
       $usuario = $_POST['usuario'];
       $hora = $_POST['hora'];


              
       $ret = putReservHoras($token,$fecha,$servicio,$profe,$usuario,$hora);
             
?>