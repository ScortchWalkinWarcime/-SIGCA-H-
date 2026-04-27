<?php
require_once __DIR__ . '/../database/database.php';

$sql = "
SELECT ct.cve_temp, ct.valor, ct.fecha, a.nombre AS area
FROM control_temperatura ct
JOIN areas a ON ct.id_area = a.cve_area
ORDER BY ct.fecha DESC
";

try {
    $data = Database::query($sql);

    echo json_encode([
        "status" => "ok",
        "data" => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}