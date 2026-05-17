<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

try {
    $data = Database::query(
        'SELECT ct.cve_temp, a.nombre AS area, ct.valor AS temp, ct.fecha FROM control_temperatura ct JOIN areas a ON ct.id_area = a.cve_area ORDER BY ct.fecha DESC LIMIT 100'
    );
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error del servidor']);
}
