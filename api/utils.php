<?php
require_once(__DIR__ . '/phpMailer/src/PHPMailer.php');
require_once(__DIR__ . '/phpMailer/src/POP3.php');
require_once(__DIR__ . '/phpMailer/src/SMTP.php');
require_once(__DIR__ . '/phpMailer/src/Exception.php');
require_once(__DIR__ . '/Conexion.php');
require_once(__DIR__ . '/push/send-notification.php');
use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set("America/Montevideo");

function enviar_mail_contacto($destino, $nombreDestino, $cc, $cco, $titulo, $cuerpo)
{

  // Load Composer's autoloader
  //$titulo = "=?UTF-8?B?".base64_encode($titulo)."=?=";
  //$nombreDestino = "=?UTF-8?B?".base64_encode($nombreDestino)."=?=";

  // Instantiation and passing `true` enables exceptions
  $mail = new PHPMailer(true);

  try {

    //Server settings
    $mail->SMTPDebug = 0;                      // Enable verbose debug output
    $mail->isSMTP();
    $mail->SMTPSecure = 'ssl';                                     // Send using SMTP
    $mail->Host = 'mail.gopadel.uy';                    // Set the SMTP server to send through
    $mail->SMTPAuth = true;                                   // Enable SMTP authentication
    $mail->Username = 'info@gopadel.uy';
    $mail->Password = 'neLN0n^bkT1*';      					// SMTP password
    $mail->CharSet = 'UTF-8';

    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('info@gopadel.uy', 'Equipo GO-PADEL');
    $mail->addAddress($destino, $nombreDestino);     // Add a recipient

    //$mail->addReplyTo('info@example.com', 'Information');
    if (isset($cc)) {
      $mail->addCC($cc);
    }			//si tiene con copia
    if (isset($cco)) {
      $mail->addBCC($cco);
    }

    // Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $titulo;
    $mail->Body = $cuerpo;
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    //echo 'Message enviado';
    return true;
  } catch (Exception $e) {
    //echo "Message Mailer Error: {$mail->ErrorInfo}";
    return false;
  }
}

//include "config.php";

//Abrir conexion a la base de datos
function connect($db)
{
  $options = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
  );
  try {
    $conn = new PDO("mysql:host={$db['host']};dbname={$db['db']};charset=utf8", $db['username'], $db['password'], $options);

    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $conn;
  } catch (PDOException $exception) {
    exit($exception->getMessage());
  }
}

//Obtener los campos enviados
function getKeyParams($input)
{
  $filterParams = [];
  foreach ($input as $param => $value) {
    $filterParams[] = "$param";
  }
  return implode(", ", $filterParams);
}

function getKeyValues($input)
{
  $filterParams = [];
  foreach ($input as $param => $value) {
    $filterParams[] = "'" . $value . "'";
  }
  return implode(", ", $filterParams);
}

//Obtener parametros para updates
function getParams($input)
{
  $filterParams = [];
  foreach ($input as $param => $value) {
    $filterParams[] = "$param=:$param";
  }
  return implode(", ", $filterParams);
}

//Asociar todos los parametros a un sql
function bindAllValues($statement, $params)
{
  foreach ($params as $param => $value) {
    $statement->bindValue(':' . $param, $value);
  }

  return $statement;
}

function msg_exeption($cod, $msg)
{
  $arrmsg = explode(":", $msg);
  $errs = array(
    "23000" => "Registro existente. " . $arrmsg[2],
    "1" => "Prueba. " . $msg,

  );
  $txt = "";
  foreach ($errs as $er => $value) {
    if ($er == $cod) {
      $txt .= $value;
    }
  }
  return $txt;
}



/// MARKETING
//mando mensjae a usuario por whatsapp
function setSMSUserGO($mensaje, $celular, $nombre)
{
  $plantilla = 'send_informa_generico';

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://marketing.mcn.com.uy/api/putSendWhats',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "usUsuario":"goPadel",
                    "usPasword":"goPadel-2982",
                    "usMensaje":"' . $mensaje . '",
                    "usCelular":"' . $celular . '",
                    "usNombre":"' . $nombre . '",
                    "usPlantilla":"' . $plantilla . '",
                     "usLink":""
                }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);


  curl_close($curl);
  //echo $response;




}



