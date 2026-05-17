<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

try {
    $data = Database::query('SELECT cve_agua, cve_usuario, area, cloro, ph, potabilidad, temperatura, turbidez, dureza, metales_pesados, observaciones, fecha FROM control_agua ORDER BY fecha DESC');
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
