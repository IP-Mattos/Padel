<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

date_default_timezone_set("America/Montevideo");

    require_once '../Conexion.php';
   
    
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
 
            $dbConn = new Conexion();
            include "_userToken.php";
            ///tabla localidad
            $tabla = "usuarios";
            
            // GET - listar todos las localidad o solo una*/
            if ($_SERVER['REQUEST_METHOD'] == 'GET'){  
                try{
                    foreach(getallheaders()as $clave=>$valor){
                        if($clave === "UsUsuario"){
                            $usuario = $valor;
                        }
                        if($clave === "UsPasword"){
                            $pasword = $valor;
                        }
                    }
                } catch(PDOException $e){
                    //echo $e->getMessage();
                }
               
                try{
                    $input = json_decode(file_get_contents('php://input'), true);
                
                    foreach($input as $clave=>$valor){
                        if($clave === "UsUsuario"){
                            $usuario = $valor;
                        }
                        if($clave === "UsPasword"){
                            $pasword = $valor;
                        }
                    }
                } catch(PDOException $e){
                    //echo $e->getMessage();
                }

                if (isset($usuario) && isset($pasword)){
                        
                        $sql = $dbConn->prepare("SELECT id,nombre,mail,usuario,estado,cedula,celular,categoria,vigencia FROM "
                        .$tabla." where usuario=:UsUsuario and pass=:UsPasword");
                        //print_r($sql);
                        $sql->bindValue(':UsUsuario', $usuario);
                        $sql->bindValue(':UsPasword', $pasword);
                        $sql->execute();
                        $data = $sql->fetch(PDO::FETCH_ASSOC);

                    //si traigo usuario creo el tocken
                    if($data){
                        $arryTok=getTocken($data);
                        
                        $sql = "Insert into " . TABLA . 
                        "(TkInicio,TkUsuario,TkToken,TkValido) values (".
                            $arryTok["inicio"].",".$data["id"].",'".$arryTok["token"]."',".$arryTok["vence"].")";
                            //print_r($sql ." \n");
                            $statement = $dbConn->prepare($sql);
                            //getAllValues($statement, $input);
                            try {
                                $statement->execute();
                                header("HTTP/1.1 200 OK");
                                header("Authorization:".$arryTok["token"]);
                                header("userId:".$data["id"]);
                                header("userUser:".$data["usuario"]);
                                header("userCi:".$data["cedula"]);
                                header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
                                $return['result'] = "OK";
                                $return["Authorization"] = $arryTok["token"];
                                echo json_encode($return);
                                exit();
                            } catch (PDOException $e) {
                                $return['Error'] = 'Error generando token';
                                $return['result'] = "False";
                                header("HTTP/1.1 401 ERROR");
                                echo json_encode($return);
                                exit();
                            }
                    }else{
                            $return['codigoError'] = "20";
                            $return['Error'] = 'Error de autentificación';
                            $return['result'] = "False";
                            header("HTTP/1.1 401 ERROR");
                            echo json_encode($return);
                            exit();
                        
                    }
                       
                    
                }else{
                    $return['Error'] = 'BODY ROW FALSE';
                    $return['result'] = "False";
                    header("HTTP/1.1 401 ERROR");
                    echo json_encode($return);
                    exit();
                }
                
            }

    ?>