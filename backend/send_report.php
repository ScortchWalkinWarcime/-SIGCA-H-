<?php
session_start();

require_once __DIR__ . '/../database/database.php';

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

        $objects[$contentObjNum] =
            "$contentObjNum 0 obj << /Length " .
            strlen($streamContent) .
            " >> stream\n{$streamContent}\nendstream endobj";

        $pageObjNum = $objectIndex++;

        $objects[$pageObjNum] =
            "$pageObjNum 0 obj << /Type /Page /Parent 2 0 R " .
            "/MediaBox [0 0 612 792] " .
            "/Contents {$contentObjNum} 0 R " .
            "/Resources << /Font << /F1 5 0 R >> >> >> endobj";

        $pageRefs[] = "{$pageObjNum} 0 R";
    }

    $fontObjNum = $objectIndex++;

    $objects[$fontObjNum] =
        "$fontObjNum 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj";

    $pagesObj =
        "2 0 obj << /Type /Pages /Kids [" .
        implode(' ', $pageRefs) .
        "] /Count " .
        count($pageRefs) .
        " >> endobj";

    $catalogObj =
        "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj";

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
    die("Método no permitido");
}

if (!isset($_SESSION['n_usuario'])) {
    die("No autorizado");
}

$module = trim($_POST['module'] ?? '');
$fromDate = trim($_POST['from_date'] ?? '');
$toDate = trim($_POST['to_date'] ?? '');

$allowedModules = [
    'agua',
    'higiene',
    'temperatura',
    'recepcion'
];

if (!in_array($module, $allowedModules, true)) {
    die("Módulo inválido");
}

$from = $fromDate ? validateDate($fromDate) : null;
$to = $toDate ? validateDate($toDate) : null;
// Ajustar los valores debidos a cada modulo y tambien agregar las diferencias de grados a cada uno.
try {

    // correcion de el error de labe
    $label = '';

    switch ($module) {

        case 'agua':

            $label = 'Control de Agua';

            $sql = "
                SELECT
                    fecha,
                    area,
                    cloro,
                    ph,
                    potabilidad,
                    temperatura,
                    turbidez,
                    dureza,
                    metales_pesados,
                    observaciones
                FROM control_agua
            ";

            break;

        case 'higiene':

            $label = 'Control de Higiene';

            $sql = "
                SELECT
                    fecha,
                    manos_limpias,
                    uniforme,
                    cofia,
                    sinjoyeria,
                    incumplimiento,
                    guantes,
                    cubrebocas,
                    observaciones
                FROM control_higiene
            ";

            break;

        case 'temperatura':

            $label = 'Control de Temperatura';

            $sql = "
                SELECT
                    ct.fecha,
                    a.nombre AS area,
                    ct.valor AS temperatura
                FROM control_temperatura ct
                JOIN areas a
                ON ct.id_area = a.cve_area
            ";
//cambiar valores en frontend de este case
            break;

        case 'recepcion':

            $label = 'Recepción de Alimentos';

            $sql = "
                SELECT
                    fecha,
                    producto,
                    proveedor,
                    estado,
                    resultado,
                    temperatura,
                    observaciones
                FROM recepcion_alimentos
            ";

            break;
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
        die("No hay registros para generar el reporte.");
    }

    $title = "Reporte SIGCA-H - {$label}";

    $textLines = [];

    $textLines[] = $title;
    $textLines[] = 'Fecha de generación: ' . date('Y-m-d H:i:s');
    $textLines[] = 'Módulo: ' . $label;
    $textLines[] = 'Período: ' . ($from ?? 'Sin límite') . ' - ' . ($to ?? 'Sin límite');
    $textLines[] = 'Total de registros: ' . count($rows);
    $textLines[] = str_repeat('-', 72);

    foreach ($rows as $idx => $row) {

        $textLines[] = 'Registro #' . ($idx + 1);

        foreach ($row as $field => $value) {

            $textLines[] =
                ucfirst(str_replace('_', ' ', $field))
                . ': '
                . ($value ?? '-');
        }

        $textLines[] = str_repeat('-', 72);
    }

    $pdfData = buildPdfDocument($title, $textLines);

    $carpeta = __DIR__ . '/../reportes/';

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombreArchivo =
        'Reporte_' .
        $module .
        '_' .
        date('Ymd_His') .
        '.pdf';

    file_put_contents(
        $carpeta . $nombreArchivo,
        $pdfData
    );

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
    header('Content-Length: ' . strlen($pdfData));

    echo $pdfData;
    exit;

} catch (Exception $e) {

    die(
        "Error al generar reporte: "
        . $e->getMessage()
    );
}
?>