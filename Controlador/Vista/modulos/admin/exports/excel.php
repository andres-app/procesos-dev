<?php
require_once __DIR__ . '/../../../../Config/config.php';

// viene desde router: $type = $parts[2] ?? 'estado';
$type = $type ?? ($_GET['type'] ?? 'estado');

$filename = 'reporte_'.$type.'_'.date('Ymd_His').'.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
echo "\xEF\xBB\xBF"; // BOM

$out = fopen('php://output', 'w');
fputcsv($out, ['REPORTE', 'TIPO', 'GENERADO'], ';');
fputcsv($out, ['MAQUETA', (string)$type, date('Y-m-d H:i:s')], ';');
fclose($out);
exit;