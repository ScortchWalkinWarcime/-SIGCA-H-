<?php
session_start();

// Check if we have Google user data
if (!isset($_SESSION['google_user_data'])) {
    header("Location: Login_W3.html");
    exit;
}

$googleData = $_SESSION['google_user_data'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro con Google - SIGCA</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg,#0d6efd,#0dcaf0);
            height:100vh;
            margin:0;
        }
        .register-container {
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .register-card {
            width:400px;
            border-radius:15px;
            box-shadow:0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="w3-card w3-white w3-padding-large register-card">
        <h3 class="w3-center">Completar Registro</h3>
        <p class="w3-center w3-text-grey">Datos obtenidos de Google</p>

        <div class="w3-center w3-margin-bottom">
            <img src="<?php echo htmlspecialchars($googleData['picture']); ?>" alt="Foto de perfil"
                 style="width:80px; height:80px; border-radius:50%; border:3px solid #ddd;">
        </div>

        <form id="registerForm">
            <label>Nombre Completo</label>
            <input id="nombre" class="w3-input w3-border w3-margin-bottom"
                   type="text" value="<?php echo htmlspecialchars($googleData['name']); ?>" required>

            <label>Correo Electrónico</label>
            <input id="correo" class="w3-input w3-border w3-margin-bottom"
                   type="email" value="<?php echo htmlspecialchars($googleData['email']); ?>" readonly>

            <label>Contraseña (requerida para el sistema)</label>
            <input id="password" class="w3-input w3-border w3-margin-bottom"
                   type="password" placeholder="Crea una contraseña" required>

            <label>Confirmar Contraseña</label>
            <input id="confirm_password" class="w3-input w3-border w3-margin-bottom"
                   type="password" placeholder="Confirma tu contraseña" required>

            <button type="submit" class="w3-button w3-blue w3-block w3-margin-top">
                Completar Registro
            </button>
        </form>

        <p id="mensaje" class="w3-center w3-margin-top"></p>

        <div class="w3-center w3-margin-top">
            <a href="Login_W3.html" class="w3-button w3-border w3-small">Volver al Login</a>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#registerForm").submit(function(e){
        e.preventDefault();

        let nombre = $("#nombre").val();
        let correo = $("#correo").val();
        let password = $("#password").val();
        let confirmPassword = $("#confirm_password").val();

        if(password !== confirmPassword){
            $("#mensaje").html("<span class='w3-text-red'>Las contraseñas no coinciden</span>");
            return;
        }

        if(password.length < 6){
            $("#mensaje").html("<span class='w3-text-red'>La contraseña debe tener al menos 6 caracteres</span>");
            return;
        }

        $("#mensaje").html("Registrando...");

        fetch("registro_google_backend.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                nombre: nombre,
                correo: correo,
                password: password,
                google_id: "<?php echo $googleData['google_id']; ?>"
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === "ok"){
                $("#mensaje").html("<span class='w3-text-green'>Registro completado. Redirigiendo...</span>");
                setTimeout(() => {
                    window.location.href = "login_success.php";
                }, 1000);
            } else {
                $("#mensaje").html("<span class='w3-text-red'>" + (data.message || "Error en el registro") + "</span>");
            }
        })
        .catch(error => {
            $("#mensaje").html("<span class='w3-text-red'>Error del sistema</span>");
            console.error(error);
        });
    });
});
</script>

</body>
</html>