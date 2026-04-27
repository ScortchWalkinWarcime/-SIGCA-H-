<?php
session_start();
if (!isset($_SESSION['n_usuario'])) {
    header("Location: Login_W3.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIGCA-H - Usuario</title>

<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body { background:#f4f6f9; margin:0; }
.sidebar { height:100vh; background:#212529; color:#fff; position:fixed; width:220px; }
.sidebar a { color:#fff; padding:12px; display:block; text-decoration:none; }
.sidebar a:hover { background:#343a40; }
.main { margin-left:220px; }
.header { background:#fff; padding:15px; border-bottom:1px solid #ddd; }
.card-indicator { border-left:5px solid #2196F3; }
.content { padding:20px; }
</style>

</head>
<body>

<div class="sidebar w3-bar-block">

    <h4 class="w3-center w3-padding">SIGCA-H</h4>
    <hr>

    <a href="#" class="menu" data-modulo="">Dashboard</a>

    <div class="w3-small w3-padding w3-text-grey">OPERACIÓN SANITARIA</div>

    <a href="#" class="menu" data-modulo="Control_Temp.html">Control de Temperatura</a>
    <a href="#" class="menu" data-modulo="Control_agua.html">Control de Agua</a>
    <a href="#" class="menu" data-modulo="Control_higiene.html">Control de Higiene</a>
    <a href="#" class="menu" data-modulo="recepcion_alimentos.html">Recepción de Alimentos</a>
    <a href="#" class="menu" data-modulo="almacenamiento.html">Almacenamiento</a>

</div>

<div class="main">

<div class="header w3-display-container">
    <h5>Sistema de Gestión - Usuario</h5>
    <div class="w3-display-right">
        <span id="user-info"></span>
        <button id="logout" class="w3-button w3-border w3-border-red w3-text-red w3-small">
            Cerrar sesión
        </button>
    </div>
</div>

<div class="content" id="contenido">

<div class="w3-row-padding w3-margin-bottom">

<div class="w3-third">
<div class="w3-card w3-white w3-padding card-indicator">
<h6>Temperatura promedio</h6>
<h3 id="tempProm">--</h3>
</div>
</div>

<div class="w3-third">
<div class="w3-card w3-white w3-padding card-indicator">
<h6>Registros hoy</h6>
<h3 id="registros">--</h3>
</div>
</div>

</div>

<div class="w3-card w3-white">
<div class="w3-container w3-blue">
<h6>Control de Temperatura</h6>
</div>

<div class="w3-container">
<table class="w3-table w3-bordered" id="tablaTemp">
<tr>
<th>Área</th>
<th>Temp</th>
<th>Fecha</th>
<th>Estado</th>
</tr>
</table>
</div>
</div>

</div>
</div>

<script>

$(document).ready(function(){

const rol = "user"; // Fixed for user dashboard
const nombre = localStorage.getItem("usuario") || "Usuario";

// mostrar usuario
$("#user-info").text(`Usuario: ${nombre} (${rol})`);

// No blocking for user, they can edit
function bloquearEdicion(){
    // User can edit
}

// cargar módulos
async function cargarModulo(mod){
    if (!mod) return; // for dashboard
    $("#contenido").html("Cargando...");
    try {
        let res = await fetch("/SIGCA/" + mod);
        if (!res.ok) throw new Error("Módulo no encontrado: " + res.status + " " + res.statusText);
        let html = await res.text();
        // Parse the HTML and extract only the .content part
        let parser = new DOMParser();
        let doc = parser.parseFromString(html, 'text/html');
        let content = doc.querySelector('.content');
        if (content) {
            $("#contenido").html(content.innerHTML);
        } else {
            $("#contenido").html(html); // fallback
        }

        // Execute module scripts after content is inserted
        doc.querySelectorAll('script').forEach(oldScript => {
            if (oldScript.src && oldScript.src.includes('jquery')) return;
            let newScript = document.createElement('script');
            if (oldScript.src) {
                newScript.src = oldScript.src;
                newScript.async = false;
            } else {
                newScript.textContent = oldScript.textContent;
            }
            document.body.appendChild(newScript);
        });

        bloquearEdicion();
    } catch (e) {
        $("#contenido").html("<p>Error: " + e.message + "</p>");
    }
}

$(".menu").click(function(e){
    e.preventDefault();
    cargarModulo($(this).data("modulo"));
});

// cargar datos
async function cargarDatos(){
       try {
        let res = await fetch('backend/obtener_temp.php');
        let data = await response.json();
        
        $("#tablaTemp tr:gt(0)").remove();

        for (let d of datos){
            await agregarFila(d.area, d.temp, d.fecha);
        }
    }catch(error) {
        console.error("Error al cargar datos:", error);
        $("#mensaje").html("<span class='w3-text-red'>Error al cargar datos</span>");
    }
}

// refresco
setInterval(cargarDatos,30000);
cargarDatos();

// logout
$("#logout").click(()=>window.location.href="logout.php");

});

</script>

</body>
</html>