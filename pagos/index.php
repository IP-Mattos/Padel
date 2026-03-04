<?php

  error_reporting(E_ALL);
  ini_set("display_errors", 1);





  require __DIR__ . '/vendor/autoload.php';
  $acces_token = "APP_USR-3056284424026823-071415-30da7708f784dec66998e01df66a5a01-89581418";
  MercadoPago\SDK::setAccessToken($acces_token);
  $preferencia = new MercadoPago\Preference();

  $preferencia->back_urls = array(
    "success" => "http://www.gopadel.uy/pagos/rest/correcto.php",
    "failure" => "http://www.gopadel.uy/pagos/rest/falla.php",
    "pending" => "http://www.gopadel.uy/pagos/rest/pedi.php"

  );

  $productos=[];
  $item = new MercadoPago\Item();

  $item->title="nomProducto";
  $item->quantity=1;
  $item->unit_price = 200;
  array_push($productos,$item);

  $preferencia->items=$productos;
  $preferencia->save();
?>

<html>
<head>
    <!--
      SDK MercadoPago.js
       Para incluir el SDK de Mercado Pago.js, agrega el siguiente código al HTML del proyecto o instala la biblioteca para ReactJs.
    -->
    <script src="https://sdk.mercadopago.com/js/v2">
    </script>
</head>
<body>
    <div id="contenedro_btn">
    </div>
    <script>
      var publicKey = "APP_USR-932a4d47-0885-4207-afd6-3b7d2e07b039";
      const mp = new MercadoPago(publicKey, {
        locale: 'es-UY'
      });
      const checkout = mp.checkout({
        performance:{
          id:<?php echo $preferencia->id; ?>
        },
        render: {
          container: 'contenedro_btn',
          label: 'Pagar',
        }
      });
      //inicializa tu checkout usando el ID de la preferencia previamente creada con el identificador del elemento donde se debe mostrar el botón

  </script>
  
</body>
</html>

