<?php
include("conexion.php");

$area = $_GET['area'];

$sql = "SELECT * FROM parametros_temperatura WHERE nombre_area = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $area);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>