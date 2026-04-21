<?php
require_once __DIR__ . '/database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$area = $data['area'];
$temp = $data['temp'];
$usuario = 1;

// 1. Obtener ID del área
$sqlArea = "SELECT cve_area FROM areas WHERE nombre = :area";
$res = Database::query($sqlArea, [':area' => $area]);

if (!$res) {
    echo json_encode(["status" => "error", "msg" => "Área no existe"]);
    exit;
}

$id_area = $res[0]['cve_area'];

// 2. Insertar temperatura
$sql = "INSERT INTO control_temperatura (id_area, valor, fecha, cve_usuario)
VALUES (:area, :temp, NOW(), :usuario)";

$id = Database::insert($sql, [
    ':area' => $id_area,
    ':temp' => $temp,
    ':usuario' => $usuario
]);

echo json_encode([
    "status" => "ok",
    "id" => $id
]);