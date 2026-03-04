<?php
 error_reporting(E_ALL);
 ini_set('display_errors', '1');
    require_once "../api/utils.php";
    $res = acreditUser($_GET['ced']);
       header('Location:../landing.php?acredit='.$res);

    

?>