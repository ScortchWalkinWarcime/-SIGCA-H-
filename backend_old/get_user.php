<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/database.php';

session_start();

$id = $_GET['id'] ?? null;
$correo = $_GET['correo'] ?? null;

try {
    if (isset($_SESSION['user']) && !($id || $correo)) {
        // Return current session user
        $id = $_SESSION['user']['cve_usuario'] ?? null;
    }

    if ($id) {
        $sql = "SELECT cve_usuario, nombre, correo AS email, rol, cve_restaurante FROM usuarios WHERE cve_usuario = :id LIMIT 1";
        $res = Database::query($sql, [':id' => $id]);
        $user = $res[0] ?? null;
    } elseif ($correo) {
        $sql = "SELECT cve_usuario, nombre, correo AS email, rol, cve_restaurante FROM usuarios WHERE correo = :correo LIMIT 1";
        $res = Database::query($sql, [':correo' => $correo]);
        $user = $res[0] ?? null;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing id or correo']);
        exit;
    }

    if ($user) {
        // Return plain user object for frontend
        echo json_encode($user);
    } else {
        echo json_encode(null);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
