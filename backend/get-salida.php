<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

try {
    $data = Database::query('SELECT cve_salida, producto, proveedor, estado, resultado, observaciones, temperatura, fecha, cve_usuario FROM salida_alimentos ORDER BY fecha DESC');
    echo json_encode(['status' => 'ok', 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}

?>
