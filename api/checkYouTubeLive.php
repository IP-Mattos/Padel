<?php
header('Content-Type: application/json');

// Reemplaza con tu API KEY de YouTube
$youtube_api_key = 'AIzaSyBl_Kyb_nsznetptIL5oKTP-u4BYLyhg30';
$channel_id = 'UCcjex9unkd8U_Jm-bmOXeYA'; // ID del canal de YouTube

$response = array('isLive' => false);

try {
    if ($youtube_api_key === 'AIzaSyBl_Kyb_nsznetptIL5oKTP-u4BYLyhg30' || $channel_id === 'UCcjex9unkd8U_Jm-bmOXeYA') {
        // Si no están configurados, devolver false
        echo json_encode($response);
        exit;
    }

    // URL de la API de YouTube para buscar transmisiones en vivo
    $search_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channel_id}&type=video&eventType=live&key={$youtube_api_key}&maxResults=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $search_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($result, true);
        
        if (isset($data['items']) && count($data['items']) > 0) {
            $response['isLive'] = true;
        }
    }
} catch (Exception $e) {
    // En caso de error, simplemente devolver que no hay transmisión
}

echo json_encode($response);
?>
