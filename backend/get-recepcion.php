<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../database/database.php';

try {

    $sql = "SELECT * FROM recepcion_alimentos ORDER BY fecha DESC";

    $res = Database::query($sql);

    echo json_encode([
        "status" => "ok",
        "data" => $res
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>