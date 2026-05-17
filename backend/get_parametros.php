<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';

if (!isset($_GET['area'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Área requerida']);
    exit;
}

$area = trim($_GET['area']);
try {
    $result = Database::query('SELECT temp_min, temp_max FROM parametros_temperatura WHERE nombre_area = :area LIMIT 1', ['area' => $area]);
    if (!$result) {
        echo json_encode(null);
        exit;
    }
    echo json_encode($result[0]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error del servidor']);
}
