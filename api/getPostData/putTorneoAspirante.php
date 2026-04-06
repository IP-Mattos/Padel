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
        $response['consultaResponse'] = $return;
        echo json_encode($response);
        exit();
    }

    $accion = "insert";
    $id = null;
    $idTorneo = null;
    $idUsuario = null;
    $estado = 0;

    foreach ($input as $clave => $valor) {
        if ($clave === "accion") { $accion = strtolower(trim((string)$valor)); }
        if ($clave === "id") { $id = trim((string)$valor); }
        if ($clave === "idTorneo") { $idTorneo = intval($valor); }
        if ($clave === "idUsuario") { $idUsuario = intval($valor); }
        if ($clave === "estado") { $estado = intval($valor); }
    }

    if ($accion === "insert") {
        if (!$idTorneo || !$idUsuario) {
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "2";
            $return['detalleError'] = "Faltan datos requeridos (idTorneo, idUsuario)";
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }

        try {
            $estadoInsert = 0;
            if ($id !== null && $id !== "") {
                $sql = "INSERT INTO torneo_aspirantes (id, idTorneo, idUsuario, Estado)
                        VALUES (:id, :idTorneo, :idUsuario, :estado)";
                $stmt = $dbConn->prepare($sql);
                $idInt = intval($id);
                $stmt->bindParam(':id', $idInt);
            } else {
                $sql = "INSERT INTO torneo_aspirantes (idTorneo, idUsuario, Estado)
                        VALUES (:idTorneo, :idUsuario, :estado)";
                $stmt = $dbConn->prepare($sql);
            }

            $stmt->bindParam(':idTorneo', $idTorneo);
            $stmt->bindParam(':idUsuario', $idUsuario);
            $stmt->bindParam(':estado', $estadoInsert);
            $stmt->execute();

            $idAspirante = $dbConn->lastInsertId();
            if (!$idAspirante && $id !== null && $id !== "") {
                $idAspirante = intval($id);
            }

            header("HTTP/1.1 200 OK");
            $return['codigoError'] = "0";
            $return['detalleError'] = "Aspirante registrado correctamente";
            $return['fechaHora'] = $fechHora;
            $return['accion'] = "insert";
            $return['id'] = $idAspirante;
            $return['idTorneo'] = $idTorneo;
            $return['idUsuario'] = $idUsuario;
            $return['estado'] = $estadoInsert;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();

        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            $return['codigoError'] = "99";
            $return['detalleError'] = "Error al guardar aspirante: " . $e->getMessage();
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }
    }

    if ($accion === "update") {
        if ($estado < 0) {
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "3";
            $return['detalleError'] = "estado invalido";
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }

        try {
            if ($id !== null && $id !== "") {
                $sql = "UPDATE torneo_aspirantes SET Estado = :estado WHERE id = :id";
                $stmt = $dbConn->prepare($sql);
                $idInt = intval($id);
                $stmt->bindParam(':id', $idInt);
            } else {
                if (!$idTorneo || !$idUsuario) {
                    header("HTTP/1.1 400 Bad Request");
                    $return['codigoError'] = "4";
                    $return['detalleError'] = "Para update sin id, enviar idTorneo e idUsuario";
                    $return['fechaHora'] = $fechHora;
                    $response['consultaResponse'] = $return;
                    echo json_encode($response);
                    exit();
                }

                $sql = "UPDATE torneo_aspirantes
                        SET Estado = :estado
                        WHERE idTorneo = :idTorneo AND idUsuario = :idUsuario";
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':idTorneo', $idTorneo);
                $stmt->bindParam(':idUsuario', $idUsuario);
            }

            $stmt->bindParam(':estado', $estado);
            $stmt->execute();

            header("HTTP/1.1 200 OK");
            $return['codigoError'] = "0";
            $return['detalleError'] = "Estado actualizado correctamente";
            $return['fechaHora'] = $fechHora;
            $return['accion'] = "update";
            $return['id'] = ($id !== null && $id !== "") ? intval($id) : null;
            $return['idTorneo'] = $idTorneo;
            $return['idUsuario'] = $idUsuario;
            $return['estado'] = $estado;
            $return['filasAfectadas'] = $stmt->rowCount();
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();

        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            $return['codigoError'] = "99";
            $return['detalleError'] = "Error al actualizar aspirante: " . $e->getMessage();
            $return['fechaHora'] = $fechHora;
            $response['consultaResponse'] = $return;
            echo json_encode($response);
            exit();
        }
    }

    header("HTTP/1.1 400 Bad Request");
    $return['codigoError'] = "5";
    $return['detalleError'] = "accion invalida. Use insert o update";
    $return['fechaHora'] = $fechHora;
    $response['consultaResponse'] = $return;
    echo json_encode($response);
    exit();
}

header("HTTP/1.1 405 Method Not Allowed");
$return['codigoError'] = "11";
$return['detalleError'] = "Metodo no permitido";
$return['fechaHora'] = $fechHora;
$response['consultaResponse'] = $return;
echo json_encode($response);
exit();
?>