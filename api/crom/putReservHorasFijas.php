<?php
//date_default_timezone_set('America/Montevideo');

echo "Hora actual: ".date('d-m-Y H:i:s');
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set("America/Montevideo");
   
    
    //echo "entro a metodo";

    require_once "../utils.php";
    require_once "../Conexion.php";
    require_once "../clases/servicios.php";
    require_once "../token/funcToken.php";
    require_once "../clases/usuarios.php";
    require_once "../clases/agenda.php";
    require_once "../clases/horaFija.php";
    //verificacion de token

    //el dia de hoy en numero
    $diaHoy = date("N")+1;
    //echo "<br>Dia de hoy: ". $diaHoy;
    //busqueda de horas fijas del dia de hoy
    $horasFijasHoy = HoraFija::buscarPorDia($diaHoy);

    foreach ($horasFijasHoy as $hf) {
        $servicio = $hf->getServicio();
        $usuario = $hf->getIdUser();
        $hora = $hf->getHora();
        //fecha de hoy mas 7 dias
        $fecha = date("Y-m-d", strtotime("+7 days"));
       
       // echo "<br>Servicio: ". $servicio. ", Usuario: ". $usuario. ", Hora: ". $hora . " - Fecha ".$fecha;
           
            $agenda = new Agenda(null,$fecha,$hora,$usuario,1,date("Y-m-d H:i:s"),$servicio,0,0,"",0,0,0);
            $agenda->guardar();
            //si $agenda quedo guardodo doy mensaje de ok
            if($agenda->getId()){
                header("HTTP/1.1 200 OK");
                $return['codigoError'] = "0";
                $return['detalleError'] = "Hora fija reservada correctamente";
                $return['userId']=$usuario;
                $return['fecha']=$fecha;
                $return['hora']=$hora;
                $return['servicio']=$servicio;
                $respose['consultaResponse'] = $return;
                echo json_encode( $respose );
            } else{
                header("HTTP/1.1 500 Internal Server Error");
                $return['codigoError'] = "500";
                $return['detalleError'] = "Error al reservar la hora fija";
                $return['userId'] = "";
                $return['fecha'] = "";
                $return['hora'] = "";
                $return['servicio'] = "";
                $respose['consultaResponse'] = $return;
                echo json_encode( $respose );
            }
    }
?>