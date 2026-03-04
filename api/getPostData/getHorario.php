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
    require_once "../token/funcToken.php";
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
            if($clave === "fecha"){$fecha = $valor;}
            if($clave === "servicio"){$servicio = $valor;}
            if($clave === "profe"){$profe = $valor;}
        }

        foreach($input as $clave=>$valor){
            if($clave === "fecha"){$fecha = $valor;}
            if($clave === "servicio"){$servicio = $valor;}
            if($clave === "profe"){$profe = $valor;}
        }
     
        //obtengo el dia de la fecha como nombre del dia de la semana
        $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
        $diaSemana = date('w', strtotime($fecha));
        //echo ("<br>busco servicio por servicio y usuario " .$servicio." y ".$profe." el dia ".$dias[$diaSemana]);
        $servicio = Servicio::buscarPorServicioYUsuario($servicio,$profe,$dias[$diaSemana]);
        //obtengo los horarios disponibles para la servicio
        //var_dump($servicio);
        foreach($servicio as $serv){
            $horasServicio = $serv["$dias[$diaSemana]"];
            $intrvaloServicio = $serv["intervalo"];
            $elServicio = $serv["servicio"];
        }
        //echo "<br>horasServicio = ".$horasServicio;

        //recorro los horariosServicio en un while
        $i = 0;
        $arrHoraServ = explode(",",$horasServicio);
        
            $horaDesde = strtotime($arrHoraServ[0]);
            $horaHasta = strtotime($arrHoraServ[1]);
            if($fecha == date('Y-m-d')){
                if($horaDesde < strtotime(date("H:i"))){
                    $horaDesde = strtotime((intval(date("H"))+1).":00");
                }
                //echo("<br>dia es igual y hora de horadesde ".$horaDesde);
            }
            

        //echo "<br>horaDesde = ".$horaDesde." horaHasta = ".$horaHasta;
        $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
        
        $laHora = array();
        $horas['diaSemana'] = $dias[$diaSemana];
        

        if($fecha){
                $string = "";
                $i=0;
                while($horaDesde <= $horaHasta){
                    $horaOcupada =0;
                    if($elServicio<=4){
                        $sqlServicio = " servicio <= 4 ";
                    }else{
                        $sqlServicio = " servicio = ".$elServicio;
                    }

                    // $sql = $dbConn->prepare("SELECT fecha,hora,idUsuario,estado,timeEstado,servicio FROM agenda where fecha=:fecha and servicio=:servicio and hora=".$i);
                    $sql = "SELECT id,fecha,hora,idUsuario,estado,timeEstado,servicio FROM agenda where fecha='".$fecha.
                    "' and ".$sqlServicio." and hora='".date("H:i",$horaDesde)."'";
                    //echo "sql =".$sql;
                    $consql = $dbConn->prepare($sql);
                    //echo "<br>sql: ".$sql. 
                    //$sql->bindValue(':fecha', $fecha);
                    //$sql->bindValue(':servicio', $servicio);
                    //echo $sql;
                    $consql->execute();
                    $consql->setFetchMode(PDO::FETCH_ASSOC);
                    $registro = $consql->fetchAll();
                    if($registro){
                        foreach($registro as $reg){
                            if($reg['estado']>0){
                                $horaOcupada = 1;
                            }else{
                                $horaOcupada = 0;
                            }
                        }
                        //array_push($array,$servicio,$i,$horaOcupada);
                        $horas['hora']          = date("H:i",$horaDesde);
                        $horas['estado']        = $reg['estado'];
                        $horas['idReserva']     = $reg['id'];
                        $horas['fecha']         = $reg['fecha'];
                        $horas['servicio']      = $reg["servicio"];
                        $horas['timeEstado']    = $reg['timeEstado'];
                        $horas['idUsuario']     = $reg['idUsuario'];

                        
                       
                       
                        
                        //$horas['sql'] = $sql;
                    }else{
                        $horas['hora']          = date("H:i",$horaDesde);
                        $horas['estado']        = $horaOcupada;
                        $horas['idReserva']     = "0";
                        $horas['fecha']         = "";
                        $horas['servicio']      = $elServicio;
                        $horas['timeEstado']    = "";
                        $horas['idUsuario']     = "";
                       
                      
                       
                        //$horas['sql'] = $sql;
                       // $string .= '{servicio: '.$servicio.', hora: '.$i.', estado: '.$horaOcupada.'}';
                    }
                    $horaDesde = strtotime("+".$intrvaloServicio." minute", $horaDesde);
                    $i=$i+1;
                    array_push($laHora,$horas);
                }
            //$datos = json_encode($string);

            if($laHora){

                //$datos = json_encode($string);
                    
               
                    header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "0";
                    $return['detalleError'] = "OK";
                    $return['fechaHora'] = $fechHora;
                    $return['datos'] = $laHora;
                    $respose['consultaResponse'] = $return;
                    echo json_encode( $respose );
                    exit();
                
            }else{
                //si no hay registro
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "1";
                $return['detalleError'] = "No se encontraron datos para esta consulta";
                $return['fechaHora'] = $fechHora;
                $respose['consultaResponse'] = $return;
                echo json_encode( $respose );
                exit();
            }

        }else{
             //si falta información
             header("HTTP/1.1 200 OK");
             $return['codigoError'] = "1";
             $return['detalleError'] = "Falta información para devolver la consulta";
             $return['fecha'] = $fecha;
             $return['servicio'] = $servicio;
             $return['fechaHora'] = $fechHora;
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