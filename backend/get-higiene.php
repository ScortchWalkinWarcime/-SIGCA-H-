<?php
include("conexion.php");

$sql = "SELECT * FROM control_higiene ORDER BY fecha DESC";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);

$conn->close();
?>
