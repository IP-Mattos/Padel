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

    $estado = isset($input['estado']) ? trim((string)$input['estado']) : "";
    $fechaDesde = isset($input['fechaDesde']) ? trim((string)$input['fechaDesde']) : "";
    $fechaHasta = isset($input['fechaHasta']) ? trim((string)$input['fechaHasta']) : "";

    $where = array();
    $params = array();

    if ($estado !== "") {
        $where[] = "estado = :estado";
        $params[':estado'] = intval($estado);
    }

    if ($fechaDesde !== "") {
        $fechaDesdeValida = DateTime::createFromFormat('Y-m-d', $fechaDesde);
        if (!$fechaDesdeValida || $fechaDesdeValida->format('Y-m-d') !== $fechaDesde) {
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "2";
            $return['detalleError'] = "fechaDesde invalida. Formato requerido: Y-m-d";
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }
        $where[] = "fecha >= :fechaDesde";
        $params[':fechaDesde'] = $fechaDesde;
    }

    if ($fechaHasta !== "") {
        $fechaHastaValida = DateTime::createFromFormat('Y-m-d', $fechaHasta);
        if (!$fechaHastaValida || $fechaHastaValida->format('Y-m-d') !== $fechaHasta) {
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "3";
            $return['detalleError'] = "fechaHasta invalida. Formato requerido: Y-m-d";
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }
        $where[] = "fecha <= :fechaHasta";
        $params[':fechaHasta'] = $fechaHasta;
    }

    $sql = "SELECT id, categoria, fecha, nombre, entre, estado FROM torneos";
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY fecha DESC, id DESC";

    try {
        $stmt = $dbConn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "0";
        $return['detalleError'] = "OK";
        $return['fechaHora'] = $fechHora;
        $return['filtro'] = array(
            'estado' => $estado,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta
        );
        $return['cantidad'] = count($torneos);
        $return['torneos'] = $torneos;
        $response['consultaResponse'] = $return;
        echo json_encode($response);
        exit();

    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        $return['codigoError'] = "99";
        $return['detalleError'] = "Error al consultar torneos: " . $e->getMessage();
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