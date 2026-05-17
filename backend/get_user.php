<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

if (!isset($_SESSION['cve_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

try {
    $user = Database::query('SELECT cve_usuario, nombre AS nombre_usuario, correo AS email, rol FROM usuario WHERE cve_usuario = :id LIMIT 1', ['id' => $_SESSION['cve_usuario']]);
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        exit;
    }
    echo json_encode(['status' => 'ok', 'data' => $user[0]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
