<?php
require_once __DIR__ . '/../database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$area = $data['area'] ?? null;
$temp = $data['temp'] ?? null;
$usuario = 1;

if (!$area || !$temp) {
    echo json_encode([
        "status" => "error",
        "message" => "Área y temperatura son requeridos"
    ]);
    exit;
}

try{

$sqlArea = "SELECT cve_area FROM area WHERE nombre = :area LIMIT1";
$res = Database::query($sqlArea, [':area' => $area]);

if (!$res) {
    echo json_encode([
        "status" => "error",
        "message" => "Área no encontrada"
    ]);
    exit;
}

$idArea = $res[0]['cve_area'];
$sql = "INSERT INTO control_temperatura (id_area, valor, fecha, cve_usuario) VALUES (:id_area, :valor, NOW(), :cve_usuario)";

$id =Database::insert($sql, [
    ':id_area' => $idArea,
    ':valor' => $temp,
    ':cve_usuario' => $usuario
]);
    echo json_encode([
        "status" => "ok",
        "id" => $id
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al guardar: " . $e->getMessage()
    ]);
    exit;
}

echo json_encode([
    "status" => "ok",
    "id" => $id
]);