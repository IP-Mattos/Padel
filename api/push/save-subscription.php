<?php
// ============================================================
// api/push/save-subscription.php
// Recibe el objeto de suscripción del cliente y lo guarda en BD
// ============================================================
// add at the top after session_start or however you handle sessions
session_start();
$user_id = $_SESSION['userId'] ?? null;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once './../Conexion.php'; // ajustá la ruta a tu Conexion.php

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['endpoint'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Suscripción inválida']);
    exit;
}

$endpoint = $data['endpoint'];
$p256dh = $data['keys']['p256dh'] ?? '';
$auth = $data['keys']['auth'] ?? '';

try {
    $db = new Conexion();

    // Creá esta tabla en tu BD (ejecutar una sola vez):
    // CREATE TABLE push_subscriptions (
    //   id         INT AUTO_INCREMENT PRIMARY KEY,
    //   endpoint   TEXT NOT NULL,
    //   p256dh     VARCHAR(255),
    //   auth       VARCHAR(255),
    //   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //   UNIQUE KEY uq_endpoint (endpoint(200))
    // );

    $stmt = $db->prepare(
        'INSERT INTO push_subscriptions (endpoint, p256dh, auth, user_id)
     VALUES (:endpoint, :p256dh, :auth, :user_id)
     ON DUPLICATE KEY UPDATE p256dh = :p256dh_update, auth = :auth_update, user_id = :user_id_update'
    );
    $stmt->execute([
        ':endpoint' => $endpoint,
        ':p256dh' => $p256dh,
        ':auth' => $auth,
        ':user_id' => $user_id,
        ':p256dh_update' => $p256dh,
        ':auth_update' => $auth,
        ':user_id_update' => $user_id,
    ]);

    echo json_encode(['ok' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}