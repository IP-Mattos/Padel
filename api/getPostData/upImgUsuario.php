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
    require_once "../token/funcToken.php";
    require_once "../clases/usuarios.php";
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
    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
        $input = json_decode(file_get_contents('php://input'), true);

        foreach(getallheaders()as $clave=>$valor){
            if($clave === "UsId"){$id = $valor;}
            if($clave === "UsNewImg"){$img = $valor;}
           
        }

        foreach($input as $clave=>$valor){
            if($clave === "UsId"){$id = $valor;}
            if($clave === "UsNewImg"){$img = $valor;}
        }
     
        //obtengo el usuario
        $Usuario = Usuarios::buscarPorId($id);

        //elimino su imagen si tiene un guardada
        //borrar imagen guardada
        if($Usuario->getImgperfil()!= ""){
            unlink("../../accion/imgPerfilUser/".$Usuario->getImgperfil());
        }
        //actualizo la imagen actual
        $Usuario->setImgperfil($img);
        $Usuario->guardar();

                //si no hay registro
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "0";
                $return['detalleError'] = "Datos actualizados correctamente";
                $return['fechaHora'] = $fechHora;
                $return['newImg'] = $img;
                $respose['consultaResponse'] = $return;
                echo json_encode( $respose );
                exit();
   
    }else{
        header("HTTP/1.1 405 Method Not Allowed");
    }