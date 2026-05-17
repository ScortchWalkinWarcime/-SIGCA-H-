<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/database.php';

try {
    $sql = "SELECT cve_usuario, nombre, correo AS email, rol, cve_restaurante FROM usuarios ORDER BY cve_usuario DESC";
    $res = Database::query($sql);

    // Return raw array for frontend that expects a plain list
    echo json_encode($res);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}


?>
