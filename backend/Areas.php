<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../database/database.php';

if (!isset($_GET['area'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Parámetro 'area' requerido"
    ]);
    exit;
}

$area = $_GET['area'];

try {

    $sql = "SELECT * FROM parametros_temperatura WHERE nombre_area = :area LIMIT 1";

    $res = Database::query($sql, [
        ':area' => $area
    ]);

    if (!$res || count($res) === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Área no encontrada"
        ]);
        exit;
    }

    echo json_encode([
        "status" => "ok",
        "data" => $res[0]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}