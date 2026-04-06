<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

date_default_timezone_set("America/Montevideo");

    require_once '../Conexion.php';
    require_once "../utils.php";
    require_once "../token/funcToken.php";
    require_once "../clases/movimientos.php";
    require_once "../clases/deuda.php";
    //include "../utils.php";
    //    $dbConn =  connect($db);

    /* 
        id       Primaria	int(11)			No	Ninguna		AUTO_INCREMENT	Cambiar Cambiar	Eliminar Eliminar	
	2	nombre	varchar(250)	utf16_spanish_ci		No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	3	mail	varchar(250)	utf16_spanish_ci		No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	4	usuario	varchar(250)	utf16_spanish_ci		No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	5	pass	varchar(250)	utf16_spanish_ci		No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	6	estado	int(11)			No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	7	cedula Índice	int(11)			No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	8	celular	varchar(20)	utf16_spanish_ci		No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	9	categoria	int(11)			No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	10	vigencia	date			No	Ninguna			Cambiar Cambiar	Eliminar Eliminar	
	11	beneficiarioDe	int(11)			No	Ninguna			Cambiar Cambiar	Eliminar Eliminar

    */

    $fechHora = date("Y-m-d H:i:s");
    $usSMS = false;
    $mensajeSMS = "";
            $dbConn = new Conexion();
            include "_userToken.php";
            ///tabla localidad
            $tabla = "usuarios";
            $response = array();
            $return = array();

            // GET - listar todos las localidad o solo una*/
            if ($_SERVER['REQUEST_METHOD'] == 'GET'){  
                try{
                    foreach(getallheaders()as $clave=>$valor){
                        if($clave === "UsToken"){
                            $UsToken = $valor;
                        }
                    }
                } catch(PDOException $e){
                    //echo $e->getMessage();
                }
               
                try{
                    $input = json_decode(file_get_contents('php://input'), true);
                
                    foreach($input as $clave=>$valor){
                        if($clave === "UsToken"){
                            $UsToken = $valor;
                        }
                    }
                } catch(PDOException $e){
                    //echo $e->getMessage();
                }
                    //echo $UsToken;
                    $datUser = decodeTocken($UsToken);
                    
                    $id = $datUser[1]->data->id;
                   
        
                    
                    
                  
                

               

                if ($id>0){
                        
                        $sql = $dbConn->prepare("SELECT id as UsId,nombre,mail,usuario,estado,cedula,celular,categoria,vigencia,juego,
                        fechnac,frase,imgperfil,mascategoria,isadmin,profesor,misestrellas FROM "
                        .$tabla." where id=:UsId");
                       // print_r($sql);
                       // print_r($UsCl);
                      //  print_r("    ");
                       // print_r($UsCd);
                        $sql->bindValue(':UsId', $id);
                        $sql->execute();
                        $data = $sql->fetch(PDO::FETCH_ASSOC);

                    //si traigo usuario creo el tocken
                    if($data){

                        /// si el token es valido
                        if(validateToken($UsToken)==TRUE){
                            //comparo el usuario del token con el usuario que se esta logueando
                            if(UsuarioToken($UsToken) === $data['UsId']){
                                //si esto pasa no mando mensaje y devuelvo sms false
                                //echo "<BR>ENTRO A VALIDAR false = ".$UsToken;
                                $usSMS = false;
                            }else{
                                //si esto pasa el usuario no es el mismo y devuelvo sms true
                                $usSMS = true;
                                //echo "<BR>ENTRO A VALIDAR true por ese igualdad = ".UsuarioToken($UsToken). " <> " .$data['UsId'];
                            }
                        }else{
                            $usSMS = true;
                            //echo "<BR>ENTRO A VALIDAR true por ese validacion = ".$UsToken;
                        }




                        //echo 'PIDO UN NUEVO TOKEN\n';
                        $arryTok=getTocken($data);
                        
                            $sql = "Insert into " . TABLA . 
                            "(TkInicio,TkUsuario,TkToken,TkValido) values (".
                            $arryTok["inicio"].",".$data["UsId"].",'".$arryTok["token"]."',".$arryTok["vence"].")";
                            //print_r($sql ." \n");
                            $statement = $dbConn->prepare($sql);
                            $statement->execute();
                            //getAllValues($statement, $input);
                            try {
                               
                                $puntos = Movimiento::obtenerSaldoUsuario($data["UsId"]);       
                                $deuda = Deuda::obtenerSaldoActual($data["UsId"]);
                                
                                header("HTTP/1.1 200 OK");
                                header("Authorization:".$arryTok["token"]);
                                header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
                                $return['codigoError'] = "0";
                                $return['result'] = "OK";
                                $return['userId']=$data["UsId"];
                                $return['userUser']=$data["usuario"];
                                $return['userCi']=$data["cedula"];
                                $return['userCel']=$data["celular"];
                                $return['userJuego']=$data["juego"];
                                $return['userFechNac']=$data["fechnac"];
                                $return['userMail']=$data["mail"];
                                $return['userFrase']=$data["frase"];
                                $return['userCategoria']=$data["categoria"];
                                $return['userNombre']=$data["nombre"];
                                $return['userImgPerfil']=$data["imgperfil"];
                                $return['userMasCategoria']=$data["mascategoria"];
                                $return['isAdmin']=$data["isadmin"];
                                $return['profesor'] = $data['profesor'];
                                $return['misEstrellas'] = $data['misestrellas'];
                                $return['puntos'] = $puntos;
                                $return['deuda'] = $deuda;
                                $return['usSMS']=$usSMS;
                                $return['mensaje'] = $mensajeSMS;
                                $return['fechaHora'] = $fechHora;
                                $return['token'] = $arryTok["token"];
                                $response['confirmacionResponse'] = $return;
                                echo json_encode($response);
                                return $response;

                            } catch (PDOException $e) {
                                header("HTTP/1.1 400 ERROR");
                                header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
                                $return['codigoError'] = "15";
                                $return['Error'] = 'Error generando token';
                                $return['result'] = "False";
                                $response['confirmacionResponse'] = $return;
                                echo json_encode($response);
                                return $response;
                              
                                exit();
                            }
                    }else{
                       
                        //echo 'NO traigo datos\n';
                        
                            header("HTTP/1.1 400 ERROR");
                            header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
                            $return['codigoError'] = "20";
                            $return['Error'] = 'Error, datos invalidos';
                            $return['result'] = "False";
                            $response['confirmacionResponse'] = $return;
                            echo json_encode($response);
                            return $response;
                            exit();
                        
                    }
                       
                    
                }else{
                    $return['Error'] = 'BODY ROW FALSE';
                    $return['result'] = "False";
                    header("HTTP/1.1 401 ERROR");
                    $response['confirmacionResponse'] = $return;
                    echo json_encode($response);
                    return $response;
                    exit();
                }
                
            }
                

    ?>