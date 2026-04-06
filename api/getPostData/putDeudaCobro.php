<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
    }
}

if ($valToken == false){
    $return['codigoError'] = "10";
    $return['detalleError'] = "Token invalido";
    $return['fechaHora'] = $fechHora;
    header("HTTP/1.1 401 ERROR");
    echo json_encode($return);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
    $input = json_decode(file_get_contents('php://input'), true);

    $idUsuario = null;
    $monto = null;
    $origen = null;
    $detalle = null;

    foreach(getallheaders() as $clave => $valor){
        if($clave === "idUsuario"){ $idUsuario = $valor; }
        if($clave === "monto"){ $monto = $valor; }
        if($clave === "origen"){ $origen = $valor; }
        if($clave === "detalle"){ $detalle = $valor; }
    }

    foreach($input as $clave => $valor){
        if($clave === "idUsuario"){ $idUsuario = $valor; }
        if($clave === "monto"){ $monto = $valor; }
        if($clave === "origen"){ $origen = $valor; }
        if($clave === "detalle"){ $detalle = $valor; }
    }

    if($idUsuario && $monto && $origen){
        $cobro = new DeudaCobros(
            null,
            $idUsuario,
            date('Y-m-d H:i:s'),
            $monto,
            $origen,
            $detalle,
            1
        );

        $id = $cobro->guardar();

        if($id){
            header("HTTP/1.1 200 OK");
            $return['codigoError'] = "0";
            $return['detalleError'] = "OK";
            $return['fechaHora'] = $fechHora;
            $return['id'] = $id;
            $return['datos'] = $cobro->toArray();
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        } else {
            header("HTTP/1.1 500 ERROR");
            $return['codigoError'] = "2";
            $return['detalleError'] = "Error al guardar el cobro";
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }
    } else {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "Faltan datos requeridos (idUsuario, monto, origen)";
        $return['fechaHora'] = $fechHora;
        $response['consultaResponse'] = $return;
        echo json_encode($response);
        exit();
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    $return['codigoError'] = "3";
    $return['detalleError'] = "Método no permitido";
    $return['fechaHora'] = $fechHora;
    $response['consultaResponse'] = $return;
    echo json_encode($response);
    exit();
}
?>