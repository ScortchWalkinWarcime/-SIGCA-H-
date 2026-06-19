<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['area'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida: falta el campo area']);
    exit;
}

// Helper to normalize optional numeric values
/**
 * Normalize an optional numeric value to a float or null.
 * Accepts null, string, int or float.
 *
 * @param mixed $val
 * @return float|null
 */
function parse_nullable_number(mixed $val): ?float {
    if (!isset($val)) return null;
    if (is_int($val) || is_float($val)) return (float)$val;
    $val = trim((string)$val);
    if ($val === '') return null;
    // Use float for numeric measurements
    return is_numeric($val) ? (float)$val : null;
}

try {
    $usuario = $_SESSION['cve_usuario'] ?? null;
    $area = trim($input['area']);
    $ph = parse_nullable_number($input['ph'] ?? null);
    $cloro = parse_nullable_number($input['cloro'] ?? null);
    $observaciones = isset($input['observaciones']) ? trim($input['observaciones']) : null;
    $potabilidad = isset($input['potabilidad']) ? trim($input['potabilidad']) : null;
    $temperatura = parse_nullable_number($input['temperatura'] ?? null);
    $turbidez = parse_nullable_number($input['turbidez'] ?? null);
    $dureza = parse_nullable_number($input['dureza'] ?? null);
    $metales_pesados = isset($input['metales_pesados']) ? trim($input['metales_pesados']) : null;
    $fecha = date('Y-m-d H:i:s');

    $sql = 'INSERT INTO control_agua (ph, cloro, observaciones, cve_usuario, area, potabilidad, temperatura, turbidez, dureza, metales_pesados, fecha) VALUES (:ph, :cloro, :observaciones, :usuario, :area, :potabilidad, :temperatura, :turbidez, :dureza, :metales_pesados, :fecha)';

    $params = [
        'ph' => $ph,
        'cloro' => $cloro,
        'observaciones' => $observaciones,
        'usuario' => $usuario,
        'area' => $area,
        'potabilidad' => $potabilidad,
        'temperatura' => $temperatura,
        'turbidez' => $turbidez,
        'dureza' => $dureza,
        'metales_pesados' => $metales_pesados,
        'fecha' => $fecha
    ];

    $insertId = Database::insert($sql, $params);

    echo json_encode(['status' => 'ok', 'id' => $insertId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
