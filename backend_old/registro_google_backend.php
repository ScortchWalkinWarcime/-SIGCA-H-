<?php
session_start();
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/roles_config.php';

if (!function_exists('get_user_role')) {
    function get_user_role($correo, $userId = null) {
        return 'user';
    }
}

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data['nombre'] ?? '');
$correo = trim($data['correo'] ?? '');
$password = $data['password'] ?? '';
$google_id = $data['google_id'] ?? '';

// Validate input
if (empty($nombre) || empty($correo) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Todos los campos son obligatorios"
    ]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Correo electrónico inválido"
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        "status" => "error",
        "message" => "La contraseña debe tener al menos 6 caracteres"
    ]);
    exit;
}

try {
    // Check if user already exists
    $sql = "SELECT cve_usuario FROM usuario WHERE correo = :correo LIMIT 1";
    $existing = Database::query($sql, [':correo' => $correo]);

    if ($existing) {
        echo json_encode([
            "status" => "error",
            "message" => "Ya existe un usuario con este correo"
        ]);
        exit;
    }

    // Determine role and insert new user
    $rol = get_user_role($correo, null);
    $sql = "INSERT INTO usuario (correo, nombre, contrasena, rol) VALUES (:correo, :nombre, :password, :rol)";
    $userId = Database::insert($sql, [
        ':correo' => $correo,
        ':nombre' => $nombre,
        ':password' => $password,
        ':rol' => $rol
    ]);

    // Set user role from DB or fallback
    $rol = strtolower(trim($rol)) ?: get_user_role($correo, $userId);

    // Set session for login
    $_SESSION['n_usuario'] = $nombre;
    $_SESSION['user'] = [
        'google_id' => $google_id,
        'email' => $correo,
        'name' => $nombre,
        'cve_usuario' => $userId,
        'rol' => $rol
    ];

    // Clear Google user data
    unset($_SESSION['google_user_data']);

    echo json_encode([
        "status" => "ok",
        "message" => "Usuario registrado exitosamente",
        "usuario" => $nombre,
        "rol" => $rol,
        "cve_usuario" => $userId
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>