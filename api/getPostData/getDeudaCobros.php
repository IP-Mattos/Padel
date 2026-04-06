<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set("America/Montevideo");

require_once "../utils.php";
require_once "../Conexion.php";
require_once "../token/funcToken.php";
require_once "../clases/deudaCobros.php";

$dbConn = new Conexion();
$fechHora = date('Y-m-d H:i:s');

$token = "";
$valToken = false;

foreach (getallheaders() as $nombre => $valor) {
    if($nombre === 'Authorization'){
        $token = $valor;
        $valToken = validateToken($valor) == true;