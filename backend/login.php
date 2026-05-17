<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['correo']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
    exit;
}

$correo = trim($input['correo']);
$password = trim($input['password']);

try {
    $user = Database::query('SELECT cve_usuario, nombre, correo, contrasena, rol FROM usuario WHERE correo = :correo LIMIT 1', ['correo' => $correo]);
    if (!$user) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    $user = $user[0];
    if ($user['contrasena'] !== $password) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    $_SESSION['cve_usuario'] = $user['cve_usuario'];
    $_SESSION['email'] = $user['correo'];
    $_SESSION['n_usuario'] = $user['nombre'];
    $_SESSION['rol'] = $user['rol'];

    echo json_encode([
        'status' => 'ok',
        'usuario' => $user['nombre'],
        'cve_usuario' => $user['cve_usuario'],
        'rol' => strtolower($user['rol'])
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
