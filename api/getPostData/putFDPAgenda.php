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
    require_once "../clases/fdpAgenda.php";

    //verificacion de token
   
    $dbConn = new Conexion();
    $fechHora = date('d-m-Y H:i:s');

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
    // GET - listar adeudo por numero de abonado o por documento
    /*
                        "fecha":"'.$fecha.'",
                    "idAgenda":"'.$idAgenda.'",
                    "idUsuario":"'.$idUsuario.'",
                    "fdpUsuario":"'.$fdpUsuario.'",
                    "idInvitado1":"'.$idInvitado1.'"
                    "fdpInvitado1":"'.$fdpInvitado1.'",
                    "idInvitado2":"'.$idInvitado2.'",
                    "fdpInvitado2":"'.$fdpInvitado2.'",
                    "idInvitado3":"'.$idInvitado3.'",
                    "fdpInvitado3":"'.$fdpInvitado3.'"*/

    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
        $input = json_decode(file_get_contents('php://input'), true);

        foreach($input as $clave=>$valor){
            if($clave === "fecha"){$fecha = $valor;}
            if($clave === "idAgenda"){$idAgenda = $valor;}
            if($clave === "idUsuario"){$idUsuario = $valor;}
            if($clave === "fdpUsuario"){$fdpUsuario = $valor;}
            if($clave === "idInvitado1"){$idInvitado1 = $valor;}
            if($clave === "fdpInvitado1"){$fdpInvitado1 = $valor;}
            if($clave === "idInvitado2"){$idInvitado2 = $valor;}
            if($clave === "fdpInvitado2"){$fdpInvitado2 = $valor;}
            if($clave === "idInvitado3"){$idInvitado3 = $valor;}
            if($clave === "fdpInvitado3"){$fdpInvitado3 = $valor;}
            if($clave === "impUsu"){$impUsu = $valor;}
            if($clave === "impInv1"){$impInv1 = $valor;}
            if($clave === "impInv2"){$impInv2 = $valor;}
            if($clave === "impInv3"){$impInv3 = $valor;}


        }
     
        //obtengo el usuario




        if($idAgenda){
           
            $agenda = new FdpAgenda(null, $fecha, $idAgenda, $idUsuario, $fdpUsuario, $idInvitado1, $fdpInvitado1, 
            $idInvitado2, $fdpInvitado2, $idInvitado3, $fdpInvitado3,$impUsu,$impInv1,$impInv2,$impInv3);
            $agenda->guardar();

            
            
                //si no hay registro
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "0";
                $return['detalleError'] = "PAGOS GUARDADOS Correctamente";
                $return['agenda']=$idAgenda;
                $return['fecha']=$fecha;
                $respose['consultaResponse'] = $return;

                echo json_encode( $respose );
                exit();
        }else{
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "1";
            $return['detalleError'] = "NO LLEGO INFORMACIÓN DE LA FDP AGENDA";
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