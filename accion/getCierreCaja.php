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

// Inicio del período "día" actual (ciclo 05:00 → 05:00 siguiente)
$inicioPeriodoActual = ($hora < 5)
    ? new DateTime('yesterday 05:00:00')
    : new DateTime('today 05:00:00');

// Inicio del período anterior (24 h antes del actual)
$inicioPeriodoAnterior = (clone $inicioPeriodoActual)->modify('-1 day');

try {
    $conexion = new Conexion();

    $iniActStr = $inicioPeriodoActual->format('Y-m-d H:i:s');
    $iniAntStr = $inicioPeriodoAnterior->format('Y-m-d H:i:s');
    $ahoraStr  = $ahora->format('Y-m-d H:i:s');

    // ── Período anterior ──────────────────────────────────────────────────────
    // Busca el último cierre que cerró dentro del rango del período anterior.
    // Si su fechaHasta llega exactamente hasta iniAct, el período está cerrado.
    $stmtPrev = $conexion->prepare(
        "SELECT fechaHasta FROM cierre_caja
          WHERE fechaHasta >  :iniAnt
            AND fechaHasta <= :iniAct
          ORDER BY fechaHasta DESC LIMIT 1"
    );
    $stmtPrev->bindParam(':iniAnt', $iniAntStr);
    $stmtPrev->bindParam(':iniAct', $iniActStr);
    $stmtPrev->execute();
    $rowPrev = $stmtPrev->fetch(PDO::FETCH_ASSOC);

    $anteriorDesde = null;
    if ($rowPrev) {
        $lastHasta = new DateTime($rowPrev['fechaHasta']);
        // Gap: el último cierre no llegó hasta el inicio del período actual
        if ($lastHasta < $inicioPeriodoActual) {
            $anteriorDesde = $lastHasta;
        }
    } else {
        // Ningún cierre previo en el período anterior → gap completo
        $anteriorDesde = $inicioPeriodoAnterior;
    }

    // ── Período actual ────────────────────────────────────────────────────────
    // Busca el último cierre parcial DENTRO del período actual.
    // Usamos > (estricto) para no confundir con el cierre que cerró el período anterior.
    $stmtAct = $conexion->prepare(
        "SELECT fechaHasta FROM cierre_caja
          WHERE fechaHasta >  :iniAct
            AND fechaHasta <  :ahora
          ORDER BY fechaHasta DESC LIMIT 1"
    );
    $stmtAct->bindParam(':iniAct', $iniActStr);
    $stmtAct->bindParam(':ahora', $ahoraStr);
    $stmtAct->execute();
    $rowAct = $stmtAct->fetch(PDO::FETCH_ASSOC);

    $actualDesde = $rowAct ? new DateTime($rowAct['fechaHasta']) : $inicioPeriodoActual;

    // ── Construir lista de períodos pendientes ────────────────────────────────
    $periodos = [];
    if ($anteriorDesde !== null) {
        $periodos[] = calcularPeriodo($conexion, $anteriorDesde, $inicioPeriodoActual, 'anterior');
    }
    $periodos[] = calcularPeriodo($conexion, $actualDesde, $ahora, 'actual');

    echo json_encode(['periodos' => $periodos]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error al consultar: ' . $e->getMessage()]);
}

// ─────────────────────────────────────────────────────────────────────────────
// Calcula totales y pendientes para un período determinado
// ─────────────────────────────────────────────────────────────────────────────
function calcularPeriodo(PDO $conexion, DateTime $desde, DateTime $hasta, string $tipo): array
{
    $desdeStr = $desde->format('Y-m-d H:i:s');
    $hastaStr = $hasta->format('Y-m-d H:i:s');

    // Reservas sin confirmar o sin medio de pago cuya hora cae dentro del período.
    // Se usa CONCAT(fecha,' ',hora) para filtrar correctamente en cierres parciales
    // dentro del mismo día.
    $sqlPend = "SELECT COUNT(*) AS pendientes
        FROM agenda a
        WHERE CONCAT(a.fecha, ' ', a.hora) BETWEEN :desde AND :hasta
          AND (
            a.estado = 1
            OR (a.estado = 2 AND (SELECT COUNT(*) FROM pagos p WHERE p.idAgenda = a.id) = 0)
          )";
    $stmtV = $conexion->prepare($sqlPend);
    $stmtV->bindParam(':desde', $desdeStr);
    $stmtV->bindParam(':hasta', $hastaStr);
    $stmtV->execute();
    $pendientes = (int)$stmtV->fetch(PDO::FETCH_ASSOC)['pendientes'];

    // Totales por forma de pago — tabla pagos (alquileres)
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
    WHERE fecha BETWEEN :desde AND :hasta";
    $stmtP = $conexion->prepare($sqlPagos);
    $stmtP->bindParam(':desde', $desdeStr);
    $stmtP->bindParam(':hasta', $hastaStr);
    $stmtP->execute();
    $totalesPagos = $stmtP->fetch(PDO::FETCH_ASSOC);

    // Totales — cobros de deuda
    $sqlCobros = "SELECT
        COALESCE(SUM(CASE WHEN origen = 'EFECTIVO'  THEN monto ELSE 0 END), 0) AS efectivo,
        COALESCE(SUM(CASE WHEN origen = 'TRANS'     THEN monto ELSE 0 END), 0) AS transferencia,
        COALESCE(SUM(CASE WHEN origen = 'MERCPAGO'  THEN monto ELSE 0 END), 0) AS mercadopago,
        COALESCE(SUM(CASE WHEN origen = 'DEBITO'    THEN monto ELSE 0 END), 0) AS debito
    FROM deuda_cobros
    WHERE fecha BETWEEN :desde AND :hasta AND estado = 1";
    $stmtC = $conexion->prepare($sqlCobros);
    $stmtC->bindParam(':desde', $desdeStr);
    $stmtC->bindParam(':hasta', $hastaStr);
    $stmtC->execute();
    $totalesCobros = $stmtC->fetch(PDO::FETCH_ASSOC);

    $totales = [
        'EFECTIVO'  => round(floatval($totalesPagos['efectivo'])      + floatval($totalesCobros['efectivo']),      2),
        'TRANS'     => round(floatval($totalesPagos['transferencia'])  + floatval($totalesCobros['transferencia']), 2),
        'MERCPAGO'  => round(floatval($totalesPagos['mercadopago'])    + floatval($totalesCobros['mercadopago']),   2),
        'DEBITO'    => round(floatval($totalesPagos['debito'])         + floatval($totalesCobros['debito']),        2),
    ];
    $totales['TOTAL'] = round(array_sum($totales), 2);

    return [
        'tipo'          => $tipo,
        'fechaDesde'    => $desdeStr,
        'fechaHasta'    => $hastaStr,
        'pendientes'    => $pendientes,
        'totalesPagos'  => array_map('floatval', $totalesPagos),
        'totalesCobros' => array_map('floatval', $totalesCobros),
        'totales'       => $totales,
    ];
}
?>
