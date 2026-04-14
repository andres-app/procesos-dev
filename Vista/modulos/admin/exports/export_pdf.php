<?php
// Vista/modulos/admin/exports/export_pdf.php
require_once __DIR__ . '/../../../../Config/config.php';

$type = $type ?? ($_GET['type'] ?? 'estado');
$type = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$type);

if ($type === '') {
    $type = 'estado';
}

/*
|--------------------------------------------------------------------------
| AJUSTA ESTA RUTA SEGÚN TU PC O SERVIDOR
|--------------------------------------------------------------------------
*/
$wkhtmltopdf = 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe';

/*
|--------------------------------------------------------------------------
| URL HTML QUE SE VA A CONVERTIR
|--------------------------------------------------------------------------
*/
$viewUrl = rtrim(BASE_URL, '/') . '/admin/export_pdf_view/' . rawurlencode($type);

/*
|--------------------------------------------------------------------------
| ARCHIVO TEMPORAL
|--------------------------------------------------------------------------
*/
$tmpPdf = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'reporte_' . $type . '_' . uniqid() . '.pdf';

/*
|--------------------------------------------------------------------------
| COMANDO
|--------------------------------------------------------------------------
*/
$cmd =
    '"' . $wkhtmltopdf . '"' .
    ' --enable-local-file-access' .
    ' --page-size A4' .
    ' --orientation Portrait' .
    ' --margin-top 0' .
    ' --margin-right 0' .
    ' --margin-bottom 0' .
    ' --margin-left 0' .
    ' ' . escapeshellarg($viewUrl) .
    ' ' . escapeshellarg($tmpPdf);

/*
|--------------------------------------------------------------------------
| EJECUTAR
|--------------------------------------------------------------------------
*/
exec($cmd . ' 2>&1', $output, $result);

if ($result !== 0 || !file_exists($tmpPdf)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error generando PDF.\n\n";
    echo "Comando ejecutado:\n" . $cmd . "\n\n";
    echo "Salida:\n" . implode("\n", $output);
    exit;
}

/*
|--------------------------------------------------------------------------
| DEVOLVER PDF REAL INLINE
|--------------------------------------------------------------------------
*/
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="reporte_' . $type . '.pdf"');
header('Content-Length: ' . filesize($tmpPdf));

readfile($tmpPdf);
@unlink($tmpPdf);
exit;