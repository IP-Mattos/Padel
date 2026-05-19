<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../api/clases/usuarios.php'; // adjust path to match your project

header('Content-Type: application/json');

$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
$mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';

if (!$userId || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$usuario = Usuarios::buscarPorId($userId);

if (!$usuario) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    exit;
}

$usuario->setMail($mail);
$usuario->guardar();

// TODO: here you can trigger sending the verification code to the email address
// e.g. call your existing SMS/code generation logic adapted for email

echo json_encode(['success' => true]);