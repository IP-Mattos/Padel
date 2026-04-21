<?php
    
    
    require_once '../Conexion.php';
    $dbConn = new Conexion();

    include "../utils.php";
    //$dbConn =  connect($db);

    /* 
        1	UsId	int(11) 
        2	UsNombre 	varchar(50) 
        3	UsUsuario 	varchar(50) 
        4	UsPasword 	varchar(150) 
        5   UsEstudio 	int(11) 
        6	UsEstado 	int(11) 

    */
    
    ///tabla Usuarios
    $tabla = "usuario";
    
    // GET - listar todos las localidad o solo una*/
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){   
        $input = json_decode(file_get_contents('php://input'), true);
        foreach($input as $clave=>$valor){
            if($clave === "UsId"){
                $id = $valor;
            }
        }

        if (isset($id)){

            //Mostrar un dato
                $sql = $dbConn->prepare("SELECT * FROM "
                .$tabla." where UsId=:UsId");
                $sql->bindValue(':UsId', $id);
                $sql->execute();
                $sql->setFetchMode(PDO::FETCH_ASSOC);
                header("HTTP/1.1 200 OK");
                echo json_encode( $sql->fetchAll() );
                exit();

        }else{
            
            $return['Error'] = 'BODY ROW FALSE';
            $return['result'] = "False";
            header("HTTP/1.1 401 ERROR");
            echo json_encode($return);
            exit();
        }
           
    }

    // POST - Crear una nuevo
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $input = json_decode(file_get_contents('php://input'), true);
        //print_r($input);
        if(count($input)==5){
            $sql = "INSERT INTO ".$tabla."
                (".getKeyParams($input).")
                VALUES
                (".getKeyValues($input).")";
            //print_r($sql);
            $statement = $dbConn->prepare($sql);
           
            try {
                $statement->execute();
                
            } catch (PDOException $e) {
                $return['exep-cod'] =($e->getCode());
                $return['exep'] = msg_exeption($e->getCode(),$e->getMessage());
                /*$return['exep-msg'] =($e->getMessage());
                $return['exep-cod'] =($e->getCode());
                $return['exep-prev'] =($e->getPrevious());
                $return['exep-file'] =($e->getFile());
                $return['exep-line'] =($e->getLine());
                $return['exep-trace'] =($e->getTrace());
                $return['exep-traceStr'] =($e->getTraceAsString());*/

            }
           

            //print_r($statement->errorInfo());
            $postId = $dbConn->lastInsertId();
            if($postId){
                $return['id'] = $postId;
                $return['result'] = "True";
                header("HTTP/1.1 200 OK");
                echo json_encode($return);
                exit();
            }else{
                $return['Error'] = 'RECORD SAVE';
                $return['result'] = "False";
                header("HTTP/1.1 402 ERROR");
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

    //Borrar
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $input = json_decode(file_get_contents('php://input'), true);
        foreach($input as $clave=>$valor){
            if($clave === "UsId"){
                $id = $valor;
            }else{
                $return['Error'] = 'BODY ROW FALSE';
                $return['result'] = "False";
                header("HTTP/1.1 401 ERROR");
                echo json_encode($return);
                exit();
            }
        }
        $statement = $dbConn->prepare("DELETE FROM ".$tabla." where UsId=:UsId");
        $statement->bindValue(':UsId', $id);
        try {
            $statement->execute();
            
        } catch (PDOException $e) {
            $return['exep-cod'] =($e->getCode());
            $return['exep'] = msg_exeption($e->getCode(),$e->getMessage());
            /*$return['exep-msg'] =($e->getMessage());
            $return['exep-cod'] =($e->getCode());
            $return['exep-prev'] =($e->getPrevious());
            $return['exep-file'] =($e->getFile());
            $return['exep-line'] =($e->getLine());
            $return['exep-trace'] =($e->getTrace());
            $return['exep-traceStr'] =($e->getTraceAsString());*/

        }

        if($statement->rowCount()>0){
            $return['result'] = "true";
            header("HTTP/1.1 200 OK");
            echo json_encode($return);
            exit();
        }else{
            $return['Error'] = 'RECORD SAVE';
            $return['result'] = "False";
            header("HTTP/1.1 402 ERROR");
            echo json_encode($return);
            exit();
        }
    }

    //Actualizar
    if ($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $input = json_decode(file_get_contents('php://input'), true);
        
        $postId = $input['UsId'];
        $fields = getParams($input);

        if($postId){
            $sql = "
            UPDATE ".$tabla."
            SET ".$fields." 
            WHERE UsId='$postId'
            ";
            //echo $sql;
            $statement = $dbConn->prepare($sql);
            bindAllValues($statement, $input);
            try {
                $statement->execute();
               
            } catch (PDOException $e) {
                $return['exep-cod'] =($e->getCode());
                $return['exep'] = msg_exeption($e->getCode(),$e->getMessage());
                /*$return['exep-msg'] =($e->getMessage());
                $return['exep-cod'] =($e->getCode());
                $return['exep-prev'] =($e->getPrevious());
                $return['exep-file'] =($e->getFile());
                $return['exep-line'] =($e->getLine());
                $return['exep-trace'] =($e->getTrace());
                $return['exep-traceStr'] =($e->getTraceAsString());*/
    
            }
            
            if($statement->rowCount()>0){
                $return['result'] = "true";
                header("HTTP/1.1 200 OK");
                echo json_encode($return);
                exit();
            }else{
                $return['Error'] = 'RECORD SAVE';
                $return['result'] = "False";
                header("HTTP/1.1 402 ERROR");
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


    //En caso de que ninguna de las opciones anteriores se haya ejecutado
    header("HTTP/1.1 499 Bad Request");

?>