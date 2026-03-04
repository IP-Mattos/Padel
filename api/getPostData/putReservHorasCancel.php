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
    $fechHora = date('Y-m-d H:i:s');

    $token = "";
    $valToken =false;


    foreach (getallheaders() as $nombre => $valor) {
        //echo "$nombre: $valor\n";
        //header("Authorization:".$token);
        if($nombre === 'Authorization'){
            $token = $valor;
            $valToken =validateToken($valor)==true;
        }
    }

    if ($valToken==false){
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
    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
        $input = json_decode(file_get_contents('php://input'), true);

        foreach($input as $clave=>$valor){
            if($clave === "idReserv"){$idReserv = $valor;}
            if($clave === "idUser"){$idUser = $valor;}
            
        }

         $agenda = Agenda::buscarPorId($idReserv);
        if($agenda->getId() == $idReserv ){
                 
            //obtengo el usuario
            $elUser = Usuarios::buscarPorId($idUser);
            

                //si el usuario es igual al rival, entonces cancelo la relacion del usuario rival
                 if( $agenda->getIdUserRival() == $idUser){
                        $agenda->setIdUserRival(0);
                        $agenda->setInvitado2(0);
                        $agenda->setMensaje("");
                        $agenda->guardar();
                        //si no hay registro
                        header("HTTP/1.1 200 OK");
                        $return['codigoError'] = "0";
                        $return['detalleError'] = "Relacion de VS cancelada con éxito";
                        $respose['consultaResponse'] = $return;

                        echo json_encode( $respose );
                        exit();


                    //de lo contrario si el usuario es diferente al rival, entonces me fijo que no tenga rival
                }elseif( $agenda->getIdUserRival() == 0 && $agenda->getIdUsuario() == $idUser){

                        Agenda::borrarRegistro($idReserv);
                        $servicio = Servicio::buscarPorId($agenda->getServicio());
                        //si no hay registro
                         //MANDO WHATSAPP
                        $mensaje = "reservaMauro";
                        $whatsSend = setLoginUserFast($mensaje, $elUser->getCelular(), $elUser->getNombre() . " - " . 
                        $elUser->getCelular(). " SE CANCELA RESERVA horas para el día ". $agenda->getFecha(). 
                        " en el horario ". $agenda->getHora(). " cancha ". $servicio->getNombre());
                        $ret['whatsSend'] = $whatsSend;
                        $sneds = setActualizarEnvioClave();

                        header("HTTP/1.1 200 OK");
                        $return['codigoError'] = "0";
                        $return['detalleError'] = "Reserva Eliminada con éxito";
                        $respose['consultaResponse'] = $return;

                        echo json_encode( $respose );
                        exit();
                }elseif($elUser->getisadmin() == 1){
                        Agenda::borrarRegistro($idReserv);
                        //si no hay registro
                        header("HTTP/1.1 200 OK");
                        $return['codigoError'] = "0";
                        $return['detalleError'] = "Reserva Eliminada ";
                        $respose['consultaResponse'] = $return;
                        echo json_encode( $respose );
                        exit();

                }else{
                        header("HTTP/1.1 400 Bad Request");
                        $return['codigoError'] = "2";
                        $return['detalleError'] = "Tu reserva ya tiene un rival, debes solicitar a tú rival que cancele para que tu puedas cancelar la reserva";
                        $return['fechaHora'] = $fechHora;
                        $respose['consultaResponse'] = $return;
                        echo json_encode( $respose );
                        exit();
                }

            
        }else{
                    header("HTTP/1.1 400 Bad Request");
                    $return['codigoError'] = "1";
                    $return['detalleError'] = "No se encontro la reserva";
                    $return['fechaHora'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode( $respose );
                    exit();
        }
            
    }else{
        header("HTTP/1.1 405 Method Not Allowed");
        $return['codigoError'] = "10";
        $return['detalleError'] = "Metodo no permitido";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode( $respose );
        exit();
    }