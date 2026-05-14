<?php
// test-push.php — delete this file when done testing!
require_once './api/push/send-notification.php';

sendPushToUser(73, '🎾 GO Padel', 'Esto es una prueba!', '/landing.php');
echo json_encode(['ok' => true]);