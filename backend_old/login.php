<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$correo = $data['correo'] ?? '';
$password = $data['password'] ?? '';

$sql = "SELECT cve_usuario, nombre, contrasena, rol FROM usuarios WHERE correo = :correo LIMIT 1";
$res = Database::query($sql, [':correo' => $correo]);

if ($res && isset($res[0]['contrasena']) && $res[0]['contrasena'] === $password) {

    $user_id = $res[0]['cve_usuario'];

    // Set session
    session_start();
    $_SESSION['n_usuario'] = $res[0]['nombre'];
    $_SESSION['user'] = [
        'cve_usuario' => $user_id,
        'rol' => strtolower($res[0]['rol'])
    ];

    echo json_encode([
        "status" => "ok",
        "usuario" => $res[0]['nombre'],
        "rol" => strtolower($res[0]['rol']),
        "cve_usuario" => $res[0]['cve_usuario']
    ]);

} else {
    echo json_encode([
        "status" => "error"
    ]);
}