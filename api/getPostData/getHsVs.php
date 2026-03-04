<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set("America/Montevideo");
   
    
    //echo "entro a metodo";

    require_once "../utils.php";
    require_once "../Conexion.php";
    require_once "../clases/servicios.php";
    require_once "../clases/usuarios.php";
    require_once "../token/funcToken.php";
    require_once "../clases/agenda.php";
    
    //verificacion de token
   
    $dbConn = new Conexion();
    $fechHora = date('d-m-Y h:i:s');

    $token = "";
    $valToken =false;


    foreach (getallheaders() as $nombre => $valor) {
        //echo "$nombre: $valor\n";
        //header("Authorization:".$token);
        if($nombre === 'Authorization'){
            $token = $valor;
            $valToken =validateToken($valor)==true;
        }
    }
    if ($valToken==false){
        //token invalido
        $return['codigoError'] = "10";
        $return['detalleError'] = "Token invalido";
        $return['fechaHora'] = $fechHora;
        header("HTTP/1.1 401 ERROR");
        ///echo json_encode($return);
        exit();
    }

    //echo "antes de methodo";
    // GET - listar adeudo por numero de abonado o por documento*/
    if ($_SERVER['REQUEST_METHOD'] == 'GET'){   
        $input = json_decode(file_get_contents('php://input'), true);

        foreach(getallheaders()as $clave=>$valor){
            if($clave === "estado"){$estado = $valor;}
            if($clave === "idUser"){$idUser = $valor;}
          
        }

        foreach($input as $clave=>$valor){
            if($clave === "estado"){$estado = $valor;}
            if($clave === "idUser"){$idUser = $valor;}

        }
        
                    $partidoActivo = Agenda::tieneReserva($idUser);



                    // $sql = $dbConn->prepare("SELECT fecha,hora,idUsuario,estado,timeEstado,servicio FROM agenda where fecha=:fecha and servicio=:servicio and hora=".$i);
                    $sql = "SELECT id,fecha,hora,idUsuario,estado,idUserRival,estadoRival,mensaje,timeEstado,servicio 
                    FROM agenda where servicio = 4 and estado =".$estado ." and estadoRival=0 and idUserRival =0";
                    //echo "sql =".$sql;
                    $consql = $dbConn->prepare($sql);
                    $consql->execute();
                    $consql->setFetchMode(PDO::FETCH_ASSOC);
                    $registro = $consql->fetchAll();
                    $verPartido = false;
                    $laHora = array();

                    if($registro){
                        foreach($registro as $reg){
                            if($reg['estado']==1){
                                $horaOcupada = 1;
                            }else{
                                $horaOcupada = 0;
                            }
                            //para cada registro de partido, debo fijarme categoria y con quien juega quien arma
                            // entonces categoria = x
                            // si juega con  = 0 solo es en su categoria,,, si juega con = 1 hasta 1 categoria mayor y otra menor
                            $usuarioArmado = Usuarios::buscarPorId($reg["idUsuario"]);
                             // echo "<br>iduser".$idUser;
                                  
                            $usuarioRival = Usuarios::buscarPorId($idUser);
                            switch ($usuarioArmado->getMascategoria()){
                                case 0: // solo juega con la misma categoria
                                  
                                    if($usuarioArmado->getCategoria() == $usuarioRival->getCategoria()){
                                        //si es asi, es que el posible contrincante puede ver el partido
                                        $verPartido = true;
                                    }else{
                                        $verPartido = false;
                                    }
                                    break;
                                case 1:
                                    //  echo "<br>categoria armador".$usuarioArmado->getCategoria();
                                   // echo "<br>categoria rival".$usuarioRival->getCategoria();
                                    //en este caso el armador de partido jueba en su categoria y una mas arriba y una mas abajo
                                    $diferencia = ($usuarioArmado->getCategoria() - $usuarioRival->getCategoria());
                                    if($diferencia < 1 or $diferencia > -1 ){
                                        //si es asi, es que el posible contrincante puede ver el partido
                                        $verPartido = true;
                                    }else{
                                        $verPartido = false;
                                    }
                                    break;
                                  
                                case 2:
                                    //en este caso el armador de partido juega en cualquier categoria
                                    $verPartido = true;

                                    break;
                            }
                            if($verPartido){
                                $horas['hora'] = $reg['hora'];
                                $horas['estado'] = $horaOcupada;
                                $horas['idReserva'] = $reg['id'];
                                $horas['fecha'] = $reg['fecha'];
                                $horas['servicio'] = $reg["servicio"];
                                $horas['timeEstado'] = $reg['timeEstado'];
                                $horas['idUsuario'] = $reg['idUsuario'];
                                $horas['idRival'] = $reg['idUserRival'];
                                $horas['estadoRival'] = $reg['estadoRival'];
                                $horas['mensaje'] = $reg['mensaje'];
                                $horas['partidoActivo'] = $partidoActivo;
                                array_push($laHora,$horas);
                            }
                        }
                   
                    }

            if($laHora){

                //$datos = json_encode($string);
                    
               
                    header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "0";
                    $return['detalleError'] = "OK";
                    $return['fechaHora'] = $fechHora;
                    $return['datos'] = $laHora;
                    $return['partidoActivo'] = $partidoActivo;
                    $respose['consultaResponse'] = $return;
                    echo json_encode( $respose );
                    exit();
                
            }else{
                //si no hay registro
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "1";
                $return['detalleError'] = "No se encontraron datos para esta consulta";
                $return['fechaHora'] = $fechHora;
                $return['partidoActivo'] = $partidoActivo;
                $respose['consultaResponse'] = $return;
                echo json_encode( $respose );
                exit();
            }


           
    }else{
            header("HTTP/1.1 499 Bad Request");
            $return['fechaHora'] = $fechHora;
            $return['TipDocumento'] = $tipDocumento;
            $respose['consultaResponse'] = $return;
            echo json_encode( $respose );
            exit();
    }


?>