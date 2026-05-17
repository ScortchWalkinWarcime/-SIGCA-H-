<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['lavado_manos']) || !isset($input['guantes']) || !isset($input['cubrebocas']) || !isset($input['uniforme']) || !isset($input['cofia']) || !isset($input['epp'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
    exit;
}

try {
    $usuario = $_SESSION['cve_usuario'] ?? null;
    $fecha = date('Y-m-d H:i:s');
    $manos_limpias = strtolower(trim($input['lavado_manos'])) === 'correcto' ? 1 : 0;
    $uniforme = trim($input['uniforme']);
    $cofia = trim($input['cofia']);
    $guantes = trim($input['guantes']);
    $cubrebocas = trim($input['cubrebocas']);
    $observaciones = trim($input['observaciones'] ?? '');

    $issues = [];
    if (strtolower($input['epp']) !== 'correcto') {
        $issues[] = 'EPP incompleto';
    }
    if (strtolower($input['cabello'] ?? '') !== 'correcto') {
        $issues[] = 'Cabello no recogido';
    }
    if (strtolower($input['lavado_manos']) !== 'correcto') {
        $issues[] = 'Lavado de manos incorrecto';
    }
    if (strtolower($input['uniforme']) !== 'correcto') {
        $issues[] = 'Uniforme incorrecto';
    }
    $incumplimiento = implode('; ', $issues);
    $sinjoyeria = strtolower(trim($input['epp'])) === 'correcto' ? 1 : 0;

    Database::insert(
        'INSERT INTO control_higiene (cve_usuario, fecha, manos_limpias, uniforme, cofia, sinjoyeria, incumplimiento, guantes, cubrebocas, observaciones) VALUES (:usuario, :fecha, :manos, :uniforme, :cofia, :sinjoyeria, :incumplimiento, :guantes, :cubrebocas, :observaciones)',
        [
            'usuario' => $usuario,
            'fecha' => $fecha,
            'manos' => $manos_limpias,
            'uniforme' => $uniforme,
            'cofia' => $cofia,
            'sinjoyeria' => $sinjoyeria,
            'incumplimiento' => $incumplimiento,
            'guantes' => $guantes,
            'cubrebocas' => $cubrebocas,
            'observaciones' => $observaciones
        ]
    );

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
