<?php
// File: c:\wamp64\www\www\www\elGO\gopadel\api\getPostData\putDeudaChelada.php

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
require_once "../clases/deuda.php";
require_once "../clases/usuarios.php";

$dbConn = new Conexion();
$fechHora = date('d-m-Y H:i:s');
$nowFech = date('Y-m-d H:i:s');

// PUT - Registrar deuda de chelada
if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
    $input = json_decode(file_get_contents('php://input'), true);

    $idChelada = null;
    $cedula = null;
    $celular = null;
    $importe = null;
    $usuario = null;
    $password = null;

    foreach($input as $clave => $valor){
        if($clave === "idChelada"){$idChelada = $valor;}
        if($clave === "cedula"){$cedula = $valor;}
        if($clave === "celular"){$celular = $valor;}
        if($clave === "importe"){$importe = $valor;}
        if($clave === "usuario"){$usuario = $valor;}
        if($clave === "password"){$password = $valor;}
    }

    // Validar que se recibieron usuario y contraseña
    if(!$usuario || !$password){
        header("HTTP/1.1 401 Unauthorized");
        $return['codigoError'] = "10";
        $return['detalleError'] = "Usuario y contraseña son requeridos";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    // Verificar credenciales - usuario y contraseña hardcodeados
    $usuarioValido = "adminChelada";  // Cambiar por el usuario correcto
    $passwordValido = "Chelada2024!";  // Cambiar por la contraseña correcta

    if($usuario !== $usuarioValido || $password !== $passwordValido){
        header("HTTP/1.1 401 Unauthorized");
        $return['codigoError'] = "11";
        $return['detalleError'] = "Usuario o contraseña incorrectos";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    // Validar datos requeridos
    if(!$idChelada || $idChelada <= 0){
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "idChelada es requerido y debe ser mayor a 0";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    if(!$cedula && !$celular){
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "2";
        $return['detalleError'] = "Se requiere cédula o celular para identificar al usuario";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    if(!$importe || $importe <= 0){
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "3";
        $return['detalleError'] = "importe es requerido y debe ser mayor a 0";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    // Buscar el usuario por cédula o celular
    try {
        $usuarioDeuda = null;
        
        if($cedula){
            // Buscar por cédula
            $usuarioDeuda = Usuarios::buscarPorCedula($cedula);
        }
        
        // Si no se encontró por cédula, buscar por celular
      

        if(!$usuarioDeuda){
            header("HTTP/1.1 404 Not Found");
            $return['codigoError'] = "4";
            $return['detalleError'] = "Usuario no encontrado con la cédula o celular proporcionado";
            $return['fechaHora'] = $fechHora;
            $return['cedula'] = $cedula;
            $return['celular'] = $celular;
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();
        }

        $idUsuario = $usuarioDeuda->getId();

    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        $return['codigoError'] = "14";
        $return['detalleError'] = "Error al buscar usuario: " . $e->getMessage();
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    try {
        // Crear el registro de deuda
        // idChelada, idPagos=0 (no viene de un pago), debe=importe, haber=0
        $deuda = new Deuda($idUsuario,$idChelada,0,0,$importe,0,0,$nowFech);


        // Guardar la deuda
        $deuda->guardar();

        // Verificar que se guardó correctamente
        if($deuda->getId() > 0){
            // Obtener el saldo actualizado del usuario
            $saldoActual = Deuda::obtenerSaldoActual($idUsuario);

            header("HTTP/1.1 200 OK");
            $return['codigoError'] = "0";
            $return['detalleError'] = "Deuda registrada correctamente";
            $return['fechaHora'] = $fechHora;
            $return['idDeuda'] = $deuda->getId();
            $return['idChelada'] = $idChelada;
            $return['nombreUsuario'] = $usuarioDeuda->getNombre();
            $return['cedula'] = $usuarioDeuda->getCedula();
            $return['celular'] = $usuarioDeuda->getCelular();
            $return['importe'] = $importe;
            $return['saldoActual'] = $saldoActual;
            $return['idIntegra'] = $deuda->getId();
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $return['codigoError'] = "5";
            $return['detalleError'] = "Error al guardar la deuda";
            $return['fechaHora'] = $fechHora;
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();
        }

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