<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../database/database.php';

$data = json_decode(file_get_contents("php://input"), true);

// Mapear datos del frontend a tu DB
$manos_limpias = $data['lavado_manos'] ?? null;
$uniforme = $data['uniforme'] ?? null;
$cofia = $data['cofia'] ?? null;
$sinjoyeria = $data['sinjoyeria'] ?? null;

// Lógica simple de incumplimiento
$incumplimiento = 0;
if (
    $manos_limpias !== "Correcto" ||
    $uniforme !== "Correcto" ||
    $cofia !== "Correcto" ||
    $sinjoyeria !== "Correcto"
) {
    $incumplimiento = 1;
}

$usuario = 1;

// Validación
if (!$manos_limpias || !$uniforme || !$cofia) {
    echo json_encode([
        "status" => "error",
        "message" => "Faltan datos obligatorios"
    ]);
    exit;
}

try {

    $sql = "INSERT INTO control_higiene 
    (manos_limpias, uniforme, cofia, sinjoyeria, incumplimiento, fecha, cve_usuario)
    VALUES 
    (:manos, :uniforme, :cofia, :sinjoyeria, :incumplimiento, NOW(), :usuario)";

    $id = Database::insert($sql, [
        ':manos' => $manos_limpias,
        ':uniforme' => $uniforme,
        ':cofia' => $cofia,
        ':sinjoyeria' => $sinjoyeria,
        ':incumplimiento' => $incumplimiento,
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
?>