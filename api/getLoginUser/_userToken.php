<?php



require_once '../token/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set("America/Montevideo");

use Firebase\JWT\JWT;

CONST TABLA = "usertoken"; 

    /* 
        1	TkId    	int(11) 
        2	TkInicio 	datetime
        3	TkUsuario 	int(11)
        4	TkToken 	varchar(10000) 
        5   TkValido 	datetime 
       

    */
    
    


    function getTocken($dataUS){
        //print_r("entro a generar token \n");
        $time = time();
    
        $key = "sha1WithRSAEncryption";
    
        $headerToken = array(
            "alg"=> "HS256",
            "typ"=> "JWT"
        );
        $inicio =$time;
        $fin =$time + (60*60*24*7); // Tiempo que expirará el token (+7 dias) 


        $payloadToken = array(
            
            'iat' => $inicio, // Tiempo que inició el token
            'exp' => $fin, // Tiempo que expirará el token (+4 hora)  
            'data' => [ // información del usuario
                'id' => $dataUS["UsId"],
                'name' => $dataUS["usuario"],
                'ci' => $dataUS["cedula"]
            ]
        );

        $token = array();
            array_push($token,$headerToken);
            array_push($token,$payloadToken);

        $jwt = JWT::encode($token, $key);
        //print_r("token generardo \n");
        $arryToken=array();
        $arryToken["token"]=$jwt;
        $arryToken["inicio"]=$inicio;
        $arryToken["vence"]=$fin;
        return $arryToken;
    
    }
    
    function decodeTocken($token){
        $key = "sha1WithRSAEncryption";
        $data = JWT::decode($token, $key, array('HS256'));
    
        return($data);
    }
