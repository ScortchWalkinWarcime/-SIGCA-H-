<?php
require_once __DIR__ . '/../database/database.php';

$sql = "SELECT * FROM alertas ORDER BY fecha DESC";

try {
    $data = Database::query($sql);

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>