<?php

       session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $idReserva = $_POST['idReserva'];
       $idInvitado = $_POST['idInvitado'];



              
       $ret = putCamcelInvitado($token,$idReserva,$idInvitado,$_SESSION['userId']);

?>