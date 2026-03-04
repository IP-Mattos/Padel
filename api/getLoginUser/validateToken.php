<?php

        require_once '../Conexion.php';
        
        $dbConn = new Conexion();
    
    include "../utils.php";

    
    // GET - listar todos las localidad o solo una*/
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){   
        $input = json_decode(file_get_contents('php://input'), true);
        foreach($input as $clave=>$valor){
            if($clave === "UsToken"){ $token = $valor;  }
           
        }

        if (isset($token)){
            try{
                $decToken=decodeTocken($token);
            }catch (Exception $e) {
                $return['Error'] = 'BODY ROW FALSE';
                $return['result'] = "False";
                header("HTTP/1.1 401 ERROR");
                echo json_encode($return);
                exit();
            }
            

                header("HTTP/1.1 200 OK");
                header("Authorization:".$token);
                $data = ["datosToken"=>$decToken];
                header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
                echo json_encode($data);
                exit();
        }else{
            $return['Error'] = 'BODY ROW FALSE';
            $return['result'] = "False";
            header("HTTP/1.1 401 ERROR");
            echo json_encode($return);
            exit();
        }
           
    }



    ?>