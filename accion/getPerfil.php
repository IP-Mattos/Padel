<?php
       require_once "../api/utils.php";
       
 
       $token = $_COOKIE['goCookToken'];
       $idPerfil = $_POST['idPerfil'];
       

       //$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W3siYWxnIjoiSFMyNTYiLCJ0eXAiOiJKV1QifSx7ImlhdCI6MTczNDI3NTE3NCwiZXhwIjoxNzM0ODc5OTc0LCJkYXRhIjp7ImlkIjpudWxsLCJuYW1lIjoiMzgyMDkwMDIiLCJjaSI6MzgyMDkwMDJ9fV0.7SCiNwfdg8qx3YR8pgTxYFYcJdFilYNhuaTGcnT84yg';
       //$fecha = "2024-12-15";
       //$cancha = "1";
       $ret = getPerfil($token,$idPerfil);
       //verifico cedula correcta
       if($ret){
              //echo "Cedula correcta<br>" ;
              //  echo($ret);
              //echo "mande datos a put<br>" ;
              //ya el sistema de registro envia al mail y al cel un link para actualizar el usuario.
              
              $arry = json_decode($ret,true);
              foreach($arry['confirmacionResponse'] as $key=>$value){
                            //echo $key."----" ;
                            //echo $value."<br>" ;
                     switch($key){
                            /* header("userId:".$data["id"]);
                                header("userUser:".$data["usuario"]);
                                header("userCi:".$data["cedula"]);*/ 
                                
                            case 'codigoError':
                                   $codigoError = $value;
                            break;
                            case 'userId':
                                   $_SESSION["userId"] = $value;
                            break;
                            case 'userUser':
                                   $_SESSION["userUser"] = $value;
                            break;
                            case 'userMail':
                                   $_SESSION["userMail"] = $value;
                            break;
                            case 'userCi':
                                   $_SESSION["userCi"] = $value;
                            break;
                            case 'token':
                                   $_SESSION["userToken"] = $value;
                            break;
                            case 'userCategoria':
                                   $_SESSION["userCategoria"] = $value;
                            break;
                            case 'userMasCategoria':
                                   $_SESSION["userMasCategoria"] = $value;
                            break;
                            case 'userNombre':
                                   $_SESSION["userNombre"] = $value;
                            break;
                            case 'userCel':
                                   $_SESSION["userCel"] = $value;
                            break;
                            case 'userJuego':    
                                   $_SESSION["userJuego"] = $value;
                            break;
                            case 'userFechNac':
                                 $_SESSION["userFechNac"] = $value;
                            break;
                            case 'userFrase':
                                   $_SESSION["userFrase"] =  $value;
                            break;
                            case 'userImgPerfil':
                                   $_SESSION["userImgPerfil"] =  $value;
                            break;
                            case 'isAdmin':
                                   $_SESSION["isAdmin"] =  $value;
                            break;
                            case 'misEstrellas':
                                   $_SESSION["misEstrellas"] =  $value;
                            break;
                            case 'puntos':
                                   $_SESSION["userPuntos"] =  $value;
                            break;
                            
                
    
                     }
              }


       }else{
              //echo "Cedula incorrecta<br>" ;
              $arrReturn = array();
              array_push($arrReturn,["Status" => 400, "descrip"=> 'Cedula invalida']);
              $datos = json_encode($arrReturn);



              
              return $datos;
       }
       
?>