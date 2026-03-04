<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set("America/Montevideo");
   
    
    //echo "entro a metodo";

    require_once "../utils.php";
    require_once "../Conexion.php";
    require_once "../clases/servicios.php";
    require_once "../clases/usuarios.php";
    require_once "../token/funcToken.php";
    require_once "../clases/movimientos.php";
    
    //verificacion de token
   
    $dbConn = new Conexion();
    $fechHora = date('d-m-Y h:i:s');

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
        ///echo json_encode($return);
        exit();
    }

    //echo "antes de methodo";
    // GET - listar adeudo por numero de abonado o por documento*/
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){   
        $input = json_decode(file_get_contents('php://input'), true);

        foreach(getallheaders()as $clave=>$valor){

            if($clave === "idPerfil"){$idPerfil = $valor;}
          
        }

        foreach($input as $clave=>$valor){

            if($clave === "idPerfil"){$idPerfil = $valor;}

        }

            $usuario = Usuarios::buscarPorId($idPerfil);
       

                //$datos = json_encode($string);
            if($usuario){
               $saldoUsuario = Movimiento::obtenerSaldoUsuario($usuario->getId());
                    header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "0";
                    $return['detalleError'] = "OK";
                    $return['fechaHora'] = $fechHora;
                    $return['id']= $usuario->getId();
                    $return['nombre']= $usuario->getNombre();
                    $return['mail']= $usuario->getMail();
                    $return['usuario']= $usuario->getUsuario();
                    $return['celular']= $usuario->getCelular();
                    $return['categoria']= $usuario->getCategoria();
                    $return['vigencia']= $usuario->getVigencia();
                    $return['beneficiarioDe']= $usuario->getBeneficiarioDe();
                    $return['juego']= $usuario->getJuego();
                    $return['fechnac']= $usuario->getFechnac();
                    $return['frase']= $usuario->getFrase();
                    $return['profesor']= $usuario->getProfesor();
                    $return['mascategoria']= $usuario->getMascategoria();
                    $return['imgperfil']= $usuario->getImgperfil();
                    $return['isadmin']= $usuario->getisadmin();
                    $return['misestrellas']= $usuario->getMisestrellas();
                    $return['puntos'] = $saldoUsuario;

                    $respose['consultaResponse'] = $return;
                    echo json_encode( $respose );
                    exit();
                
            }else{
                header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "1";
                    $return['detalleError'] = "Usuario no encontrado";
                    $return['fechaHora'] = $fechHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode( $respose );
                    exit();
            }


           
    }else{
            header("HTTP/1.1 499 Bad Request");
             $return['codigoError'] = "4";
            $return['detalleError'] = "METODO NO SOPORTADO";
            $return['fechaHora'] = $fechHora;
            $respose['consultaResponse'] = $return;
            echo json_encode( $respose );
            exit();
    }


?>