function setLogin($mensaje, $celular, $nombre, $cedula)
{

  $link = 'https://gopadel.uy/setLogin.php?cd=' . base64_encode($cedula) . '&cl=' . base64_encode($celular);

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://marketing.mcn.com.uy/api/putSendWhats',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "usUsuario":"goPadel",
                    "usPasword":"goPadel-2982",
                    "usMensaje":"' . $mensaje . '/n/n' . $link . ',
                    "usCelular":"' . $celular . '",
                    "usNombre":"' . $nombre . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;




}
///

///Conexion.php//mando mensjae a usuario por whatsapp
function setLoginUserFast($mensaje, $celular, $nombre)
{
  //$plantilla = 'send_informa_generico';
  if ($mensaje == "New") {
    $plantilla = 'go_activ_user';
    $mensaje = "Te damos la bienvenida a goPadel! Utiliza este código " . crearCodigo6_a();
  } elseif ($mensaje == "login") { //login
    $plantilla = 'go_user_key';
    $mensaje = "Usa este código " . crearCodigo6_a();
    //$mensaje = crearCodigo6();
  } elseif ($mensaje == "reservaMauro") { //cambio de contraseña
    $plantilla = 'go_user_key';
    $mensaje = "NUEVA RESERVA - " . $nombre;
    $celular = "59892474385";
  }
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://marketing.mcn.com.uy/api/putSendWhats',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "usUsuario":"goPadel",
                    "usPasword":"goPadel-2982",
                    "usMensaje":"' . $mensaje . '",
                    "usCelular":"' . $celular . '",
                    "usNombre":"' . $nombre . '",
                    "usPlantilla":"' . $plantilla . '",
                     "usLink":""
                }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);


  curl_close($curl);
  //echo $response;




}
function setActualizarEnvioClave()
{

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://soft.mcn.com.uy/mantenimiento/salud_cliente/suport/_setSMSwhat.php?idCliente=2982',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{}',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
}


///   USSERS
//envio mensaje whats de activacion de usuario
function setActiveUser($mensaje, $celular, $nombre, $cedula)
{

  // esto es para mandar la confirmación de la cuenta al celular
  $plantilla = 'go_activ_user';
  $link = base64_encode($cedula);


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://marketing.mcn.com.uy/api/putSendWhats',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                        "usUsuario":"goPadel",
                        "usPasword":"goPadel-2982",
                        "usMensaje":"' . $mensaje . '",
                        "usCelular":"' . $celular . '",
                        "usNombre":"' . $nombre . '",
                        "usPlantilla":"' . $plantilla . '",
                        "usLink":"' . $link . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  return $response;

}
//el usuario confirma ser el y se activa su usuario
function acreditUser($cedula)
{
  try {
    $dbConn = new Conexion();
    $cedu = base64_decode($cedula);
    $sql = "UPDATE usuarios  SET estado = 1 WHERE cedula = :ced LIMIT 1";
    echo $sql;
    $statementInsert = $dbConn->prepare($sql);
    $statementInsert->bindValue(':ced', $cedu);
    $statementInsert->execute();
    return true;
  } catch (Exception $e) {
    return false;
  }

}
//alta de nuevo usuario
function putNewUser($celular, $nombre, $cedula, $mail, $cud)
{

  $pass = base64_encode($cedula);

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putUsuario',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                        "UsId":"0",
                        "UsMail":"' . $mail . '",
                        "UsNombre":"' . $nombre . '",
                        "UsUsuario":"",
                        "UsPass":"' . $pass . '",
                        "UsCedula":"' . $cedula . '",
                        "UsCelular":"' . $celular . '",
                         "UsCud":"' . $cud . '"
                    }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
  return $response;

}

function pisarCookieToken($token)
{
  $nameCokie = 'goCookToken1';
  //$expire = time() + (60*60*24*7);
  $expire = time() + (60 * 60 * 24 * 7);

  setcookie($nameCokie, $token, $expire, '../;samesite=strict', "gopadel.uy", true);
}
function dropCookieToken($token)
{
  $nameCokie = 'goCookToken';
  //$expire = time() + (60*60*24*7);
  $expire = time() - (60 * 60 * 24);
  setcookie($nameCokie, $token, $expire);

}


