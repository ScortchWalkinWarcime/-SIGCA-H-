<?php
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$producto = $data['producto'];
$proveedor = $data['proveedor'];
$temperatura = $data['temperatura'];
$estado = $data['estado'];
$empaque = $data['empaque'];
$caducidad = $data['caducidad'];
$calidad = $data['calidad'];
$envases = $data['envases'];
$observaciones = $data['observaciones'];
$usuario = 1;

// Evaluar si la recepción es aceptada
$resultado = "ACEPTADO";
if($estado === "En mal estado" || $empaque === "Incorrecto" || $caducidad === "Incorrecto" || $calidad === "Inadecuado" || $envases === "Incorrecto"){
    $resultado = "RECHAZADO";
}

$sql = "INSERT INTO recepcion_alimentos (producto, proveedor, temperatura, estado, empaque, caducidad, calidad, envases, observaciones, resultado, fecha, cve_usuario)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdssssssi", $producto, $proveedor, $temperatura, $estado, $empaque, $caducidad, $calidad, $envases, $observaciones, $resultado, $usuario);

if($stmt->execute()){
    echo json_encode(["status" => "ok", "resultado" => $resultado]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
