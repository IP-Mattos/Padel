<?php
       require_once "../api/utils.php";
       
       //$token = $_POST['token'];
       $token = $_COOKIE['goCookToken'];
       
       $ret = getValidToken($token);
       //verifico cedula correcta
      //echo($ret);
    
       
?>