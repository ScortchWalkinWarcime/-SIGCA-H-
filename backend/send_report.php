<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();

function sendJson(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function pdfEscape(string $text): string {
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

function buildPdfDocument(string $title, array $lines): string {
    $maxLinesPerPage = 45;
    $pages = array_chunk($lines, $maxLinesPerPage);
    $objects = [];
    $pageRefs = [];
    $objectIndex = 3;

    foreach ($pages as $pageLines) {
        $streamContent = "BT\n/F1 12 Tf\n50 760 Td\n";
        foreach ($pageLines as $line) {
            $escaped = pdfEscape($line);
            $streamContent .= "({$escaped}) Tj\n0 -14 Td\n";
        }
        $streamContent .= "ET";

        $contentObjNum = $objectIndex++;
        $contentData = "$contentObjNum 0 obj << /Length " . strlen($streamContent) . " >> stream\n{$streamContent}\nendstream endobj";
        $objects[$contentObjNum] = $contentData;

        $pageObjNum = $objectIndex++;
        $pageObj = "$pageObjNum 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents {$contentObjNum} 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj";
        $objects[$pageObjNum] = $pageObj;
        $pageRefs[] = "{$pageObjNum} 0 R";
    }

    $fontObjNum = $objectIndex++;
    $objects[$fontObjNum] = "$fontObjNum 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj";

    $pagesObj = "2 0 obj << /Type /Pages /Kids [" . implode(' ', $pageRefs) . "] /Count " . count($pageRefs) . " >> endobj";
    $catalogObj = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj";

    $objectList = [1 => $catalogObj, 2 => $pagesObj] + $objects;
    $pdf = "%PDF-1.4\n";
    $xref = [];

    foreach ($objectList as $num => $content) {
        $xref[$num] = strlen($pdf);
        $pdf .= $content . "\n";
    }

    $xrefStart = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objectList) + 1) . "\n";
    $pdf .= sprintf("%010d 65535 f \n", 0);
    foreach ($xref as $offset) {
        $pdf .= sprintf("%010d 00000 n \n", $offset);
    }

    $pdf .= "trailer << /Size " . (count($objectList) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n{$xrefStart}\n%%EOF";
    return $pdf;
}

function validateDate(string $value): ?string {
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date ? $date->format('Y-m-d') : null;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['status' => 'error', 'message' => 'Método no permitido'], 405);
}

$recipientEmail = trim($_POST['recipient_email'] ?? '');
$manualEmail = trim($_POST['manual_email'] ?? '');
$module = trim($_POST['module'] ?? '');
$fromDate = trim($_POST['from_date'] ?? '');
$toDate = trim($_POST['to_date'] ?? '');

if (!isset($_SESSION['cve_usuario'])) {
    sendJson(['status' => 'error', 'message' => 'No autorizado'], 401);
}

if ($manualEmail !== '') {
    $recipientEmail = $manualEmail;
}

if (!$recipientEmail || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
    sendJson(['status' => 'error', 'message' => 'Correo destinatario inválido']);
}

$allowedModules = ['agua', 'higiene', 'temperatura', 'recepcion'];
if (!in_array($module, $allowedModules, true)) {
    sendJson(['status' => 'error', 'message' => 'Módulo de reporte inválido']);
}

$from = $fromDate ? validateDate($fromDate) : null;
$to = $toDate ? validateDate($toDate) : null;
if ($fromDate && !$from) {
    sendJson(['status' => 'error', 'message' => 'Fecha inicio inválida']);
}
if ($toDate && !$to) {
    sendJson(['status' => 'error', 'message' => 'Fecha fin inválida']);
}

try {
    $sender = [
        'nombre_usuario' => 'SIGCA-H',
        'email' => 'gutierrezvergarad@gmail.com'
    ];

    switch ($module) {
        case 'agua':
            $label = 'Control de Agua';
            $sql = 'SELECT fecha, area, cloro, ph, potabilidad, temperatura, turbidez, dureza, metales_pesados, observaciones FROM control_agua';
            break;
        case 'higiene':
            $label = 'Control de Higiene';
            $sql = 'SELECT fecha, manos_limpias, uniforme, cofia, sinjoyeria, incumplimiento, guantes, cubrebocas, observaciones FROM control_higiene';
            break;
        case 'temperatura':
            $label = 'Control de Temperatura';
            $sql = 'SELECT ct.fecha, a.nombre AS area, ct.valor FROM control_temperatura ct JOIN areas a ON ct.id_area = a.cve_area';
            break;
        case 'recepcion':
            $label = 'Recepción de Alimentos';
            $sql = 'SELECT fecha, producto, proveedor, estado, resultado, temperatura, observaciones FROM recepcion_alimentos';
            break;
        default:
            sendJson(['status' => 'error', 'message' => 'Módulo desconocido'], 400);
    }

    $conditions = [];
    $params = [];
    if ($from) {
        $conditions[] = 'DATE(fecha) >= :from_date';
        $params['from_date'] = $from;
    }
    if ($to) {
        $conditions[] = 'DATE(fecha) <= :to_date';
        $params['to_date'] = $to;
    }
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY fecha DESC';

    $rows = Database::query($sql, $params);
    if (!$rows) {
        sendJson(['status' => 'error', 'message' => 'No hay registros para el rango seleccionado']);
    }

    $title = "Reporte SIGCA-H - {$label}";
    $textLines = [];
    $textLines[] = $title;
    $textLines[] = 'Fecha de envío: ' . date('Y-m-d H:i:s');
    $textLines[] = 'Destinatario: ' . $recipientEmail;
    $textLines[] = 'Módulo: ' . $label;
    $textLines[] = 'Período: ' . ($from ?? 'sin límite') . ' - ' . ($to ?? 'sin límite');
    $textLines[] = 'Total de registros: ' . count($rows);
    $textLines[] = str_repeat('-', 72);

    foreach ($rows as $idx => $row) {
        $textLines[] = 'Registro #' . ($idx + 1);
        foreach ($row as $field => $value) {
            $textLines[] = ucfirst(str_replace('_', ' ', $field)) . ': ' . ($value === null ? '-' : $value);
        }
        $textLines[] = str_repeat('-', 72);
    }

    $pdfData = buildPdfDocument($title, $textLines);

    $subject = "Reporte SIGCA-H: {$label}";
    $bodyText = "Se adjunta el reporte en PDF del módulo '{$label}' para el rango seleccionado.\n";

    $boundary = '==SIGCA-' . md5(uniqid('', true));
    $headers = [];
    $headers[] = 'From: ' . $sender['nombre_usuario'] . ' <' . $sender['email'] . '>';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $bodyText . "\r\n";
    $message .= "--{$boundary}\r\n";
    $message .= "Content-Type: application/pdf; name=\"reporte.pdf\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"reporte.pdf\"\r\n\r\n";
    $message .= chunk_split(base64_encode($pdfData));
    $message .= "\r\n--{$boundary}--\r\n";

    if (!mail($recipientEmail, $subject, $message, implode("\r\n", $headers))) {
        sendJson(['status' => 'error', 'message' => 'Error al enviar el correo. Verifica la configuración del servidor de correo.'], 500);
    }

    sendJson(['status' => 'ok', 'message' => 'Correo enviado correctamente.']);
} catch (Exception $e) {
    sendJson(['status' => 'error', 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
}
