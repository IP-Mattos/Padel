<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $idReserva = $_POST['idReserv'];


              
       $ret = putReservHorasConfirm($token,$idReserva);
             
?>