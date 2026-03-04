<?php
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
        CURLOPT_POSTFIELDS =>'{}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        var_dump($response);
        curl_close($curl);
?>