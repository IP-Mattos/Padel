
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
require_once "../clases/horaFija.php";
require_once "../token/funcToken.php";

$dbConn = new Conexion();
$fechHora = date('d-m-Y H:i:s');

$token = "";
$valToken = false;

// Verificación de token
foreach (getallheaders() as $nombre => $valor) {
    if($nombre === 'Authorization'){
        $token = $valor;
        $valToken = validateToken($valor) == true;
    }
}

if ($valToken == false){
    // Token inválido
    $return['codigoError'] = "10";
    $return['detalleError'] = "Token invalido";
    $return['fechaHora'] = $fechHora;
    header("HTTP/1.1 401 ERROR");
    echo json_encode($return);
    exit();
}

// PUT - Guardar o eliminar hora fija
if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
    $input = json_decode(file_get_contents('php://input'), true);

    // Inicializar variables
    $idUsuario = null;
    $dia = null;
    $hora = null;
    $servicio = null;
    $accion = null;

    // Obtener parámetros del input
    foreach($input as $clave => $valor){
        if($clave === "idUsuario"){ $idUsuario = $valor; }
        if($clave === "dia"){ $dia = intval($valor); }
        if($clave === "hora"){ $hora = $valor; }
        if($clave === "servicio"){ $servicio = $valor; }
        if($clave === "accion"){ $accion = intval($valor); }
    }

    // Validar que todos los parámetros estén presentes
    if($idUsuario === null || $dia === null || $hora === null || $servicio === null || $accion === null){
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "Faltan parámetros requeridos (idUsuario, dia, hora, servicio, accion)";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    // Validar que el día esté en el rango correcto (0-6)
    if($dia < 0 || $dia > 6){
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "2";
        $return['detalleError'] = "El dia debe estar entre 0 (domingo) y 6 (sabado)";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    // Validar que la acción sea 0 o 1
    if($accion !== 0 && $accion !== 1){
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "3";
        $return['detalleError'] = "La accion debe ser 0 (eliminar) o 1 (guardar)";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

    try {
        if($accion === 1){
            // GUARDAR - Verificar si ya existe
            if(HoraFija::existeHoraFija($dia, $hora, $servicio, $idUsuario)){
                header("HTTP/1.1 409 Conflict");
                $return['codigoError'] = "4";
                $return['detalleError'] = "Ya existe una hora fija con estos parametros";
                $return['fechaHora'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode($respose);
                exit();
            }

            // Crear y guardar nueva hora fija
            $horaFija = new HoraFija($dia, $hora, $servicio, $idUsuario);
            $horaFija->guardar();

            header("HTTP/1.1 200 OK");
            $return['codigoError'] = "0";
            $return['detalleError'] = "Hora fija guardada correctamente";
            $return['fechaHora'] = $fechHora;
            $return['idHoraFija'] = $horaFija->getId();
            $return['datos'] = [
                'idUsuario' => $idUsuario,
                'dia' => $dia,
                'hora' => $hora,
                'servicio' => $servicio
            ];
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();

        } else {
            // ELIMINAR - Buscar la hora fija
            $conexion = new Conexion();
            $consulta = $conexion->prepare('SELECT id FROM horafija 
                WHERE dia = :dia AND hora = :hora AND servicio = :servicio AND idUser = :idUser');
            $consulta->bindParam(':dia', $dia);
            $consulta->bindParam(':hora', $hora);
            $consulta->bindParam(':servicio', $servicio);
            $consulta->bindParam(':idUser', $idUsuario);
            $consulta->execute();
            $registro = $consulta->fetch();

            if(!$registro){
                header("HTTP/1.1 404 Not Found");
                $return['codigoError'] = "5";
                $return['detalleError'] = "No se encontró la hora fija para eliminar";
                $return['fechaHora'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode($respose);
                exit();
            }

            // Eliminar la hora fija
            $horaFija = HoraFija::buscarPorId($registro['id']);
            $horaFija->eliminar();

            header("HTTP/1.1 200 OK");
            $return['codigoError'] = "0";
            $return['detalleError'] = "Hora fija eliminada correctamente";
            $return['fechaHora'] = $fechHora;
            $return['datos'] = [
                'idUsuario' => $idUsuario,
                'dia' => $dia,
                'hora' => $hora,
                'servicio' => $servicio
            ];
            $respose['consultaResponse'] = $return;
            echo json_encode($respose);
            exit();
        }

    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        $return['codigoError'] = "99";
        $return['detalleError'] = "Error al procesar la solicitud: " . $e->getMessage();
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode($respose);
        exit();
    }

} else {
    header("HTTP/1.1 405 Method Not Allowed");
    $return['codigoError'] = "10";
    $return['detalleError'] = "Método no permitido";
    $return['fechaHora'] = $fechHora;
    $respose['consultaResponse'] = $return;
    echo json_encode($respose);
    exit();
}
?>