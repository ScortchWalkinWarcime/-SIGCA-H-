<?php
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$area = $data['area'];
$cloro_ppm = $data['cloro_ppm'];
$ph = $data['ph'];
$potabilidad = $data['potabilidad'];
$temperatura = $data['temperatura'];
$turbidez = $data['turbidez'];
$dureza = $data['dureza'];
$metales_pesados = $data['metales_pesados'];
$observaciones = $data['observaciones'];
$usuario = 1;

$sql = "INSERT INTO control_agua (area, cloro_ppm, ph, potabilidad, temperatura, turbidez, dureza, metales_pesados, observaciones, fecha, cve_usuario)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sddsddissi", $area, $cloro_ppm, $ph, $potabilidad, $temperatura, $turbidez, $dureza, $metales_pesados, $observaciones, $usuario);

if($stmt->execute()){
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
