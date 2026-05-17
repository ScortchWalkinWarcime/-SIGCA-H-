<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['area']) || !isset($input['lectura'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
    exit;
}

try {
    $usuario = $_SESSION['cve_usuario'] ?? null;
    $area = trim($input['area']);
    $lectura = trim($input['lectura']);
    $fecha = date('Y-m-d H:i:s');

    Database::insert('INSERT INTO control_agua (cve_usuario, area, lectura, fecha) VALUES (:usuario, :area, :lectura, :fecha)', [
        'usuario' => $usuario,
        'area' => $area,
        'lectura' => $lectura,
        'fecha' => $fecha
    ]);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
