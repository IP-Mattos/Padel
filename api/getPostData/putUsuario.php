<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set("America/Montevideo");

    require_once "../Conexion.php";
    $dbConn = new Conexion();
    include "../getLoginUser/_userToken.php";
    $fechHora = date('Y-m-d H:i:s');

    require_once "../utils.php";

    
    $SERVER_ENTRA = $_SERVER['REQUEST_METHOD'];



    //Actualizar
    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $datJson = json_decode(file_get_contents('php://input'), true);
        foreach($datJson as $clave=>$valor){
            if($clave === "UsId")         {$id        = $valor;}
            if($clave === "UsNombre")     {$nombre    = $valor;}
            if($clave === "UsMail")       {$mail      = $valor;}
            if($clave === "UsUsuario")    {$usuario   = $valor;}
            if($clave === "UsPass")       {$pass      = $valor;}
            if($clave === "UsCedula")     {$cedula    = $valor;}
            if($clave === "UsCelular")    {$celular   = $valor;}
            if($clave === "UsCud")        {$cud       = $valor;}
            
        }
        
        $estado = 1;
        $categoria =7;
        $vigencia = date("Y-m-d");
        $beneficiarioDe = 0;
        $response = array();
        $returnEr= array();
        
        if($mail==""){$mail=Null;}
        //$celular = castCelular598($celular);
        try{
            if($nombre && $celular!=null){
                $sql = "SELECT id FROM usuarios WHERE cedula = :cedula";
                $ejecut = $dbConn->prepare($sql);
                $ejecut->bindValue(':cedula', $cedula);
               
                $ejecut->execute();
                $dataOk = $ejecut->fetch(PDO::FETCH_ASSOC);
                if(isset($dataOK)){
                
                    header("HTTP/1.1 401 ERROR");
                    $returnEr['codigoError'] = "4";
                    $returnEr['mensaje'] = "Esta cedula ya esta registrada";
                    $returnEr['fechaHora'] = $fechHora;
                    $returnEr['nombre'] = $nombre;
                    $returnEr['usuario'] = $usuario;
                    $returnEr['cedula'] = $cedula;
                    $returnEr['mail'] = $mail;
                    $returnEr['celular'] = $celular;
                    $response['confirmacionResponse'] = $returnEr;
                    echo json_encode($response);
                    return $response;
                    exit();

                }else{
                
                    if($id==0){
                            $sql = "INSERT into usuarios  
                            (nombre,mail,usuario,pass,cedula,celular,estado,categoria,vigencia,beneficiarioDe,misestrellas) values 
                            ('".$nombre."','".$mail."','".$usuario."','".$pass."','".$cedula."','".$celular."','".$estado."','".$categoria.
                            "','".$vigencia."','".$beneficiarioDe."',3)";
                                //print $sql;
                            $statementInsert    =   $dbConn->prepare($sql);
                            $statementInsert->execute();
                            $idUsuario          =   $dbConn->lastinsertid();
                                
                            if ( intval($statementInsert->errorCode() )===0){
                                    $sql = "SELECT id as UsId,nombre,mail,usuario,estado,cedula,celular,categoria,vigencia,juego,fechnac,frase,misestrellas FROM usuarios WHERE id=:id";
                                    $ejecut = $dbConn->prepare($sql);
                                    
                                    $ejecut->bindValue(':id', $idUsuario);
                                    $ejecut->execute();
                                    
                                    $data = $ejecut->fetch(PDO::FETCH_ASSOC);

                                    //si traigo usuario creo el tocken
                                    if($data){
                                        $arryTok=getTocken($data);
                                    
                                        $sql = "Insert into " . TABLA . 
                                        "(TkInicio,TkUsuario,TkToken,TkValido) values (".
                                            $arryTok["inicio"].",".$data["UsId"].",'".$arryTok["token"]."',".$arryTok["vence"].")";
                                        //print_r($sql ." \n");
                                        $statement = $dbConn->prepare($sql);
                                        //getAllValues($statement, $input);
                                        try {
                                            $statement->execute();
                                            
                                            if($celular){
                                                //MANDO WHATSAPP
                                                $mensaje = 'New';
                                                $whatsSend = setLoginUserFast($mensaje,$celular,$nombre);
                                                //$ret['whatsSend'] = $whatsSend;
                                                $sneds = setActualizarEnvioClave();
                                                
                                            }

                                            header("HTTP/1.1 200 OK");
                                            header("Authorization:".$arryTok["token"]);
                                            header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
                                            $returnEr['codigoError'] = "0";
                                            $returnEr['result'] = "OK";
                                            $returnEr['mensaje'] = "Recibiras un WhatsApp para solicitar el codigo.";
                                            $returnEr['userId']=$data["UsId"];
                                            $returnEr['userUser']=$data["usuario"];
                                            $returnEr['userCi']=$data["cedula"];
                                            $returnEr['userCategoria']=$data["categoria"];
                                            $returnEr['userNombre']=$data["nombre"];
                                            $returnEr['userJuego']=$data["juego"];
                                            $returnEr['userFechNac']=$data["fechnac"];
                                            $returnEr['userFrase']=$data["frase"];
                                            $returnEr['usSMS']=true;
                                            $returnEr['fechaHora'] = $fechHora;
                                            $returnEr['token'] = $arryTok["token"];
                                            $returnEr['misEstrellas'] = $data['misestrellas'];
                                            $returnEr['puntos'] = '0';
                                            //$returnEr['recibo'] = $recibo;
                                            $response['confirmacionResponse'] = $returnEr;
                                            echo json_encode($response);
                                            return $response;
                                            
                                            exit();

                                        
                                        } catch (PDOException $e) {
                                                $returnEr['codigoError'] = "2";
                                                $returnEr['result'] = "False";
                                                $returnEr['mensaje'] = 'Error generando token';
                                                $returnEr['fechaHora'] = $fechHora;
                                                header("HTTP/1.1 401 ERROR");
                                                $response['confirmacionResponse'] = $returnEr;
                                                echo json_encode($response);
                                                return $response;
                                                exit();
                                        }  
                                            
                                    }else{
                                        //si tiene un pago cargado
                                            header("HTTP/1.1 200");
                                            $returnEr['codigoError'] = "1";
                                            $returnEr['result'] = "False";
                                            $returnEr['mensaje'] = $statementInsert->errorInfo();
                                            $returnEr['fechaHora'] = $fechHora;
                                            $returnEr['nombre'] = $nombre;
                                            $returnEr['usuario'] = $usuario;
                                            $returnEr['cedula'] = $cedula;
                                            $returnEr['mail'] = $mail;
                                            $returnEr['celular'] = $celular;
                                            $response['confirmacionResponse'] = $returnEr;
                                            echo json_encode($response);
                                            return $response;
                                            exit();
                                    }

                            }
                    }else{
                        header("HTTP/1.1 401 ERROR");
                        $returnEr['codigoError'] = "10";
                        $returnEr['result'] = "False";
                        $returnEr['mensaje'] = "Este usuario ya existe en el sistema.";
                        $returnEr['fechaHora'] = $fechHora;
                        $returnEr['nombre'] = $nombre;
                        $returnEr['usuario'] = $usuario;
                        $returnEr['cedula'] = $cedula;
                        $returnEr['mail'] = $mail;
                        $returnEr['celular'] = $celular;
                        $response['confirmacionResponse'] = $returnEr;
                        echo json_encode($response);
                        return $response;
                        exit();
                    }
                    //print_r ($sql);
                
                }
            
            }else{
                header("HTTP/1.1 401 ERROR");
                $returnEr['codigoError'] = "3";
                $returnEr['mensaje'] = "Faltan datos o el celular no esta correcto";
                $returnEr['fechaHora'] = $fechHora;
                $returnEr['nombre'] = $nombre;
                $returnEr['usuario'] = $usuario;
                $returnEr['cedula'] = $cedula;
                $returnEr['mail'] = $mail;
                $returnEr['celular'] = $celular;
                $response['confirmacionResponse'] = $returnEr;
                echo json_encode($response);
                return $response;
                exit();
            }

        } catch (PDOException $e) {
            $returnEr['codigoError'] = "15";
            $returnEr['mensaje'] = 'Error ' . $e->getMessage();
            $returnEr['result'] = "False";
            header("HTTP/1.1 401 ERROR");
            $response['confirmacionResponse'] = $returnEr;
            echo json_encode($response);
            return $response;
            exit();
        }
        
        
    }else{
        //En caso de que ninguna de las opciones anteriores se haya ejecutado
        header("HTTP/1.1 499 Bad Request");
           
            $returnEr['codigoError'] = "50";
            $returnEr['mensaje'] = "Metodo no soportado";
            $returnEr['METODO_INICIO'] = $SERVER_ENTRA;
            $returnEr['METODO_FIN'] = $_SERVER['REQUEST_METHOD'];
            $returnEr['fechaHora'] = $fechHora;
            $response['confirmacionResponse'] = $returnEr;
            echo json_encode($response);
            return $response;
            //echo json_encode( $respose );
    }


   

?>