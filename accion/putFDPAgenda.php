<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $token = $_COOKIE['goCookToken'];
       $fecha = date('Y-m-d H:i:s');
       //echo "<br>".$fecha;
       $idAgenda = $_POST['idAgenda']; //no pido servicio es las horas de cancha
       $idUsuario = $_POST['idUsuario']; 
       $fdpUsuario = $_POST['fdpUsuario'];
       $idInvitado1 = $_POST['idInvitado1'];
       $fdpInvitado1 = $_POST['fdpInvitado1'];
       $idInvitado2 = $_POST['idInvitado2'];
       $fdpInvitado2 = $_POST['fdpInvitado2'];
       $idInvitado3 = $_POST['idInvitado3'];
       $fdpInvitado3 = $_POST['fdpInvitado3'];
       $impUsu = $_POST['impUsu'];
       $impInv1 = $_POST['impInv1'];
       $impInv2 = $_POST['impInv2'];
       $impInv3 = $_POST['impInv3'];

       //echo "<br>".$idAgenda."<br>".$idUsuario."<br>".$fdpUsuario."<br>".$idInvitado1."<br>".$fdpInvitado1."<br>".
       //$idInvitado2."<br>".$fdpInvitado2."<br>".$idInvitado3."<br>".$fdpInvitado3;
              
       $ret = putFDPAgenda($token,$fecha,$idAgenda,$idUsuario,$fdpUsuario,$idInvitado1,
       $fdpInvitado1,$idInvitado2,$fdpInvitado2,$idInvitado3,$fdpInvitado3,$impUsu,$impInv1,$impInv2,$impInv3);

?>