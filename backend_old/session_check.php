<?php
session_start();
if (!isset($_SESSION['n_usuario'])) {
    echo json_encode(['logged' => false]);
} else {
    echo json_encode(['logged' => true]);
}
?>