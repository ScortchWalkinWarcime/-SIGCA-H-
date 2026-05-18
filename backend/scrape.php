<?php
// Simple server-side scraping endpoint
header('Content-Type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$url = isset($_POST['url']) ? trim($_POST['url']) : '';

if(!$url){
    http_response_code(400);
    echo json_encode(['error' => 'URL requerida']);
    exit;
}

// Basic validation
if(!filter_var($url, FILTER_VALIDATE_URL)){
    http_response_code(400);
    echo json_encode(['error' => 'URL inválida']);
    exit;
}

// Only allow same-origin or Distintivo H domains (safety)
$host = parse_url($url, PHP_URL_HOST);
$host_l = $host ? strtolower($host) : '';
$server_host = strtolower(parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST));

// Allow if same host, example.com (dev), or contains 'distintiv' (Distintivo H domains)
if($host_l){
    if($host_l === $server_host || $host_l === 'example.com' || strpos($host_l, 'distintiv') !== false){
        // allowed
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Host no permitido']);
        exit;
    }
}

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: SIGCA-Scraper/1.0\r\n",
        'timeout' => 10
    ]
];

$context = stream_context_create($options);
$html = @file_get_contents($url, false, $context);

if($html === false){
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo obtener contenido']);
    exit;
}

libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($html);

$title = null;
$h1 = null;

$nodes = $doc->getElementsByTagName('title');
if($nodes->length > 0) $title = trim($nodes->item(0)->textContent);

$nodes = $doc->getElementsByTagName('h1');
if($nodes->length > 0) $h1 = trim($nodes->item(0)->textContent);

echo json_encode(['title' => $title, 'h1' => $h1]);
