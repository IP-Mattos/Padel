<?php
// File: c:\wamp64\www\www\www\elGO\gopadel\api\getPostData\putVentasChelada.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set("America/Montevideo");

require_once "../utils.php";
require_once "../Conexion.php";

$dbConn = new Conexion();
$fechHora = date('d-m-Y H:i:s');
$nowFech = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "12";
        $return['detalleError'] = "JSON invalido";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    $idVentaChelada = null;
    $cedula = null;
    $celular = null;
    $importe = null;
    $metodoPago = null;
    $detallePago = null;
    $moneda = "UYU";
    $fechaPago = null;
    $usuario = null;
    $password = null;

    foreach ($input as $clave => $valor) {
        if ($clave === "idVentaChelada") { $idVentaChelada = trim((string)$valor); }
        if ($clave === "cedula") { $cedula = trim((string)$valor); }
        if ($clave === "celular") { $celular = trim((string)$valor); }
        if ($clave === "importe") { $importe = $valor; }
        if ($clave === "metodoPago") { $metodoPago = trim((string)$valor); }
        if ($clave === "detallePago") { $detallePago = trim((string)$valor); }
        if ($clave === "moneda") { $moneda = trim((string)$valor); }
        if ($clave === "fechaPago") { $fechaPago = trim((string)$valor); }
        if ($clave === "usuario") { $usuario = $valor; }
        if ($clave === "password") { $password = $valor; }
    }

    if ($cedula === "") { $cedula = null; }
    if ($celular === "") { $celular = null; }

    if (!$usuario || !$password) {
        header("HTTP/1.1 401 Unauthorized");
        $return['codigoError'] = "10";
        $return['detalleError'] = "Usuario y contraseña son requeridos";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    $usuarioValido = "adminChelada";
    $passwordValido = "Chelada2024!";

    if ($usuario !== $usuarioValido || $password !== $passwordValido) {
        header("HTTP/1.1 401 Unauthorized");
        $return['codigoError'] = "11";
        $return['detalleError'] = "Usuario o contraseña incorrectos";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    if (!$idVentaChelada) {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "idVentaChelada es requerido";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    if (!is_numeric($importe) || floatval($importe) <= 0) {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "3";
        $return['detalleError'] = "importe es requerido y debe ser mayor a 0";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    $fechaPagoSql = $nowFech;
    if ($fechaPago) {
        $timestamp = strtotime($fechaPago);
        if ($timestamp === false) {
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "13";
            $return['detalleError'] = "fechaPago no tiene un formato válido";
            $return['fechaHora'] = $fechHora;
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();
        }
        $fechaPagoSql = date('Y-m-d H:i:s', $timestamp);
    }

    try {
        $idUsuario = null;

        $consultaExiste = $dbConn->prepare('SELECT id FROM ventasChelada WHERE idVentaChelada = :idVentaChelada LIMIT 1');
        $consultaExiste->bindParam(':idVentaChelada', $idVentaChelada);
        $consultaExiste->execute();

        if ($consultaExiste->fetch()) {
            header("HTTP/1.1 409 Conflict");
            $return['codigoError'] = "7";
            $return['detalleError'] = "La venta ya fue integrada previamente";
            $return['fechaHora'] = $fechHora;
            $return['idVentaChelada'] = $idVentaChelada;
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();
        }

        $payload = json_encode($input, JSON_UNESCAPED_UNICODE);

        $sql = 'INSERT INTO ventasChelada
            (idVentaChelada, idUsuario, cedula, celular, importe, moneda, metodoPago, detallePago, fechaPago, payloadJson, fechaRegistro)
            VALUES
            (:idVentaChelada, :idUsuario, :cedula, :celular, :importe, :moneda, :metodoPago, :detallePago, :fechaPago, :payloadJson, :fechaRegistro)';

        $consultaInsert = $dbConn->prepare($sql);
        $consultaInsert->bindParam(':idVentaChelada', $idVentaChelada);
        $consultaInsert->bindParam(':idUsuario', $idUsuario);
        $consultaInsert->bindParam(':cedula', $cedula);
        $consultaInsert->bindParam(':celular', $celular);
        $importeDecimal = floatval($importe);
        $consultaInsert->bindParam(':importe', $importeDecimal);
        $consultaInsert->bindParam(':moneda', $moneda);
        $consultaInsert->bindParam(':metodoPago', $metodoPago);
        $consultaInsert->bindParam(':detallePago', $detallePago);
        $consultaInsert->bindParam(':fechaPago', $fechaPagoSql);
        $consultaInsert->bindParam(':payloadJson', $payload);
        $consultaInsert->bindParam(':fechaRegistro', $nowFech);
        $consultaInsert->execute();

        $idIntegracion = $dbConn->lastInsertId();

        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "0";
        $return['detalleError'] = "Venta registrada correctamente";
        $return['fechaHora'] = $fechHora;
        $return['idIntegracion'] = $idIntegracion;
        $return['idVentaChelada'] = $idVentaChelada;
        $return['idUsuario'] = $idUsuario;
        $return['cedula'] = $cedula;
        $return['celular'] = $celular;
        $return['importe'] = $importeDecimal;
        $return['moneda'] = $moneda;
        $return['metodoPago'] = $metodoPago;
        $return['detallePago'] = $detallePago;
        $return['fechaPago'] = $fechaPagoSql;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();

    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        $return['codigoError'] = "6";
        $return['detalleError'] = "Error en el servidor: " . $e->getMessage();
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    $return['codigoError'] = "10";
    $return['detalleError'] = "Método no permitido. Use PUT";
    $return['fechaHora'] = $fechHora;
    $respose['consultaResponse'] = $return;
    echo json_encode($respose);
    exit();
}
?>