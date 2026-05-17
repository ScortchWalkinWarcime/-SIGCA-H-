<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

try {
    $users = Database::query('SELECT cve_usuario, nombre AS nombre_usuario, correo AS email, rol FROM usuario ORDER BY nombre');
    echo json_encode(['status' => 'ok', 'data' => $users]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
