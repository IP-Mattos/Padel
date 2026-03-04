<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set("America/Montevideo");
    require_once "../Conexion.php";
    
    $dbConn = new Conexion();
    $fechHora = date('d-m-Y h:i:s');

    require_once "../utils.php";


    //verificacion de token
    require_once "../token/funcToken.php";
    require_once '../getLoginUser/_userToken.php';
   

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
        echo json_encode($return);
        exit();
    }else{
        $data = decodeTocken($token);
        echo "data: ".json_encode($data->data);
        $datoTok = $data['data'];
        header("HTTP/1.1 200 OK");
        $return['codigoError'] = "0";
        $return['detalleError'] = "OK";
        $return['validToken'] = "true";
        $return['decodeToken'] = $datoTok ;
        $respose['consultaResponse'] = $return;
        echo json_encode( $respose );
        exit();
    }

    

?>