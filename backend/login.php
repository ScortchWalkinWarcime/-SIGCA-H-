<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/roles_config.php';

$data = json_decode(file_get_contents("php://input"), true);

$correo = $data['correo'] ?? '';
$password = $data['password'] ?? '';

$sql = "SELECT cve_usuario, nombre, contrasena, rol FROM usuario WHERE correo = :correo LIMIT 1";
$res = Database::query($sql, [':correo' => $correo]);

if ($res && isset($res[0]['contrasena']) && $res[0]['contrasena'] === $password) {

    $user_id = $res[0]['cve_usuario'];
    $rol = strtolower(trim($res[0]['rol'] ?? '')) ?: get_user_role($correo, $user_id);

    // Set session
    session_start();
    $_SESSION['n_usuario'] = $res[0]['nombre'];
    $_SESSION['user'] = [
        'cve_usuario' => $user_id,
        'rol' => $rol
    ];

    echo json_encode([
        "status" => "ok",
        "usuario" => $res[0]['nombre'],
        "rol" => $rol,
        "cve_usuario" => $user_id
    ]);

} else {
    echo json_encode([
        "status" => "error"
    ]);
}