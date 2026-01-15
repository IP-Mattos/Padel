<?php
session_start();
       require_once "../api/utils.php";
       $fechHora = date('Y-m-d H:i:s');
       $codigoError = "1";

       $nombre =$_POST['nombre'];
       $celular = castCelular598($_POST['celular']);//;
       $mail = "";//$_POST['mail'];
       $cedula = $_POST['cedula'];
       $cud = $_POST['cud'];

       //verifico cedula correcta
       if(validateCI($cedula)){
              //echo "Cedula correcta<br>" ;
              $arry = array();
              
              $ret = putNewUser($celular,$nombre,$cedula,$mail,$cud);
              //echo json_decode($ret);
             
              $arry = json_decode($ret,true);
              foreach($arry['confirmacionResponse'] as $key=>$value){

                     switch($key){
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
                                  // echo 'clave = '.$key.' - Valor = ' . $value."<br>";
                    
                    
              }
            
            //  pisarCookieToken($token);
              //echo "mande datos a put<br>" ;
              //ya el sistema de registro envia al mail y al cel un link para actualizar el usuario.
              
       }else{
              //echo "Cedula incorrecta<br>" ;
              $return = array();
              $return['codigoError'] = "50";
              $return['result'] = "False";
              $return['mensaje'] = "Cedula invalida";
              $return['fechaHora'] = $fechHora;
              $response['confirmacionResponse'] = $return;
              $datos = json_encode($response);
              echo $datos;

             
       }
       //echo "session User id = ".$_SESSION["userId"];
?>