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
        $elDia = array();

        for ($i = 0; $i < 7; $i++) {
            //$fecha = $fehca + 1 dia
            //obtengo el dia de la fecha como nombre del dia de la semana
            $diaSemana = date('w', strtotime($fecha));

            $getServicio = Servicio::buscarPorServicioYUsuario($servicio,$profe,$dias[$diaSemana]);
            //obtengo los horarios disponibles para la servicio
            //var_dump($servicio);
            if($getServicio){
                foreach($getServicio as $serv){
                    if($serv[$dias[$diaSemana]]!=""){

                        $dia['dia'] = $dias[$diaSemana];
                        $dia['servicio'] = $serv["servicio"];
                        $dia['profe'] = $serv["usuario"];
                        $dia['estado'] = 0; //activo
                    }else{
                        $dia['dia'] = $dias[$diaSemana];
                        $dia['servicio'] = $serv["servicio"];
                        $dia['profe'] = $serv["usuario"];
                        $dia['estado'] = 1; //inactivo
                    }
                }
            }else{
                $dia['dia'] = $dias[$diaSemana];;
                $dia['servicio'] = $servicio;
                $dia['profe'] = $profe;
                $dia['estado'] = 1; // inactivo
            }
            $fecha = date('Y-m-d', strtotime($fecha.' +1 day'));
            array_push($elDia,$dia);
        }
        
 

            if($elDia){

                //$datos = json_encode($string);
                    
               
                    header("HTTP/1.1 200 OK");
                    $return['codigoError'] = "0";
                    $return['detalleError'] = "OK";
                    $return['fechaHora'] = $fechHora;
                    $return['datos'] = $elDia;
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
            header("HTTP/1.1 499 Bad Request");
            $return['fechaHora'] = $fechHora;
            $return['TipDocumento'] = $tipDocumento;
            $respose['consultaResponse'] = $return;
            echo json_encode( $respose );
            exit();
    }


?>