<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

try {
    $data = Database::query('SELECT cve_alerta, tipo_severidad, mensaje, fecha FROM alertas ORDER BY fecha DESC');
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