//login user fast
function setAccesUserFast($celular, $cedula, $cookie)
{
  //print_r("<br> datos =".$celular." ".$cedula);
  $ced = base64_encode($cedula);
  $cel = base64_encode($celular);
  $token = $cookie;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getLoginUser/loginfast',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                        "UsCl":"' . $cel . '",
                        "UsCd":"' . $ced . '",
                        "UsToken":"' . $token . '"
                    }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));
  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;

}

//login user fast
function setAccesUserToken($token)
{
  //print_r("<br> datos =".$celular." ".$cedula);

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getLoginUser/loginToken',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                        "UsToken":"' . $token . '"
                    }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
  ));
  $response = curl_exec($curl);
  echo $response;
  return $response;
  curl_close($curl);

}

//traer horarios de un servicio y profe
function getHorarios($token, $fecha, $servicio, $profe)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getHorario',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "fecha":"' . $fecha . '",
                    "servicio":"' . $servicio . '",
                    "profe":"' . $profe . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

//traer horarios de un servicio y profe
function getProfes($token)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getProfes',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}
//traer dias de un servicio y profe
function getDias($token, $fecha, $servicio, $profe)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getDias',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "fecha":"' . $fecha . '",
                    "servicio":"' . $servicio . '",
                    "profe":"' . $profe . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

function getHorasUser($token, $fechaDesde, $fechaHasta, $idUser)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getHorasIdUser',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "fechaDesde":"' . $fechaDesde . '",
                    "fechaHasta":"' . $fechaHasta . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

function getHorasReservAdmin($token, $fechaDesde, $fechaHasta)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getHorasReservAdmin',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "fechaDesde":"' . $fechaDesde . '",
                    "fechaHasta":"' . $fechaHasta . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}


//traer PARTIDOS VS
function getHSvS($token, $estado, $user)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getHsVs',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                  "estado":"' . $estado . '",
                  "idUser":"' . $user . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

//validar token
function getValidToken($token)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getValidToken',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{}',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;
  return $response;


}

/// validar cedula
function validateCI($ci)
{
  $cod = "2987634";
  $ci = preg_replace('([^0-9])', '', $ci);
  // echo ("<br>ci=".$ci);
  $suma = 0;
  $dig = substr($ci, -1, 1);
  $valdig = 0;
  $i = 0;

  // echo ("<br>dig=".$dig);

  //echo "<br>largo ci = " .strlen($ci);

  for ($i = 0; $i < strlen($ci) - 1; $i++) {
    $valor = ($ci[$i] * $cod[$i]);

    //echo ("<br>valor=".$valor);

    $valorI = substr($valor, -1, 1);
    //echo ("<br>valorI=".$valorI);

    $suma = $suma + $valorI;
    //echo ("<br>suma=".$suma);

  }
  $valdig = substr(10 - substr($suma, -1, 1), -1, 1);
  //echo ("<br>valdig=".$valdig);


  if ($valdig == $dig) {
    return true;
  } else {
    return false;
  }

}

