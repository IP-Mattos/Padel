<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    date_default_timezone_set("America/Montevideo");
    require_once "../Conexion.php";
    $dbConn = new Conexion();
    $fechHora = date('d-m-Y h:i:s');
    $timeSecuriti = new DateTime('-30 minutes');

    $sql = $dbConn->prepare("update abonados_pagos set estado = 1 where fechaHora < '".$timeSecuriti->format('Y-m-d H:i:s'). "' and estado =0");
    print_r($sql);
    $sql->execute();

?>