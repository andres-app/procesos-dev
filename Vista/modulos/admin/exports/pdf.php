<?php
// Vista/modulos/admin/exports/pdf.php
require_once __DIR__ . '/../../../../Config/config.php';
require_once __DIR__ . '/../../../../vendor/tcpdf/tcpdf.php';

$type = $type ?? ($_GET['type'] ?? 'estado');
$type = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$type);

if ($type === '') {
    $type = 'estado';
}

$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator('Sistema');
$pdf->SetAuthor('Andres');
$pdf->SetTitle('Reporte ' . $type);
$pdf->SetSubject('Reporte ' . $type);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();

$html = '
<h2>Reporte: ' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '</h2>
<br>
<table border="1" cellpadding="5">
    <tr style="background-color:#f2f2f2;">
        <th>N° PAC</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Estimado</th>
    </tr>
    <tr>
        <td>001</td>
        <td>Ejemplo PAC</td>
        <td>PUBLICADO</td>
        <td>S/ 10,000</td>
    </tr>
</table>
';

$pdf->writeHTML($html, true, false, true, false, '');

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('reporte_' . $type . '.pdf', 'D');
exit;