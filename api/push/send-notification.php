<?php
// ============================================================
// api/push/send-notification.php
// Enviá una notificación a todos los suscriptores.
// Podés llamar a sendPushToAll() desde cualquier parte del
// backend (ej: cuando se confirma una reserva).
//
// Requiere: composer require minishlink/web-push
// ============================================================

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Conexion.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// ▸ Pegá aquí las claves generadas con generate-vapid-keys.php
define('VAPID_PUBLIC_KEY', 'BGhsYwW3JWMqSmeN_P2fP4PGnK9L8Nek4XA7AjfuKN8CUzW8bzLHRvOp5ntXBg_ou_9dglz79ZS_OtAfqprRwKE');
define('VAPID_PRIVATE_KEY', '_kN2mmFezWdq_ZIa4iuWIdl_4hae1rBmeoVm8vXN8Lw');
define('VAPID_SUBJECT', 'mailto:admin@gopadel.uy');

/**
 * Envía una notificación push a todos los suscriptores.
 *
 * @param string $title  Título de la notificación
 * @param string $body   Cuerpo del mensaje
 * @param string $url    URL que se abre al tocar (opcional)
 */
function sendPushToAll(string $title, string $body, string $url = '/'): void
{
    $auth = [
        'VAPID' => [
            'subject' => VAPID_SUBJECT,
            'publicKey' => VAPID_PUBLIC_KEY,
            'privateKey' => VAPID_PRIVATE_KEY,
        ],
    ];

    $webPush = new WebPush($auth);

    $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

    $db = new Conexion();
    $rows = $db->query('SELECT endpoint, p256dh, auth FROM push_subscriptions')->fetchAll();

    foreach ($rows as $row) {
        $subscription = Subscription::create([
            'endpoint' => $row['endpoint'],
            'keys' => ['p256dh' => $row['p256dh'], 'auth' => $row['auth']],
            'contentEncoding' => 'aesgcm',
        ]);
        $webPush->queueNotification($subscription, $payload);
    }

    // Enviar todo en batch y limpiar suscripciones expiradas
    foreach ($webPush->flush() as $report) {
        if ($report->isSubscriptionExpired()) {
            $stmt = $db->prepare('DELETE FROM push_subscriptions WHERE endpoint = ?');
            $stmt->execute([$report->getRequest()->getUri()->__toString()]);
        }
    }
}

// Send to a single user
function sendPushToUser(int $user_id, string $title, string $body, string $url = '/'): void
{
    $auth = [
        'VAPID' => [
            'subject' => VAPID_SUBJECT,
            'publicKey' => VAPID_PUBLIC_KEY,
            'privateKey' => VAPID_PRIVATE_KEY,
        ],
    ];

    $webPush = new WebPush($auth);
    $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

    $db = new Conexion();
    $stmt = $db->prepare('SELECT endpoint, p256dh, auth FROM push_subscriptions WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $subscription = Subscription::create([
            'endpoint' => $row['endpoint'],
            'keys' => ['p256dh' => $row['p256dh'], 'auth' => $row['auth']],
            'contentEncoding' => 'aesgcm',
        ]);
        $webPush->queueNotification($subscription, $payload);
    }

    foreach ($webPush->flush() as $report) {
        if ($report->isSubscriptionExpired()) {
            $stmt = $db->prepare('DELETE FROM push_subscriptions WHERE endpoint = ?');
            $stmt->execute([$report->getRequest()->getUri()->__toString()]);
        }
    }
}

// Send to multiple users by array of ids
function sendPushToUsers(array $user_ids, string $title, string $body, string $url = '/'): void
{
    foreach ($user_ids as $user_id) {
        sendPushToUser($user_id, $title, $body, $url);
    }
}


// ── Ejemplo de uso directo (para pruebas, llamar con ?test=1) ──────────────
if (isset($_GET['test'])) {
    sendPushToAll(
        title: '🎾 GO Padel',
        body: '¡Tu reserva fue confirmada!',
        url: '/index.php'
    );
    echo json_encode(['ok' => true, 'msg' => 'Notificación enviada']);
}