/// validar codigo6
function validaCodigo6($cod)
{
  //error_reporting(E_ALL);
  //ini_set('display_errors', '1');

  //$cod = "123456";
  //H1H2M1M2V1V2
  //H1+H2+M1+M2=V1V2
  //CODIGO =
  // 0  1  2  3  4  5
  // V2 M2 H1 M1 H2 V1



  $eleM = str_split($cod);
  //ordeno codigo
  //$ElemTime = $eleM[2] . $eleM[4] . $eleM[3] . $eleM[1];
  //echo "<br>ElemTime = ".$ElemTime;

  $ElemRes = intval($eleM[5] . $eleM[0]);
  //echo "<br>ElemRes = ".$ElemRes;

  $calcElem = intval($eleM[2] . $eleM[4]) + intval($eleM[3] . $eleM[1]);
  //echo "<br>calcElem = ".$calcElem;


  if ($calcElem == $ElemRes) {
    //el codigo es correcto
    //ahora ver si no esta vencido
    $fechaActual = strtotime(date("Y-m-d H:i:s"));

    //echo "<br>fechaActual = ".$fechaActual;

    $fechaCodigo = strtotime(date("Y-m-d " . $eleM[2] . $eleM[4] . ":" . $eleM[3] . $eleM[1] . ":00"));
    //echo "<br>fechaCodigo = ".$fechaCodigo;

    //echo round(abs($fechaActual - $fechaCodigo) / 60,2). " minute";
    $distancia = round(abs($fechaActual - $fechaCodigo) / 60, 2);
    //echo "<br>".$distancia. " minute";

    if ($distancia < 30) {
      $returnEr['codigoError'] = "0";
      $returnEr['result'] = "OK";
      $returnEr['codigo'] = $cod;
      $returnEr['mensaje'] = "Codigo valido";
      $returnEr['fechaHora'] = date("Y-m-d H:i:s");
      $response['confirmacionResponse'] = $returnEr;
      echo json_encode($response);
    } else {
      $returnEr['codigoError'] = "1";
      $returnEr['result'] = "FALSE";
      $returnEr['codigo'] = $cod;
      $returnEr['mensaje'] = "Codigo vencido";
      $returnEr['fechaHora'] = date("Y-m-d H:i:s");
      $response['confirmacionResponse'] = $returnEr;
      echo json_encode($response);
    }
    //echo "<br>distancia = ".$distancia;


  } else {
    $returnEr['codigoError'] = "2";
    $returnEr['result'] = "FALSE";
    $returnEr['codigo'] = $cod;
    $returnEr['mensaje'] = "Codigo invalido";
    $returnEr['fechaHora'] = date("Y-m-d H:i:s");
    $response['confirmacionResponse'] = $returnEr;
    echo json_encode($response);
  }

  return $response;
}
function crearCodigo6()
{
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  //$cod = "123456";
  //H1H2M1M2V1V2
  //H1+H2+M1+M2=V1V2
  //CODIGO =
  // 0  1  2  3  4  5
  // V2 M2 H1 M1 H2 V1
  $H = date("H");
  while (strlen($H) > 2) {
    $H = "0" . $H;
  }
  $m = date("i");
  while (strlen($m) > 2) {
    $m = "0" . $m;
  }
  $cod = $H . $m;
  $V = intval($H) + intval($m);
  while (strlen($V) > 2) {
    $V = "0" . $V;
  }
  $eV = str_split($V);
  $eH = str_split($H);
  $em = str_split($m);
  $eleM = $eV[1] . $em[1] . $eH[0] . $em[0] . $eH[1] . $eV[0];
  // ordeno codigo

  $returnEr['codigoError'] = "0";
  $returnEr['result'] = "OK";
  $returnEr['mensaje'] = "Codigo creado";
  $returnEr['codigo'] = $eleM;
  $returnEr['fechaHora'] = date("Y-m-d H:i:s");
  $response['confirmacionResponse'] = $returnEr;
  echo json_encode($response);
  return $eleM;

}
function crearCodigo6_a()
{
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  //$cod = "123456";
  //H1H2M1M2V1V2
  //H1+H2+M1+M2=V1V2
  //CODIGO =
  // 0  1  2  3  4  5
  // V2 M2 H1 M1 H2 V1
  $H = date("H");
  while (strlen($H) > 2) {
    $H = "0" . $H;
  }
  $m = date("i");
  while (strlen($m) > 2) {
    $m = "0" . $m;
  }
  $cod = $H . $m;
  $V = intval($H) + intval($m);
  while (strlen($V) > 2) {
    $V = "0" . $V;
  }
  $eV = str_split($V);
  $eH = str_split($H);
  $em = str_split($m);
  $eleM = $eV[1] . $em[1] . $eH[0] . $em[0] . $eH[1] . $eV[0];
  // ordeno codigo

  return $eleM;

}

