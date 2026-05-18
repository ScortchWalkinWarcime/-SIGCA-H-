<?php
session_start();
if (!isset($_SESSION['n_usuario'])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIGCA-H</title>

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

    <a href="#" class="menu" data-modulo="dashboard.php">Dashboard</a>

    <div class="w3-small w3-padding w3-text-grey">OPERACIÓN SANITARIA</div>
   <a href="#" class="menu" data-modulo="../Control_Temp.html">Control de Temperatura</a>
    <a href="#" class="menu" data-modulo="../Control_agua.html">Control de Agua</a>
    <a href="#" class="menu" data-modulo="../Control_higiene.html">Control de Higiene</a>
    <a href="#" class="menu" data-modulo="../recepcion_alimentos.html">Recepción de Alimentos</a>
    <a href="#" class="menu" data-modulo="../almacenamiento.html">Almacenamiento</a>

    <div class="w3-small w3-padding w3-text-grey gerente-only">MONITOREO</div>
    <a href="#" class="menu gerente-only" data-modulo="../alertas.html">Alertas</a>

    <div class="w3-small w3-padding w3-text-grey gerente-only">AUDITORÍA</div>
    <a href="#" class="menu gerente-only" data-modulo="../reporte.html">Reporte</a>
    <a href="#" class="menu gerente-only" data-modulo="../historial.html">Historial</a>

    <div class="w3-small w3-padding w3-text-grey admin-only">ADMINISTRACIÓN</div>
    <a href="#" class="menu admin-only" data-modulo="../inventario.html">Inventario</a>
    <a href="#" class="menu admin-only" data-modulo="../usuarios.html">Usuarios</a>
    <a href="#" class="menu admin-only" data-modulo="../restaurantes.html">Restaurantes</a>

</div>

<div class="main">

<div class="header w3-display-container">
    <h5>Sistema de Gestión</h5>
    <div class="w3-display-right">
        <span id="user-info"></span>
        <button id="scrape-btn" class="w3-button w3-border w3-border-green w3-text-green w3-small" title="Scrapear Distintivo H">
            Scrape Distintivo H
        </button>

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

<div class="w3-third gerente-only">
<div class="w3-card w3-white w3-padding card-indicator">
<h6>Alertas</h6>
<h3 id="alertas">--</h3>
</div>
</div>

<div class="w3-third gerente-only">
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

const rol = localStorage.getItem("rol") || "user";
const nombre = localStorage.getItem("usuario") || "Usuario";

$("#user-info").text(`Usuario: ${nombre} (${rol})`);

if(rol === "user"){
    $(".admin-only").hide();
    $(".gerente-only").hide();
}

if(rol === "gerente"){
    $(".admin-only").hide();
}

function bloquearEdicion(){
    if(rol !== "admin"){
        $("input, select, textarea, button.guardar").prop("disabled", true);
    }
}

async function cargarModulo(mod){
    $("#contenido").html("Cargando...");

    try{
        let url = mod;
        if (!mod.startsWith('/') && !mod.startsWith('http')) {
            if (mod.startsWith('../')) {
                url = mod.replace(/^(\.\.\/)+/, '/SIGCA/');
            } else {
                url = '/SIGCA/' + mod;
            }
        }

        const res = await fetch(url);
        const html = await res.text();
        $("#contenido").html(html);
        bloquearEdicion();
    }catch(e){
        $("#contenido").html("<p class='w3-text-red'>Error al cargar módulo</p>");
        console.error(e);
    }
}

$(".menu").click(function(e){
    e.preventDefault();
    cargarModulo($(this).data("modulo"));
});

function cargarDatos(){

fetch('get-temps.php')
.then(r=>r.json())
.then(res=>{
    if(res.status==="ok"){
        let d = res.data;

        $("#tablaTemp tr:not(:first)").remove();

        d.slice(0,5).forEach(x=>{
            let estado = x.valor>5||x.valor<-18 ? "w3-red" : "w3-green";
            $("#tablaTemp").append(`
            <tr>
            <td>${x.area}</td>
            <td>${x.valor}</td>
            <td>${x.fecha}</td>
            <td><span class="w3-tag ${estado}">${estado==="w3-red"?"Alerta":"Correcto"}</span></td>
            </tr>`);
        });

        let prom = d.length ? d.reduce((a,b)=>a+parseFloat(b.valor),0)/d.length : 0;
        $("#tempProm").text(prom.toFixed(1)+" °C");

        let hoy = new Date().toISOString().split('T')[0];
        let hoyCount = d.filter(x=>x.fecha.startsWith(hoy)).length;
        $("#registros").text(hoyCount);
    }
});

fetch('alertas.php')
.then(r=>r.json())
.then(d=>{
    if (!Array.isArray(d)) {
        console.error('alertas.php returned invalid payload', d);
        d = [];
    }
    let c = d.filter(x=>x.tipo_severidad==="Crítico").length;
    $("#alertas").text(c);
})
.catch(error => {
    console.error('Error loading alertas:', error);
    $("#alertas").text('0');
});

}

setInterval(cargarDatos,30000);
cargarDatos();

$("#logout").click(()=>window.location.href="logout.php");

// Distintivo H scraping
$("#scrape-btn").click(async ()=>{
    const url = prompt('Ingrese la URL de Distintivo H a scrapear', 'https://www.distintivoh.gob.mx');
    if(!url) return;

    $("#contenido").html("<p>Scrapeando...</p>");

    try{
        const res = await fetch('/SIGCA/backend/scrape.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ url })
        });

        const data = await res.json();

        if(data.error){
            $("#contenido").html(`<p class='w3-text-red'>${data.error}</p>`);
            return;
        }

        $("#contenido").html(
            `<h3>Distintivo H - Resultado</h3>
            <p><strong>Título:</strong> ${data.title || '-'} </p>
            <p><strong>Primer H1:</strong> ${data.h1 || '-'} </p>`
        );

    }catch(e){
        $("#contenido").html("<p class='w3-text-red'>Error al scrapear</p>");
        console.error(e);
    }
});

});

</script>

</body>
</html>