<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['codigoError' => '10', 'detalleError' => 'No autorizado']);
    exit;
}

require_once "../api/Conexion.php";
require_once "../api/clases/deuda.php";

$saldo = Deuda::obtenerSaldoActual($_SESSION['userId']);

echo json_encode([
    'consultaResponse' => [
        'codigoError' => '0',
        'saldo' => $saldo,
    ]
]);