/// casteo celular
function castCelular598($celular)
{
  //todo numeros
  if (strlen($celular) == 11 && substr($celular, 0, 3) == "598") {
    //estan todos los digitos
    //598--------
    // echo "<br>largo=11".$celular;
  } elseif (strlen($celular) == 9 && substr($celular, 0, 1) == "0") {
    //faltan digitos
    //099-----
    //deboremplazar la primera posicion si es cero por 598
    $celular = "598" . substr($celular, 1, 8);
    // echo "<br>largo=9".$celular;

  } elseif (strlen($celular) == 8) {
    //echo "<br>largo=8".$celular;
    //error
    $celular = "598" . $celular;

  } else {
    $celular = null;
  }
  return $celular;
}


/// USUARIO - ACTUALIZAR IMAGEN
function updateUserImage($token, $id, $newImg)
{

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/upImgUsuario',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "UsId":"' . $id . '",
                    "UsNewImg":"' . $newImg . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
  return $response;

}

/// USUARIO - ACTUALIZAR DATOS PERFIL
function updateUserPerfil($token, $id, $nombre, $mail, $usuario, $categoria, $fechnac, $frase, $mascategoria)
{

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/upUsuarioPerfil',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "UsId":"' . $id . '",
                    "UsNombre":"' . $nombre . '",
                    "UsMail":"' . $mail . '",
                    "UsUsuario":"' . $usuario . '",
                    "UsCat":"' . $categoria . '",
                    "UsFecNac":"' . $fechnac . '",
                    "UsFrase":"' . $frase . '",
                    "UsMasCat":"' . $mascategoria . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
  return $response;

}
///GET PERFIL
function getPerfil($token, $idPerfil)
{

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getPerfil',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "idPerfil":"' . $idPerfil . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
  return $response;

}
function getPerfiles($token, $filtroPerfil)
{

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getPerfiles',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "filtroPerfil":"' . $filtroPerfil . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
  return $response;

}
/// HORAS - RESERVA DE HORAS
function putReservHoras($token, $fecha, $servicio, $profe, $usuario, $arrHoras)
{

  //echo $arrHoras;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putReservHoras',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "fecha":"' . $fecha . '",
                    "servicio":"' . $servicio . '",
                    "arrHoras":"' . $arrHoras . '",
                    "usuario":"' . $usuario . '",
                    "profe":"' . $profe . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

/// HORAS - RESTRICT DE HORAS
function putRestrictHoras($token, $fecha, $servicio, $profe, $usuario, $arrHoras)
{

  //echo $arrHoras;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putRestrictHoras',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "fecha":"' . $fecha . '",
                    "servicio":"' . $servicio . '",
                    "arrHoras":"' . $arrHoras . '",
                    "usuario":"' . $usuario . '",
                    "profe":"' . $profe . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}
/// HORAS - CANCELAR RESERVA DE HORAS
function putReservHorasCancel($token, $idReserva, $idUser)
{

  //echo "idreserva = ".$idReserva;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putReservHorasCancel',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idReserv":"' . $idReserva . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

/// HORAS - CONFIRMAR RESERVA DE HORAS
function putReservHorasConfirm($token, $idReserva)
{

  //echo "idreserva = ".$idReserva;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putReservHorasConfirm',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idReserv":"' . $idReserva . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

/// HORAS - RESERVA DE HORAS VS
function putReservVs($token, $idReserva, $idRival, $mensaje)
{

  //echo $arrHoras;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putReservVs',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idReserva":"' . $idReserva . '",
                    "idRival":"' . $idRival . '",
                    "mensaje":"' . $mensaje . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}
/// HORAS - AGREGAR INVITADOS
function putReservInvitados($token, $idReserva, $idInvitado, $idUser)
{

  //echo $arrHoras;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putReservInvitados',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    // CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idReserva":"' . $idReserva . '",
                    "idInvitado":"' . $idInvitado . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

function putCamcelInvitado($token, $idReserva, $idInvitado, $idUser)
{

  //echo $arrHoras;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putCancelInvitado',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idReserva":"' . $idReserva . '",
                    "idInvitado":"' . $idInvitado . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

/// HORAS - RESTRICT DE HORAS
function putFDPAgenda($token, $fecha, $idAgenda, $idUsuario, $fdpUsuario, $idInvitado1, $fdpInvitado1, $idInvitado2, $fdpInvitado2, $idInvitado3, $fdpInvitado3, $impUsu, $impInv1, $impInv2, $impInv3)
{

  //echo $arrHoras;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putFDPAgenda',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "fecha":"' . $fecha . '",
                    "idAgenda":"' . $idAgenda . '",
                    "idUsuario":"' . $idUsuario . '",
                    "fdpUsuario":"' . $fdpUsuario . '",
                    "idInvitado1":"' . $idInvitado1 . '",
                    "fdpInvitado1":"' . $fdpInvitado1 . '",
                    "idInvitado2":"' . $idInvitado2 . '",
                    "fdpInvitado2":"' . $fdpInvitado2 . '",
                    "idInvitado3":"' . $idInvitado3 . '",
                    "fdpInvitado3":"' . $fdpInvitado3 . '",
                    "impUsu":"' . $impUsu . '",
                    "impInv1":"' . $impInv1 . '",
                    "impInv2":"' . $impInv2 . '",
                    "impInv3":"' . $impInv3 . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

//traer FDP AGENDA
function getFDP($token, $idAgenda)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getFDP',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                  "idAgenda":"' . $idAgenda . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

function putCanje($token, $puntos, $idUser)
{

  //echo "idreserva = ".$idReserva;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putCanje',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "puntos":"' . $puntos . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

function putCanjeConfirm($token, $idCanje, $idUser)
{

  //echo "idreserva = ".$idReserva;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putCanjeConfirm',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idCanje":"' . $idCanje . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}
function putCanjeCancel($token, $idCanje, $idUser)
{

  //echo "idreserva = ".$idReserva;
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putCanjeCancel',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idCanje":"' . $idCanje . '",
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}


function getCanjeUser($token, $idUser)
{


  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/gerCanjeUser',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "idUser":"' . $idUser . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;



}

function putTorneos($token, $id, $categoria, $fecha, $nombre, $entre, $estado)
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putTorneos',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "id":"' . $id . '",
                    "categoria":"' . $categoria . '",
                    "fecha":"' . $fecha . '",
                    "nombre":"' . $nombre . '",
                    "entre":"' . $entre . '",
                    "estado":"' . $estado . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;
}

function putTorneoAspirante($token, $accion, $idTorneo, $idUsuario, $estado = 0, $id = "")
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putTorneoAspirante',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "accion":"' . $accion . '",
                    "id":"' . $id . '",
                    "idTorneo":"' . $idTorneo . '",
                    "idUsuario":"' . $idUsuario . '",
                    "estado":"' . $estado . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;
}

// Agregar esta función al archivo utils.php existente

function putDeudaCobro($token, $idUsuario, $monto, $origen, $detalle = "")
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putDeudaCobro',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idUsuario":"' . $idUsuario . '",
                    "monto":"' . $monto . '",
                    "origen":"' . $origen . '",
                    "detalle":"' . $detalle . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;

}

function putHoraFija($token, $idUsuario, $dia, $hora, $servicio, $accion)
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/putHoraFija',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => '{
                    "idUsuario":"' . $idUsuario . '",
                    "dia":"' . $dia . '",
                    "hora":"' . $hora . '",
                    "servicio":"' . $servicio . '",
                    "accion":"' . $accion . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;

}

function getDeudaUsuarios($token)
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getDeudaUsuarios',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{}',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;

}

function getTorneos($token, $estado = "", $fechaDesde = "", $fechaHasta = "")
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getTorneos',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "estado":"' . $estado . '",
                    "fechaDesde":"' . $fechaDesde . '",
                    "fechaHasta":"' . $fechaHasta . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;
}

function getTorneoAspirantes($token, $idTorneo)
{
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.gopadel.uy/api/getPostData/getTorneoAspirantes',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
                    "idTorneo":"' . $idTorneo . '"
                }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: ' . $token,
      'Content-Type: application/json'
    ),
  ));

  $response = curl_exec($curl);
  echo $response;
  curl_close($curl);
  return $response;
}

?>