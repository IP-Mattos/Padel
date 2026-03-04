
<?php
 //error_reporting(E_ALL);
 //ini_set('display_errors', '1');
 date_default_timezone_set("America/Montevideo");
   
    echo "cokie =" .$_COOKIE['goCookToken']."<br>";

    setcookie('goCookToken','', time()-1000); 

  
    //echo $_COOKIE['goCookToken']."<br>";
?>

<form method="post" action="../accion/loginUserFast.php">
    <input type="text" id="celular" name="celular" value="098104106" placeholder="celular">
    <input type="text" id ="cedula" name = "cedula" value="38209002" placeholder="cedula">
    <input type="text" id ="cud" name = "cud" value="123456" placeholder="cud">
    <input type="submit" value="ENVIAR">
</form>

<form method="post" action="../accion/loginUserToken.php">

    <input type="submit" value="ENTRAR CON LA COKIE">
    
</form>