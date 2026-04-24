<?php
// accion/getCategoriaSocio.php
// Returns a single CategoriasSocios record by id.
// Called by admin.js to resolve pricing for active socios.

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

date_default_timezone_set("America/Montevideo");

require_once "../api/Conexion.php";
require_once "../api/clases/categoriasSocios.php"; // adjust path if needed

$fechHora = date('d-m-Y H:i:s');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['consultaResponse' => ['codigoError' => '10', 'detalleError' => 'Método no permitido']]);
    exit;
}

$idCategoria = $_POST['idCategoria'] ?? null;

if (!$idCategoria) {
    http_response_code(400);
    echo json_encode(['consultaResponse' => ['codigoError' => '1', 'detalleError' => 'idCategoria requerido']]);
    exit;
}

$categoria = CategoriasSocios::buscarPorId((int) $idCategoria);

if (!$categoria) {
    echo json_encode([
        'consultaResponse' => [
            'codigoError' => '1',
            'detalleError' => 'Categoría no encontrada',
            'fechaHora' => $fechHora,
            'categoria' => null,
        ]
    ]);
    exit;
}

echo json_encode([
    'consultaResponse' => [
        'codigoError' => '0',
        'detalleError' => 'OK',
        'fechaHora' => $fechHora,
        'categoria' => [
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'valor' => $categoria->getValor(),
            'valorHoraBaja' => $categoria->getValorHoraBaja(),
            'valorHoraAlta' => $categoria->getValorHoraAlta(),
            'descripcion' => $categoria->getDescripcion(),
        ],
    ]
]);