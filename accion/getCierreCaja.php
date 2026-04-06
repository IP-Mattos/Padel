<?php
session_start();

header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['userId']) || $_SESSION['isAdmin'] !== "1") {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

require_once "../api/Conexion.php";
date_default_timezone_set("America/Montevideo");

$ahora = new DateTime();
$hora  = (int)$ahora->format('H');

// Si es entre 00:00 y 04:59, el período arranca a las 05:00 del día anterior
if ($hora < 5) {
    $fechaDesde = new DateTime('yesterday 05:00:00');
} else {
    $fechaDesde = new DateTime('today 05:00:00');
}
$fechaHasta = $ahora;

$fechaDesdeStr  = $fechaDesde->format('Y-m-d H:i:s');
$fechaHastaStr  = $fechaHasta->format('Y-m-d H:i:s');
$fechaDesdeDate = $fechaDesde->format('Y-m-d');
$fechaHastaDate = $fechaHasta->format('Y-m-d');

try {
    $conexion = new Conexion();

    // Validar: reservas sin confirmar o sin medio de pago dentro del período
    $sqlValidar = "SELECT COUNT(*) AS pendientes
        FROM agenda a
        WHERE a.fecha BETWEEN :fechaDesde AND :fechaHasta
          AND (
            a.estado = 1
            OR (a.estado = 2 AND (SELECT COUNT(*) FROM pagos p WHERE p.idAgenda = a.id) = 0)
          )";
    $stmtV = $conexion->prepare($sqlValidar);
    $stmtV->bindParam(':fechaDesde', $fechaDesdeDate);
    $stmtV->bindParam(':fechaHasta', $fechaHastaDate);
    $stmtV->execute();
    $pendientes = (int)$stmtV->fetch(PDO::FETCH_ASSOC)['pendientes'];

    // Totales por forma de pago — tabla pagos (horas de alquiler)
    $sqlPagos = "SELECT
        COALESCE(SUM(CASE WHEN fdpUsuario   = 'EFECTIVO' THEN impUsu  ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado1 = 'EFECTIVO' THEN impInv1 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado2 = 'EFECTIVO' THEN impInv2 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado3 = 'EFECTIVO' THEN impInv3 ELSE 0 END), 0) AS efectivo,

        COALESCE(SUM(CASE WHEN fdpUsuario   = 'TRANS' THEN impUsu  ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado1 = 'TRANS' THEN impInv1 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado2 = 'TRANS' THEN impInv2 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado3 = 'TRANS' THEN impInv3 ELSE 0 END), 0) AS transferencia,

        COALESCE(SUM(CASE WHEN fdpUsuario   = 'MERCPAGO' THEN impUsu  ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado1 = 'MERCPAGO' THEN impInv1 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado2 = 'MERCPAGO' THEN impInv2 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado3 = 'MERCPAGO' THEN impInv3 ELSE 0 END), 0) AS mercadopago,

        COALESCE(SUM(CASE WHEN fdpUsuario   = 'DEBITO' THEN impUsu  ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado1 = 'DEBITO' THEN impInv1 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado2 = 'DEBITO' THEN impInv2 ELSE 0 END), 0)
      + COALESCE(SUM(CASE WHEN fdpInvitado3 = 'DEBITO' THEN impInv3 ELSE 0 END), 0) AS debito

    FROM pagos
    WHERE fecha BETWEEN :fechaDesde AND :fechaHasta";

    $stmtP = $conexion->prepare($sqlPagos);
    $stmtP->bindParam(':fechaDesde', $fechaDesdeStr);
    $stmtP->bindParam(':fechaHasta', $fechaHastaStr);
    $stmtP->execute();
    $totalesPagos = $stmtP->fetch(PDO::FETCH_ASSOC);

    // Totales por forma de pago — tabla deuda_cobros (cobros de deuda)
    $sqlCobros = "SELECT
        COALESCE(SUM(CASE WHEN origen = 'EFECTIVO'  THEN monto ELSE 0 END), 0) AS efectivo,
        COALESCE(SUM(CASE WHEN origen = 'TRANS'     THEN monto ELSE 0 END), 0) AS transferencia,
        COALESCE(SUM(CASE WHEN origen = 'MERCPAGO'  THEN monto ELSE 0 END), 0) AS mercadopago,
        COALESCE(SUM(CASE WHEN origen = 'DEBITO'    THEN monto ELSE 0 END), 0) AS debito
    FROM deuda_cobros
    WHERE fecha BETWEEN :fechaDesde AND :fechaHasta AND estado = 1";

    $stmtC = $conexion->prepare($sqlCobros);
    $stmtC->bindParam(':fechaDesde', $fechaDesdeStr);
    $stmtC->bindParam(':fechaHasta', $fechaHastaStr);
    $stmtC->execute();
    $totalesCobros = $stmtC->fetch(PDO::FETCH_ASSOC);

    $totales = [
        'EFECTIVO'  => round(floatval($totalesPagos['efectivo'])      + floatval($totalesCobros['efectivo']),      2),
        'TRANS'     => round(floatval($totalesPagos['transferencia'])  + floatval($totalesCobros['transferencia']), 2),
        'MERCPAGO'  => round(floatval($totalesPagos['mercadopago'])    + floatval($totalesCobros['mercadopago']),   2),
        'DEBITO'    => round(floatval($totalesPagos['debito'])         + floatval($totalesCobros['debito']),        2),
    ];
    $totales['TOTAL'] = round(array_sum($totales), 2);

    echo json_encode([
        'fechaDesde'    => $fechaDesdeStr,
        'fechaHasta'    => $fechaHastaStr,
        'pendientes'    => $pendientes,
        'totalesPagos'  => array_map('floatval', $totalesPagos),
        'totalesCobros' => array_map('floatval', $totalesCobros),
        'totales'       => $totales,
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error al consultar: ' . $e->getMessage()]);
}
?>
