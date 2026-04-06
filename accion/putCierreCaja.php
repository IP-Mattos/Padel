<?php
session_start();

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['userId']) || $_SESSION['isAdmin'] !== "1") {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

require_once "../api/Conexion.php";

$input = json_decode(file_get_contents('php://input'), true);

$fechaDesde    = isset($input['fechaDesde'])    ? trim($input['fechaDesde'])    : '';
$fechaHasta    = isset($input['fechaHasta'])    ? trim($input['fechaHasta'])    : '';
$efectivo      = isset($input['efectivo'])      ? floatval($input['efectivo'])      : 0;
$transferencia = isset($input['transferencia']) ? floatval($input['transferencia']) : 0;
$mercadopago   = isset($input['mercadopago'])   ? floatval($input['mercadopago'])   : 0;
$debito        = isset($input['debito'])        ? floatval($input['debito'])        : 0;
$observaciones = isset($input['observaciones']) ? trim($input['observaciones'])     : '';

if (!$fechaDesde || !$fechaHasta) {
    echo json_encode(["error" => "Faltan datos requeridos"]);
    exit();
}

$total     = $efectivo + $transferencia + $mercadopago + $debito;
$idUsuario = (int)$_SESSION['userId'];

try {
    $conexion = new Conexion();
    $stmt = $conexion->prepare(
        "INSERT INTO cierre_caja
            (fecha, fechaDesde, fechaHasta, efectivo, transferencia, mercadopago, debito, total, idUsuario, observaciones)
         VALUES
            (NOW(), :fechaDesde, :fechaHasta, :efectivo, :transferencia, :mercadopago, :debito, :total, :idUsuario, :observaciones)"
    );
    $stmt->bindParam(':fechaDesde',    $fechaDesde);
    $stmt->bindParam(':fechaHasta',    $fechaHasta);
    $stmt->bindParam(':efectivo',      $efectivo);
    $stmt->bindParam(':transferencia', $transferencia);
    $stmt->bindParam(':mercadopago',   $mercadopago);
    $stmt->bindParam(':debito',        $debito);
    $stmt->bindParam(':total',         $total);
    $stmt->bindParam(':idUsuario',     $idUsuario);
    $stmt->bindParam(':observaciones', $observaciones);
    $stmt->execute();

    echo json_encode(['success' => true, 'id' => (int)$conexion->lastInsertId()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al guardar: ' . $e->getMessage()]);
}
?>
