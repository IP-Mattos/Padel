<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $idReserva = $_POST['idReserva'];
       $idRival = $_POST['idRival'];
       $mensaje = $_POST['mensaje'];
       


              
       $ret = putReservVs($token,$idReserva,$idRival,$mensaje);
