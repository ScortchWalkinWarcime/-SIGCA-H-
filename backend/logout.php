<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    <script>
        localStorage.removeItem("usuario");
        localStorage.removeItem("cve_usuario");
        localStorage.removeItem("rol");
        window.location.href = "../Login_W3.html";
    </script>
</body>
</html>