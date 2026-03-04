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
            if($clave === "idUser"){$idUser = $valor;}
            if($clave === "idReserva"){$idReserva = $valor;}
            if($clave === "idInvitado"){$idInvitado = $valor;}
           
           
        }
     
        //obtengo el usuario




        if($idReserva>0 && $idInvitado>0){
            $acutalizar = false;
            $agenda = Agenda::buscarPorId($idReserva);
                    if($agenda->getIdUserRival()==$idInvitado){
                        $agenda->setIdUserRival(0);
                        $acutalizar = true;
                    }
                    if($agenda->getInvitado1() == $idInvitado){
                        $agenda->setInvitado1(0);
                        $acutalizar = true;
                    }
                    if($agenda->getInvitado2() ==  $idInvitado){
                        $agenda->setInvitado2(0);
                        $acutalizar = true;
                    }
                    if($agenda->getInvitado3() ==  $idInvitado && $agenda->getIdUserRival()==0){
                        $agenda->setInvitado3(0);
                        $acutalizar = true;
                    }
                    if ($acutalizar == false){
                        header("HTTP/1.1 400 Bad Request");
                        $return['codigoError'] = "3";
                        $return['detalleError'] = "No se encuentra el invitado para eliminar";
                        $return['fechaHora'] = $fechHora;
                        $respose['consultaResponse'] = $return;
                        echo json_encode( $respose );
                        exit();
                    }else{
                        $agenda->guardar();
                    
                        //si no hay registro
                        header("HTTP/1.1 200 OK");
                        $return['codigoError'] = "0";
                        $return['detalleError'] = "Usuario eliminado del partido";
                        $return['userId']=$agenda->getIdUsuario();
                        $return['fecha']=$fechHora;
                        $respose['consultaResponse'] = $return;
                        echo json_encode( $respose );
                        exit();
                    }

                    


           
        }else{
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "1";
            $return['detalleError'] = "Falta información sobre el partido o el invitado";
            $return['fechaHora'] = $fechHora;
            $respose['consultaResponse'] = $return;
            echo json_encode( $respose );
            exit();
        }
    }else{
        header("HTTP/1.1 405 Method Not Allowed");
        $return['codigoError'] = "10";
        $return['detalleError'] = "Método no permitido";
        $return['fechaHora'] = $fechHora;
        $respose['consultaResponse'] = $return;
        echo json_encode( $respose );
        exit();
    }