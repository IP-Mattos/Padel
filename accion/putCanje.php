<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $puntos = $_POST['puntos'];


              
       $ret = putCanje($token,$puntos,$_SESSION['userId']);
             
?>