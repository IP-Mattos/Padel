<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set("America/Montevideo");


//echo "entro a metodo";

require_once "../utils.php";
require_once "../Conexion.php";
require_once "../clases/servicios.php";
require_once "../token/funcToken.php";
require_once "../clases/usuarios.php";
require_once "../clases/agenda.php";
//verificacion de token

$dbConn = new Conexion();
$fechHora = date('d-m-Y H:i:s');
$nowFech = date('Y-m-d H:i:s');

$token = "";
$valToken = false;


foreach (getallheaders() as $nombre => $valor) {
    //echo "$nombre: $valor\n";
    //header("Authorization:".$token);
    if ($nombre === 'Authorization') {
        $token = $valor;
        $valToken = validateToken($valor) == true;
    }
}
if ($valToken == false) {
    //token invalido
    $return['codigoError'] = "10";
    $return['detalleError'] = "Token invalido";
    $return['fechaHora'] = $fechHora;
    header("HTTP/1.1 401 ERROR");
    echo json_encode($return);
    exit();
}

//echo "antes de methodo";
// GET - listar adeudo por numero de abonado o por documento*/
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    foreach ($input as $clave => $valor) {
        if ($clave === "idUser") {
            $idUser = $valor;
        }
        if ($clave === "idReserva") {
            $idReserva = $valor;
        }
        if ($clave === "idInvitado") {
            $idInvitado = $valor;
        }


    }

    //obtengo el usuario
    $userInvitado = Usuarios::buscarPorId($idInvitado);
    $userReserva = Usuarios::buscarPorId($idReserva);



    if ($idReserva > 0 && $idInvitado > 0) {
        $agenda = Agenda::buscarPorId($idReserva);
        if ($agenda->getServicio() == 4) { /// o sea si es VS
            if ($agenda->getIdUsuario() == $idUser && $agenda->getIdUserRival() != $idInvitado) {
                if ($agenda->getInvitado1() == 0) {
                    $agenda->setInvitado1($idInvitado);
                    $agenda->guardar();

                    //si no hay registro
                    header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "0";
                    $return['detalleError'] = "Usuario invitado al partido";
                    $return['userId'] = $agenda->getIdUsuario();
                    $return['fecha'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode($respose);
                    exit();
                } else {
                    header("HTTP/1.1 400 Bad Request");
                    $return['codigoError'] = "3";
                    $return['detalleError'] = "El Armador de este partido ya tiene un invitado";
                    $return['fechaHora'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode($respose);
                    exit();
                }
            } elseif ($agenda->getIdUserRival() == $idUser && $agenda->getIdUsuario() != $idInvitado) {
                if ($agenda->getInvitado3() == 0) {
                    $agenda->setInvitado3($idInvitado);
                    $agenda->guardar();

                    $mensaje = $userReserva->getNombre() . " te agrego a su partido. Revisa en tus reservas y buen juego.";
                    $whatsSend = setSMSUserGO($mensaje, $userInvitado->getCelular(), $userInvitado->getNombre());
                    $sneds = setActualizarEnvioClave();

                    //si no hay registro
                    header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "0";
                    $return['detalleError'] = "Usuario invitado al partido";
                    $return['userId'] = $agenda->getIdUsuario();
                    $return['fecha'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode($respose);
                    exit();
                } else {
                    header("HTTP/1.1 400 Bad Request");
                    $return['codigoError'] = "3";
                    $return['detalleError'] = "El Retador en este partido ya tiene un invitado";
                    $return['fechaHora'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode($respose);
                    exit();
                }

            } else {

                header("HTTP/1.1 400 Bad Request");
                $return['codigoError'] = "2";
                $return['detalleError'] = "El partido no pertenece al usuario";
                $return['fechaHora'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode($respose);
                exit();
            }
        } else {

            if ($agenda->getIdUsuario() != $idInvitado && $agenda->getInvitado1() != $idInvitado && $agenda->getInvitado2() != $idInvitado && $agenda->getInvitado3() != $idInvitado) {

                if ($agenda->getInvitado1() == 0) {
                    $agenda->setInvitado1($idInvitado);
                } elseif ($agenda->getInvitado2() == 0) {
                    $agenda->setInvitado2($idInvitado);
                } elseif ($agenda->getInvitado3() == 0 && $agenda->getIdUserRival() == 0) {
                    $agenda->setInvitado3($idInvitado);
                } else {
                    header("HTTP/1.1 400 Bad Request");
                    $return['codigoError'] = "3";
                    $return['detalleError'] = "Espacio para invitados completo";
                    $return['fechaHora'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode($respose);
                    exit();
                }

                $agenda->guardar();

                //si no hay registro
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "0";
                $return['detalleError'] = "Usuario invitado al partido";
                $return['userId'] = $agenda->getIdUsuario();
                $return['fecha'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode($respose);
                exit();
            } else {
                header("HTTP/1.1 400 Bad Request");
                $return['codigoError'] = "2";
                $return['detalleError'] = "Este jugador ya se encuentra invitado en este partido";
                $return['fechaHora'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode($respose);
                exit();
            }



        }


    } else {
        header("HTTP/1.1 400 Bad Request");
        $return['codigoError'] = "1";
        $return['detalleError'] = "Falta información sobre el partido o el invitado";
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