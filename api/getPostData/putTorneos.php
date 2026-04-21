<?php
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

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "JSON invalido";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    $id = null;
    $categoria = null;
    $fecha = null;
    $nombre = null;
    $entre = 0;
    $estado = 0;

    foreach ($input as $clave => $valor) {
        if ($clave === "id") { $id = trim((string)$valor); }
        if ($clave === "categoria") { $categoria = intval($valor); }
        if ($clave === "fecha") { $fecha = trim((string)$valor); }
        if ($clave === "nombre") { $nombre = trim((string)$valor); }
        if ($clave === "entre") { $entre = intval($valor); }
        if ($clave === "estado") { $estado = intval($valor); }
    }

    if ($categoria === null || $fecha === null || $nombre === null || $nombre === "") {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "2";
        $return['detalleError'] = "Faltan datos requeridos (categoria, fecha, nombre)";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "3";
        $return['detalleError'] = "fecha debe tener formato Y-m-d";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    try {
        if ($id !== null && $id !== "") {
            $sql = "UPDATE torneos SET categoria = :categoria, fecha = :fecha, nombre = :nombre, entre = :entre, estado = :estado 
            WHERE id = :id";
            $stmt = $dbConn->prepare($sql);
            $idInt = intval($id);
            $stmt->bindParam(':id', $idInt);
        } else {
            $sql = "INSERT INTO torneos (categoria, fecha, nombre, entre, estado)
                    VALUES (:categoria, :fecha, :nombre, :entre, :estado)";
            $stmt = $dbConn->prepare($sql);
        }

        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':entre', $entre);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        $idTorneo = $dbConn->lastInsertId();
        if (!$idTorneo && $id !== null && $id !== "") {
            $idTorneo = intval($id);
        }

        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "0";
        $return['detalleError'] = "Torneo guardado correctamente";
        $return['fechaHora'] = $fechHora;
        $return['idTorneo'] = $idTorneo;
        $return['categoria'] = $categoria;
        $return['fecha'] = $fecha;
        $return['nombre'] = $nombre;
        $return['entre'] = $entre;
        $return['estado'] = $estado;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();

    } catch (PDOException $e) {
        header("HTTP/1.1 500 Internal Server Error");
        $return['codigoError'] = "99";
        $return['detalleError'] = "Error al guardar torneo: " . $e->getMessage();
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    $return['codigoError'] = "11";
    $return['detalleError'] = "Metodo no permitido";
    $return['fechaHora'] = $fechHora;
    $respose['consultaResponse'] = $return;
    echo json_encode($respose);
    exit();
}
?>