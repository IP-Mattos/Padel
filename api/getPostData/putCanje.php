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
    require_once "../clases/canje.php";
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
        $return['fechaHora'] = $nowFech;
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
            if($clave === "puntos"){$puntos = $valor;}
        }
     
        //obtengo el usuario




        if($idUser>0 && $puntos>0){
            $canje = Canje::recuperarTodos($idUser,0);
            if(count($canje)==0){
                    $canje = new Canje($nowFech,$idUser,$puntos,0);
                    $canje->guardar();

                    if ($canje->getId()==0){
                        header("HTTP/1.1 400 Bad Request");
                        $return['codigoError'] = "3";
                        $return['detalleError'] = "No se pudo registrar el canje de puntos, verifique que no tenga un caje pendiente de aprobar";
                        $return['fechaHora'] = $fechHora;
                        $respose['consultaResponse'] = $return;
                        echo json_encode( $respose );
                        exit();
                    }else{
                        
                    
                        //si no hay registro
                        header("HTTP/1.1 200 OK");
                        $return['codigoError'] = "0";
                        $return['detalleError'] = "Canje registrado correctamente, avise a el administrador para que apruebe el canje";
                        $return['userId']=$canje->getUsuario();
                        $return['fecha']=$fechHora;
                        $respose['consultaResponse'] = $return;
                        echo json_encode( $respose );
                        exit();
                    }

            }else{
                header("HTTP/1.1 400 Bad Request");
                $return['codigoError'] = "1";
                $return['detalleError'] = "Tiene canje pendiente de aprobar, no puede realizar otro canje";
                $return['fechaHora'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode( $respose );
                exit();
            }
        }else{
            header("HTTP/1.1 405 Method Not Allowed");
            $return['codigoError'] = "10";
            $return['detalleError'] = "FALTAN DATOS PARA PROCESAR EL CANJE";
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