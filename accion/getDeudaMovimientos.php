<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['codigoError' => '10', 'detalleError' => 'No autorizado']);
    exit;
}

require_once "../api/Conexion.php";
require_once "../api/clases/deuda.php";

$movimientos = Deuda::buscarPorUsuario($_SESSION['userId']);
$data = [];

foreach ($movimientos as $m) {
    $data[] = [
        'id' => $m->getId(),
        'debe' => $m->getDebe(),
        'haber' => $m->getHaber(),
        'saldo' => $m->getSaldo(),
        'fecha' => $m->getFecha(),
        'tipo' => $m->getIdChelada() != 0 ? 'Chelada' : 'GO Padel',
    ];
}

echo json_encode([
    'consultaResponse' => [
        'codigoError' => '0',
        'detalleError' => 'OK',
        'movimientos' => $data,
    ]
]);