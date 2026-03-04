<?php
     require_once '../Conexion.php';
     //error_reporting(E_ALL);
     //ini_set('display_errors', '1');
     date_default_timezone_set("America/Montevideo");

    function validateToken($tok){
       try{
            $preSql = "SELECT TkValido,TkId,TkInicio,TkUsuario,TkToken FROM usertoken WHERE TkToken=:TkToken";
            $dbConn = new Conexion();
            $sql = $dbConn->prepare($preSql);
            $sql->bindValue(':TkToken', $tok);
            $sql->execute();
            //$sql->setFetchMode(PDO::FETCH_ASSOC);
            $miToken = $sql->fetch();
            $valido = time();

            if(isset($miToken["TkId"])){
                if($miToken["TkToken"]===$tok && $miToken["TkValido"]>$valido){
                    return true;
                }else{
                    if($miToken["TkValido"] >= $valido){
                        //print_r("caduco la fecha");
                    }
                    if($miToken["TkToken"] != $tok){
                        //print_r("token no encontrado");
                    }
                    return false;
                }
            }else{
                return false;
            }
            
       }catch(Exception $e){
         return false;
       }


    }
    function UsuarioToken($tok){
    
        $preSql = "SELECT TkUsuario FROM usertoken where TkToken=:TkToken order by TkId desc limit 1";
        $dbConn = new Conexion();
        $sql = $dbConn->prepare($preSql);
        $sql->bindValue(':TkToken', $tok);
        $sql->execute();
        //$sql->setFetchMode(PDO::FETCH_ASSOC);
        $miToken = $sql->fetch();
        $valido = time();
        return $miToken["TkUsuario"];

    }

    function getUsUsuario($idUser){
        $preSql = "SELECT nombre FROM usuarios where id=:idUser";
        $dbConn = new Conexion();
        $sql = $dbConn->prepare($preSql);
        $sql->bindValue(':idUser', $idUser);
        $sql->execute();
        //$sql->setFetchMode(PDO::FETCH_ASSOC);
        $miToken = $sql->fetch();
        return $miToken["nombre"];
    }
    function EliminarTockenOut($now){
    
        $preSql = "DELETE FROM usertoken where TkValido < " . $now;
        $dbConn = new Conexion();
        echo $preSql;
        $sql = $dbConn->prepare($preSql);
        $sql->execute();
        //$sql->setFetchMode(PDO::FETCH_ASSOC);

        return true;

    }


?>