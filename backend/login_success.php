<?php
session_start();
if (!isset($_SESSION['n_usuario'])) {
    header("Location: index.html");
    exit;
}

$userName = $_SESSION['n_usuario'];
$userId = $_SESSION['cve_usuario'] ?? ($_SESSION['user']['cve_usuario'] ?? '');
$userRole = strtolower($_SESSION['rol'] ?? ($_SESSION['user']['rol'] ?? 'user'));

// Determine dashboard based on role
$dashboard = 'SIGCA-H_User.php';
if ($userRole === 'admin') {
    $dashboard = 'SIGCA-H_W3.php';
} elseif ($userRole === 'gerente') {
    $dashboard = 'SIGCA-H_Gerente.php';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Success</title>
</head>
<body>
    <script>
        localStorage.setItem("usuario", "<?php echo addslashes($userName); ?>");
        localStorage.setItem("cve_usuario", "<?php echo $userId; ?>");
        localStorage.setItem("rol", "<?php echo $userRole; ?>");
        window.location.href = "<?php echo $dashboard; ?>";
    </script>
</body>
</html>
