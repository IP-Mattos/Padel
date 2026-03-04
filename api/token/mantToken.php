<?php

    date_default_timezone_set("America/Montevideo");
    $fechHora = time();

    include("funcToken.php");

    EliminarTockenOut($fechHora);

?>