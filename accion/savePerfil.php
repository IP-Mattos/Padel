<?php 

       session_start();

       error_reporting(E_ALL);
       ini_set('display_errors', '1');
       require_once "../api/utils.php";
       $token = $_COOKIE['goCookToken'];

       

        $nombre         =   $_POST['nombre'];
        $mail           =   $_POST['mail'];
        $usuario        =   $_POST['usuario'];
        $categoria      =   $_POST['categoria'];
        $fechnac        =   $_POST['fechnac'];
        $frase          =   $_POST['frase'];
        $mascategoria   =   $_POST['mascategorias']; // 0 defecto , 1 es categoria contigua y 2 es todas las categorias
        $id             =   $_POST['idUser'];

        
        $ret = updateUserPerfil($token,$id,$nombre,$mail,$usuario,$categoria,$fechnac,$frase,$mascategoria);

              //echo "Cedula correcta<br>" ;
              $arry = array();

             
              $arry = json_decode($ret,true);
              foreach($arry['consultaResponse'] as $key=>$value){

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
                            

                     }
                                  // echo 'clave = '.$key.' - Valor = ' . $value."<br>";
                    
                    
              }
            
            //  pisarCookieToken($token);
              //echo "mande datos a put<br>" ;
              //ya el sistema de registro envia al mail y al cel un link para actualizar el usuario.
              

       //echo "session User id = ".$_SESSION["userId"];
?>