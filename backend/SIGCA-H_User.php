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
<input id="fecha" class="w3-input w3-border" type="datetime-local">
<th>Estado</th>
</tr>
</table>
</div>
</div>

</div>
</div>

<script>

$(document).ready(function(){

const rol = "user";
const nombre = localStorage.getItem("usuario") || "Usuario";

$("#user-info").text(`Usuario: ${nombre} (${rol})`);

async function cargarModulo(mod){
    if (!mod) return;

    $("#contenido").html("Cargando...");

    try {
        let res = await fetch("/SIGCA/" + mod);
        let html = await res.text();

        let parser = new DOMParser();
        let doc = parser.parseFromString(html, 'text/html');
        let content = doc.querySelector('.content');

        $("#contenido").html(content ? content.innerHTML : html);

    } catch {
        $("#contenido").html("<p>Error al cargar módulo</p>");
    }
}

$(".menu").click(function(e){
    e.preventDefault();
    cargarModulo($(this).data("modulo"));
});

function agregarFila(area, temp, fecha){
    let estado = (temp > 5 || temp < -18) ? "w3-red" : "w3-green";

    $("#tablaTemp").append(`
    <tr>
        <td>${area}</td>
        <td>${temp}</td>
        <td>${fecha}</td>
        <td><span class="w3-tag ${estado}">
            ${estado === "w3-red" ? "Alerta" : "Correcto"}
        </span></td>
    </tr>
    `);
}

async function cargarDatos(){
    try {
        let res = await fetch('get-temps.php');
        let json = await res.json();

        if(json.status !== "ok") return;

        let datos = json.data;

        $("#tablaTemp tr:gt(0)").remove();

        datos.slice(0,5).forEach(d=>{
            agregarFila(d.area, d.valor, d.fecha);
        });

        let prom = datos.length
            ? datos.reduce((a,b)=>a+parseFloat(b.valor),0)/datos.length
            : 0;

        $("#tempProm").text(prom.toFixed(1) + " °C");

        let hoy = new Date().toISOString().split('T')[0];
        let hoyCount = datos.filter(x=>x.fecha.startsWith(hoy)).length;

        $("#registros").text(hoyCount);

    } catch(error) {
        console.error("Error al cargar datos:", error);
    }
}

setInterval(cargarDatos,30000);
cargarDatos();

$("#logout").click(()=>window.location.href="logout.php");

});

</script>

</body>
</html>