<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['area']) || !isset($input['temp'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
    exit;
}

try {
    $usuario = $_SESSION['cve_usuario'] ?? null;
    $area = trim($input['area']);
    $temp = trim($input['temp']);
    $fecha = isset($input['fecha']) && $input['fecha'] !== '' ? date('Y-m-d', strtotime($input['fecha'])) : date('Y-m-d');

    $areaRow = Database::query('SELECT cve_area FROM areas WHERE nombre = :nombre LIMIT 1', ['nombre' => $area]);
    if (!$areaRow) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Área inválida']);
        exit;
    }

    $cve_area = $areaRow[0]['cve_area'];
    Database::insert('INSERT INTO control_temperatura (cve_usuario, id_area, valor, fecha) VALUES (:usuario, :area, :valor, :fecha)', [
        'usuario' => $usuario,
        'area' => $cve_area,
        'valor' => $temp,
        'fecha' => $fecha
    ]);

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
