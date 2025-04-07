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
                                   $userId = $value;
                                   $_SESSION["userId"] = $userId;
                            break;
                            case 'userUser':
                                   $userUser = $value;
                                   $_SESSION["userUser"] = $userUser;
                            break;
                            case 'userCi':
                                   $userCi = $value;
                                   $_SESSION["userCi"] = $userCi;
                            break;
                            case 'token':
                                   $token = $value;
                                   $_SESSION["userToken"] = $token;
                            break;
                            case 'categoria':
                                   $categoria = $value;
                                   $_SESSION["userCategoria"] = $categoria;
                            break;
                            case 'userName':
                                   $userName = $value;
                                   $_SESSION["userNombre"] = $userName;
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