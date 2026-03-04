<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $fecha =$_POST['fecha'];
       $servicio = $_POST['servicio']; // se cambio al agregar 2 canchas no pido servicio es las horas de cancha
       $profe = 0; //$_POST['profe']; no precisa profesor
       $usuario = $_POST['usuario'];
       $hora = $_POST['hora'];


              
       $ret = putRestrictHoras($token,$fecha,$servicio,$profe,$usuario,$hora);
             
?>