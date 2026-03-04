<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $idCanje = $_POST['idCanje'];


              
       $ret = putCanjeCancel($token,$idCanje,$_SESSION['userId']);
             

?>