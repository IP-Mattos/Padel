<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require_once "../api/utils.php";
require_once "../api/clases/agenda.php";
$fechHora = date('Y-m-d H:i:s');
$codigoError = "1";

$token = $_COOKIE['goCookToken'];
$idReserva = $_POST['idReserv'];

$arr = array();

$agenda = Agenda::buscarPorId($idReserva);
if ($agenda != null) {
       //store all ids in the reserve into the array, the agenda class has the getters, getInvitado1, getInvitado2, and getInvitado3
       if ($agenda->getInvitado1() != 0) {
              array_push($arr, $agenda->getInvitado1());
       }
       if ($agenda->getInvitado2() != 0) {
              array_push($arr, $agenda->getInvitado2());
       }
       if ($agenda->getInvitado3() != 0) {
              array_push($arr, $agenda->getInvitado3());
       }
}

$ret = putReservHorasCancel($token, $idReserva, $_SESSION['userId']);

sendPushToUsers($arr, "🎾 GO Padel", $_SESSION['userNombre'] . " ha cancelado el partido de " . $agenda->getFecha() . " a las " . $agenda->getHora() . ".", '/landing.php');
?>