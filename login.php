<?php
require_once __DIR__ . '/database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$correo = $data['correo'] ?? '';
$password = $data['password'] ?? '';

$sql = "SELECT * FROM usuario WHERE correo = :correo LIMIT 1";
$res = Database::query($sql, [':correo' => $correo]);

if ($res && password_verify($password, $res[0]['contrasena'])) {
    
    echo json_encode([
        "status" => "ok",
        "usuario" => $res[0]['nombre']
    ]);

} else {
    echo json_encode([
        "status" => "error"
    ]);
}