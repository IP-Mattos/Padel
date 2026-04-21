<?php
// ============================================================
// generate-vapid-keys.php
// Ejecutar UNA sola vez desde la terminal:
//   php generate-vapid-keys.php
//
// Requiere la librería web-push:
//   composer require minishlink/web-push
// ============================================================

require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$keys = VAPID::createVapidKeys();

echo "=== Tus claves VAPID ===\n";
echo "PUBLIC KEY  (pegala en push-manager.js):\n";
echo $keys['publicKey'] . "\n\n";
echo "PRIVATE KEY (guardala en el servidor, nunca en el cliente):\n";
echo $keys['privateKey'] . "\n";