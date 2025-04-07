<?php
       require_once "../api/utils.php";
       
       //$token = $_POST['token'];
       $token = $_COOKIE['goCookToken'];
       $fecha = $_POST['fecha'];
       $cancha = $_POST['cancha'];
       

       //$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W3siYWxnIjoiSFMyNTYiLCJ0eXAiOiJKV1QifSx7ImlhdCI6MTczNDI3NTE3NCwiZXhwIjoxNzM0ODc5OTc0LCJkYXRhIjp7ImlkIjpudWxsLCJuYW1lIjoiMzgyMDkwMDIiLCJjaSI6MzgyMDkwMDJ9fV0.7SCiNwfdg8qx3YR8pgTxYFYcJdFilYNhuaTGcnT84yg';
       //$fecha = "2024-12-15";
       //$cancha = "1";
       $ret = getHorarios($token,$fecha,$cancha);
       //verifico cedula correcta
       if($ret){
              //echo "Cedula correcta<br>" ;
                echo($ret);
              //echo "mande datos a put<br>" ;
              //ya el sistema de registro envia al mail y al cel un link para actualizar el usuario.
              
       }else{
              //echo "Cedula incorrecta<br>" ;
              $arrReturn = array();
              array_push($arrReturn,["Status" => 400, "descrip"=> 'Cedula invalida']);
              $datos = json_encode($arrReturn);
              return $datos;
       }
       
?>