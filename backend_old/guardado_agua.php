<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

// Obtener datos
$area = $data['area'] ?? null;
$cloro = $data['cloro_ppm'] ?? null; // 👈 viene del frontend
$ph = $data['ph'] ?? null;
$potabilidad = $data['potabilidad'] ?? null;
$temperatura = $data['temperatura'] ?? null;
$turbidez = $data['turbidez'] ?? null;
$dureza = $data['dureza'] ?? null;
$metales_pesados = $data['metales_pesados'] ?? null;
$observaciones = $data['observaciones'] ?? null;

$usuario = 1;

// Validación básica
if (!$area || $ph === null || $cloro === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Faltan datos obligatorios"
    ]);
    exit;
}

try {

    $sql = "INSERT INTO control_agua 
    (area, cloro, ph, potabilidad, temperatura, turbidez, dureza, metales_pesados, observaciones, fecha, cve_usuario)
    VALUES 
    (:area, :cloro, :ph, :potabilidad, :temperatura, :turbidez, :dureza, :metales_pesados, :observaciones, NOW(), :usuario)";

    $id = Database::insert($sql, [
        ':area' => $area,
        ':cloro' => $cloro,
        ':ph' => $ph,
        ':potabilidad' => $potabilidad,
        ':temperatura' => $temperatura,
        ':turbidez' => $turbidez,
        ':dureza' => $dureza,
        ':metales_pesados' => $metales_pesados,
        ':observaciones' => $observaciones,
        ':usuario' => $usuario
    ]);

    echo json_encode([
        "status" => "ok",
        "id" => $id
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}