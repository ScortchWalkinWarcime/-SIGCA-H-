<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);
$required = ['producto', 'proveedor', 'temperatura', 'fecha', 'estado', 'empaque', 'caducidad', 'calidad', 'envases'];
foreach ($required as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
        exit;
    }
}

try {
    $usuario = $_SESSION['cve_usuario'] ?? null;
    $producto = trim($input['producto']);
    $proveedor = trim($input['proveedor']);
    $temperatura = trim($input['temperatura']);
    $fecha = date('Y-m-d', strtotime($input['fecha']));
    $estado = trim($input['estado']);
    $observaciones = trim($input['observaciones'] ?? '');

    $aceptado = strtolower($estado) === 'fresco' && strtolower($input['empaque']) === 'correcto' && strtolower($input['caducidad']) === 'correcto' && strtolower($input['calidad']) === 'adecuado' && strtolower($input['envases']) === 'correcto';
    $resultado = $aceptado ? 'ACEPTADO' : 'RECHAZADO';

    Database::insert(
        'INSERT INTO recepcion_alimentos (producto, proveedor, temperatura, fecha, estado, observaciones, resultado, cve_usuario) VALUES (:producto, :proveedor, :temperatura, :fecha, :estado, :observaciones, :resultado, :usuario)',
        [
            'producto' => $producto,
            'proveedor' => $proveedor,
            'temperatura' => $temperatura,
            'fecha' => $fecha,
            'estado' => $estado,
            'observaciones' => $observaciones,
            'resultado' => $resultado,
            'usuario' => $usuario
        ]
    );

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
