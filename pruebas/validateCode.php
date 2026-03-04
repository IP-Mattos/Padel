
<?php

    require_once "../api/utils.php";
    //06:33 = 39
            // 0  1  2  3  4  5
            // V2 M2 H1 M1 H2 V1

 
    if(isset($_GET["cod"])){
        echo "<br>Entro a isset get cod";
        $cod = $_GET["cod"];
    }else{
       
        $cod = crearCodigo6();
    }
        
        sleep(1);
        echo("<br>$cod<br>");
        validaCodigo6($cod);
        


    



?>