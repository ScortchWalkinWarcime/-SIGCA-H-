<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

try {
    $data = Database::query(
        'SELECT ct.cve_temp, ct.id_area, a.nombre AS area, ct.valor, ct.fecha 
        FROM control_temperatura ct 
        JOIN areas a 
        ON ct.id_area = a.cve_area 
        ORDER BY ct.fecha DESC'
    );
    echo json_encode(['status' => 'ok', 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
