<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

$producto = $data['producto'] ?? null;
$proveedor = $data['proveedor'] ?? null;
$temperatura = $data['temperatura'] ?? null;
$estado = $data['estado'] ?? null;
$empaque = $data['empaque'] ?? null;
$caducidad = $data['caducidad'] ?? null;
$calidad = $data['calidad'] ?? null;
$envases = $data['envases'] ?? null;
$observaciones = $data['observaciones'] ?? null;
$usuario = 1;

// Validación básica
if (!$producto || $temperatura === null || !$estado) {
    echo json_encode([
        "status" => "error",
        "message" => "Datos incompletos"
    ]);
    exit;
}

//lógica de negocio (bien hecha)
$resultado = "ACEPTADO";

if (
    $estado === "En mal estado" ||
    $empaque === "Incorrecto" ||
    $caducidad === "Incorrecto" ||
    $calidad === "Inadecuado" ||
    $envases === "Incorrecto"
) {
    $resultado = "RECHAZADO";
}

try {

    $sql = "INSERT INTO recepcion_alimentos 
    (producto, proveedor, temperatura, estado, empaque, caducidad, calidad, envases, observaciones, resultado, fecha, cve_usuario)
    VALUES 
    (:producto, :proveedor, :temperatura, :estado, :empaque, :caducidad, :calidad, :envases, :observaciones, :resultado, NOW(), :usuario)";

    $id = Database::insert($sql, [
        ':producto' => $producto,
        ':proveedor' => $proveedor,
        ':temperatura' => $temperatura,
        ':estado' => $estado,
        ':empaque' => $empaque,
        ':caducidad' => $caducidad,
        ':calidad' => $calidad,
        ':envases' => $envases,
        ':observaciones' => $observaciones,
        ':resultado' => $resultado,
        ':usuario' => $usuario
    ]);

    echo json_encode([
        "status" => "ok",
        "resultado" => $resultado,
        "id" => $id
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}