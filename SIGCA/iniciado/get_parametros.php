<?php
require_once __DIR__ . '/database/database.php';

$area = $_GET['area'] ?? '';

$sql = "SELECT * FROM parametros_temperatura WHERE nombre_area = :area";

$res = Database::query($sql, [
    ':area' => $area
]);

echo json_encode($res[0] ?? null);