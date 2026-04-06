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

$dbConn = new Conexion();
$fechHora = date('d-m-Y H:i:s');

$token = "";
$valToken = false;

foreach (getallheaders() as $nombre => $valor) {
    if ($nombre === 'Authorization') {
        $token = $valor;
        $valToken = validateToken($valor) == true;
    }
}

if ($valToken == false) {
    $return['codigoError'] = "10";
    $return['detalleError'] = "Token invalido";
    $return['fechaHora'] = $fechHora;
    header("HTTP/1.1 401 ERROR");
    echo json_encode($return);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        $input = array();
    }

    $idTorneo = isset($input['idTorneo']) ? intval($input['idTorneo']) : 0;

    if ($idTorneo <= 0) {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "idTorneo es requerido";
        $return['fechaHora'] = $fechHora;
        $response['consultaResponse'] = $return;
        echo json_encode($response);
        exit();
    }

    try {
        $sql = "SELECT id, idTorneo, idUsuario, Estado
                FROM torneo_aspirantes
                WHERE idTorneo = :idTorneo
                ORDER BY id DESC";

        $stmt = $dbConn->prepare($sql);
        $stmt->bindValue(':idTorneo', $idTorneo);
        $stmt->execute();
        $aspirantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "0";
        $return['detalleError'] = "OK";
        $return['fechaHora'] = $fechHora;
        $return['idTorneo'] = $idTorneo;
        $return['cantidad'] = count($aspirantes);
        $return['aspirantes'] = $aspirantes;
        $response['consultaResponse'] = $return;
        echo json_encode($response);
        exit();

    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        $return['codigoError'] = "99";
        $return['detalleError'] = "Error al consultar aspirantes: " . $e->getMessage();
        $return['fechaHora'] = $fechHora;
        $response['consultaResponse'] = $return;
        echo json_encode($response);
        exit();
    }
}

header("HTTP/1.1 405 Method Not Allowed");
$return['codigoError'] = "11";
$return['detalleError'] = "Metodo no permitido";
$return['fechaHora'] = $fechHora;
$response['consultaResponse'] = $return;
echo json_encode($response);
exit();
?>