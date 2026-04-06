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
require_once "../clases/deuda.php";
require_once "../clases/usuarios.php";
require_once "../token/funcToken.php";

// Verificación de token
$dbConn = new Conexion();
$fechHora = date('d-m-Y h:i:s');

$token = "";
$valToken = false;

foreach (getallheaders() as $nombre => $valor) {
    if($nombre === 'Authorization'){
        $token = $valor;
        $valToken = validateToken($valor) == true;
    }
}

if ($valToken == false){
    // Token inválido
    $return['codigoError'] = "10";
    $return['detalleError'] = "Token invalido";
    $return['fechaHora'] = $fechHora;
    header("HTTP/1.1 401 ERROR");
    echo json_encode($return);
    exit();
}

// GET - listar todas las deudas de usuarios
if ($_SERVER['REQUEST_METHOD'] == 'GET'){   
    $input = json_decode(file_get_contents('php://input'), true);

    // Obtener lista de deudores
    $deudas = Deuda::recuperarTodosDeudores();

    if($deudas && count($deudas) > 0){
        // Preparar datos con información del usuario
        $datosDeudas = [];
        foreach($deudas as $deuda){
            $usuario = Usuarios::buscarPorId($deuda->getIdUsuario());
            if($deuda->getSaldo()>0){
                 $datosDeudas[] = [
                'id' => $deuda->getId(),
                'idUsuario' => $deuda->getIdUsuario(),
                'nombreUsuario' => $usuario ? $usuario->getNombre() : 'Usuario no encontrado',
                'cedulaUsuario' => $usuario ? $usuario->getCedula() : '',
                'celularUsuario' => $usuario ? $usuario->getCelular() : '',
                'saldo' => $deuda->getSaldo(),
                'fecha' => $deuda->getFecha()
            ];
            }
           
        }

        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "0";
        $return['detalleError'] = "OK";
        $return['fechaHora'] = $fechHora;
        $return['deudas'] = $datosDeudas;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    } else {
        // Si no hay registros
        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "1";
        $return['detalleError'] = "No se encontraron deudas de usuarios";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }
} else {
    header("HTTP/1.1 499 Bad Request");
    $return['fechaHora'] = $fechHora;
    $respose['consultaResponse'] = $return;
    echo json_encode($respose);
    exit();
}
?>