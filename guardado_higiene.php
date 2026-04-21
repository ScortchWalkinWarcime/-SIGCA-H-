<?php
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$empleado = $data['empleado'];
$lavado_manos = $data['lavado_manos'];
$guantes = $data['guantes'];
$cubrebocas = $data['cubrebocas'];
$uniforme = $data['uniforme'];
$cabello = $data['cabello'];
$cofia = $data['cofia'];
$epp = $data['epp'];
$observaciones = $data['observaciones'];
$usuario = 1;

$sql = "INSERT INTO control_higiene (empleado, lavado_manos, guantes, cubrebocas, uniforme, cabello, cofia, epp, observaciones, fecha, cve_usuario)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssi", $empleado, $lavado_manos, $guantes, $cubrebocas, $uniforme, $cabello, $cofia, $epp, $observaciones, $usuario);

if($stmt->execute()){
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
