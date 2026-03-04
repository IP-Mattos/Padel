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
        echo json_encode($return);
        exit();
    }

    //echo "antes de methodo";
    // GET - listar adeudo por numero de abonado o por documento*/
    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){   
        $input = json_decode(file_get_contents('php://input'), true);

        foreach(getallheaders()as $clave=>$valor){
            if($clave === "UsId"){$id = $valor;}
            if($clave === "UsNombre"){$nombre = $valor;}
            if($clave === "UsMail"){$mail = $valor;}
            if($clave === "UsUsuario"){$usuario = $valor;}
            if($clave === "UsCat"){$categoria = $valor;}
            if($clave === "UsFecNac"){$fechnac = $valor;}
            if($clave === "UsFrase"){$frase = $valor;}
            if($clave === "UsMasCat"){$mascategoria = $valor;}

           
        }

        foreach($input as $clave=>$valor){
            if($clave === "UsId"){$id = $valor;}
            if($clave === "UsNombre"){$nombre = $valor;}
            if($clave === "UsMail"){$mail = $valor;}
            if($clave === "UsUsuario"){$usuario = $valor;}
            if($clave === "UsCat"){$categoria = $valor;}
            if($clave === "UsFecNac"){$fechnac = $valor;}
            if($clave === "UsFrase"){$frase = $valor;}
            if($clave === "UsMasCat"){$mascategoria = $valor;}

        }
     
        //obtengo el usuario
        if($id>0){
            $Usuario = Usuarios::buscarPorId($id);

            //actualizo la imagen actual
            $Usuario->setNombre($nombre);
            $Usuario->setMail($mail);
            $Usuario->setUsuario($usuario);
            $Usuario->setCategoria($categoria);
            $Usuario->setFechnac($fechnac);
            $Usuario->setFrase($frase);
            $Usuario->setMascategoria($mascategoria);

            $Usuario->guardar();

                //si no hay registro
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "0";
                $return['detalleError'] = "Datos actualizados correctamente";
                $return['userId']=$Usuario->getId();
                                $return['userUser']=$Usuario->getUsuario();
                                $return['userCi']=$Usuario->getCedula();
                                $return['userCel']=$Usuario->getCelular();
                                $return['userJuego']=$Usuario->getJuego();
                                $return['userFechNac']=$Usuario->getFechnac();
                                $return['userMail']=$Usuario->getMail();
                                $return['userFrase']=$Usuario->getFrase();
                                $return['userCategoria']=$Usuario->getCategoria();;
                                $return['userNombre']=$Usuario->getNombre();;
                                $return['userImgPerfil']=$Usuario->getImgperfil();;
                                $return['userMasCategoria']=$Usuario->getMascategoria();;
                                $return['isAdmin']=$Usuario->getisadmin();;
                                $return['misEstrellas'] =$Usuario->getMisestrellas();;
                                $return['fechaHora'] = $fechHora;
                                $return['token'] = $token;
                $respose['consultaResponse'] = $return;

                echo json_encode( $respose );
                exit();
        }else{
            header("HTTP/1.1 400 Bad Request");
            $return['codigoError'] = "1";
            $return['detalleError'] = "No se encontró el usuario";
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