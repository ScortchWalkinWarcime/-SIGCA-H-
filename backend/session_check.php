<?php
header('Content-Type: application/json; charset=UTF-8');
session_start();

if (isset($_SESSION['n_usuario'])) {
    echo json_encode(['logged' => true, 'usuario' => $_SESSION['n_usuario'], 'rol' => $_SESSION['rol'] ?? null]);
} else {
    echo json_encode(['logged' => false]);
}
