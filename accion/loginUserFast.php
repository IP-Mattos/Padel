<?php
session_start();
       require_once "../api/utils.php";
       $celular = castCelular598($_POST['celular']);//;
       $cedula = $_POST['cedula'];
       if(isset($_COOKIE['goCookToken'])){
              $cookie = $_COOKIE['goCookToken'];
       }else{
              $cookie = "";
       }
       $codigoError="1";

       //$celular = castCelular598("098104106");
       //$cedula = "38209002";

       //verifico cedula correcta
       if(validateCI($cedula)){
              //echo "Cedula correcta<br>" ;
              $arry = array();

              $ret = setAccesUserFast($celular,$cedula,$cookie);
             
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
                
    
                     }
                     if($codigoError!=0){
                            session_destroy();
                     }
                   
                    //header('location:../landing.php');
                     //print_r("cookie existe valor = ".$_COOKIE['goCookToken']);
                   
              }
             // echo '<br>$_SESSION["userId"]= '.$_SESSION["userId"] ;
             // echo '<br>$_SESSION["userCi"] = '.$_SESSION["userCi"] ;
            //  echo '<br>$_SESSION["userUser"] = '.$_SESSION["userUser"] ;
             // echo '<br>$_SESSION["userToken"] = '.$_SESSION["userToken"] ;
            //  echo '<br>$_SESSION["userCategoria"] = '.$_SESSION["userCategoria"] ;
              

              
       }else{
              //echo "Cedula incorrecta<br>" ;
              $arrReturn = array();
              array_push($arrReturn,["Status" => 400, "descrip"=> 'Cedula invalida']);
              $datos = json_encode($arrReturn);
              
              //return $datos;

       }
       
?>