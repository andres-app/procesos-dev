<?php
// Vista/modulos/admin/exports/pdf.php
declare(strict_types=1);

require_once __DIR__ . '/../../../../Config/config.php';
require_once __DIR__ . '/../../../../vendor/tcpdf/tcpdf.php';
require_once __DIR__ . '/../../../../Modelo/MdPacAdmin.php';

$type = $type ?? ($_GET['type'] ?? 'resumen');
$anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? (int)$_GET['anio'] : (int)date('Y');

if ($type !== 'resumen') {
    $type = 'resumen';
}

$ejecucion = 4; // ACFFAA
$resumen = MdPacAdmin::obtenerResumenSituacion($anio, $ejecucion);

$fases              = $resumen['fases_orden'] ?? [];
$detalle            = $resumen['detalle'] ?? [];
$subtotales         = $resumen['subtotales'] ?? [];
$totales            = $resumen['totales'] ?? [];
$valorObac          = $resumen['valor_estimado_obac'] ?? [];
$modalidadesPorFase = $resumen['modalidades_por_fase'] ?? [];
$detallePlano       = $resumen['detalle_plano'] ?? [];

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function safeInt($value): int
{
    return (int)($value ?? 0);
}

function safeFloat($value): float
{
    return (float)($value ?? 0);
}

function fmtMoney($value): string
{
    return number_format((float)$value, 2, '.', ',');
}

function buildCell(string $content, string $align = 'C', bool $bold = false, string $bg = '', string $color = '#111827'): string
{
    $style = 'text-align:' . $alignCss = (
        $align === 'R' ? 'right' :
        ($align === 'L' ? 'left' : 'center')
    ) . ';';

    $style .= 'vertical-align:middle;';
    $style .= 'color:' . $color . ';';

    if ($bold) {
        $style .= 'font-weight:bold;';
    }

    if ($bg !== '') {
        $style .= 'background-color:' . $bg . ';';
    }

    return '<td style="' . $style . '">' . $content . '</td>';
}

function renderResumenTable(
    array $fases,
    array $detalle,
    array $subtotales,
    array $totales,
    array $valorObac,
    array $modalidadesPorFase
): string {
    $html = '';

    $html .= '
    <table border="1" cellpadding="4" cellspacing="0" width="100%" style="font-size:8.5pt;">
        <thead>
            <tr style="background-color:#ECEBBF; font-weight:bold; text-align:center;">
                <th rowspan="2" width="15%">FASES</th>
                <th rowspan="2" width="15%">MODALIDAD</th>
                <th colspan="5" width="30%">OBAC</th>
                <th rowspan="2" width="8%">EXP. PAC</th>
                <th rowspan="2" width="8%">PROCESOS</th>
                <th rowspan="2" width="16%">ESTIMADOS (SOLES)</th>
            </tr>
            <tr style="background-color:#ECEBBF; font-weight:bold; text-align:center;">
                <th width="6%">CCFFAA</th>
                <th width="6%">EP</th>
                <th width="6%">FAP</th>
                <th width="6%">MGP</th>
                <th width="6%">CONIDA</th>
            </tr>
        </thead>
        <tbody>
    ';

    foreach ($fases as $fase) {
        $mods = $modalidadesPorFase[$fase] ?? [];
        $rowspan = max(1, count($mods) + 1);
        $first = true;

        foreach ($mods as $modalidad) {
            $r = $detalle[$fase][$modalidad] ?? [];

            $html .= '<tr>';

            if ($first) {
                $html .= '<td rowspan="' . $rowspan . '" style="text-align:left; font-weight:bold; vertical-align:middle;">' . h($fase) . '</td>';
                $first = false;
            }

            $html .= '<td style="text-align:left;">' . h((string)$modalidad) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['CCFFAA'] ?? 0) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['EP'] ?? 0) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['FAP'] ?? 0) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['MGP'] ?? 0) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['CONIDA'] ?? 0) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['EXPEDIENTES'] ?? 0) . '</td>';
            $html .= '<td style="text-align:center;">' . safeInt($r['PROCESOS'] ?? 0) . '</td>';
            $html .= '<td style="text-align:right;">' . fmtMoney($r['ESTIMADO'] ?? 0) . '</td>';
            $html .= '</tr>';
        }

        $s = $subtotales[$fase] ?? [];

        $html .= '
            <tr style="background-color:#F6F1C7; font-weight:bold;">
                <td style="text-align:left;">SUB TOTAL</td>
                <td style="text-align:center;">' . safeInt($s['CCFFAA'] ?? 0) . '</td>
                <td style="text-align:center;">' . safeInt($s['EP'] ?? 0) . '</td>
                <td style="text-align:center;">' . safeInt($s['FAP'] ?? 0) . '</td>
                <td style="text-align:center;">' . safeInt($s['MGP'] ?? 0) . '</td>
                <td style="text-align:center;">' . safeInt($s['CONIDA'] ?? 0) . '</td>
                <td style="text-align:center;">' . safeInt($s['EXPEDIENTES'] ?? 0) . '</td>
                <td style="text-align:center;">' . safeInt($s['PROCESOS'] ?? 0) . '</td>
                <td style="text-align:right;">' . fmtMoney($s['ESTIMADO'] ?? 0) . '</td>
            </tr>
        ';
    }

    $html .= '
        <tr style="background-color:#DCEFE2; font-weight:bold;">
            <td colspan="2" style="text-align:center;">TOTAL</td>
            <td style="text-align:center;">' . safeInt($totales['CCFFAA'] ?? 0) . '</td>
            <td style="text-align:center;">' . safeInt($totales['EP'] ?? 0) . '</td>
            <td style="text-align:center;">' . safeInt($totales['FAP'] ?? 0) . '</td>
            <td style="text-align:center;">' . safeInt($totales['MGP'] ?? 0) . '</td>
            <td style="text-align:center;">' . safeInt($totales['CONIDA'] ?? 0) . '</td>
            <td style="text-align:center;">' . safeInt($totales['EXPEDIENTES'] ?? 0) . '</td>
            <td style="text-align:center;">' . safeInt($totales['PROCESOS'] ?? 0) . '</td>
            <td style="text-align:right;">' . fmtMoney($totales['ESTIMADO'] ?? 0) . '</td>
        </tr>
    ';

    $html .= '
        </tbody>
    </table>
    ';

    $html .= '
    <br><br>
    <table border="1" cellpadding="5" cellspacing="0" width="70%" style="font-size:8.5pt;">
        <tr style="background-color:#E8EEF8; font-weight:bold; text-align:center;">
            <th width="28%">VALOR ESTIMADO (SOLES)</th>
            <th width="14.4%">CCFFAA</th>
            <th width="14.4%">EP</th>
            <th width="14.4%">FAP</th>
            <th width="14.4%">MGP</th>
            <th width="14.4%">CONIDA</th>
        </tr>
        <tr>
            <td style="font-weight:bold; text-align:left;">Monto acumulado</td>
            <td style="text-align:right;">' . fmtMoney($valorObac['CCFFAA'] ?? 0) . '</td>
            <td style="text-align:right;">' . fmtMoney($valorObac['EP'] ?? 0) . '</td>
            <td style="text-align:right;">' . fmtMoney($valorObac['FAP'] ?? 0) . '</td>
            <td style="text-align:right;">' . fmtMoney($valorObac['MGP'] ?? 0) . '</td>
            <td style="text-align:right;">' . fmtMoney($valorObac['CONIDA'] ?? 0) . '</td>
        </tr>
    </table>
    ';

    return $html;
}

function renderDetalleBloque(string $fase, string $tipo, int $anio, array $items): string
{
    $tipoTitulo = ($tipo === 'Corporativo') ? 'CORPORATIVOS' : 'INDIVIDUALES';
    $titulo = 'PROCESOS ' . $tipoTitulo . ' ' . mb_strtoupper($fase, 'UTF-8') . ' AF-' . $anio;

    $html = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="background-color:#ECEBBF; border:1px solid #000000; font-weight:bold; font-size:10pt; text-align:center; padding:8px;">
                ' . h($titulo) . '
            </td>
        </tr>
    </table>
    <br>
    ';

    $html .= '
    <table border="1" cellpadding="3" cellspacing="0" width="100%" style="font-size:7.3pt;">
        <thead>
            <tr style="background-color:#D9EAF7; font-weight:bold; text-align:center;">
                <th width="5%">N° PROC</th>
                <th width="7%">EXP. PAC</th>
                <th width="6%">OBAC</th>
                <th width="18%">HISTORIAL</th>
                <th width="24%">DESCRIPCIÓN</th>
                <th width="5%">FF</th>
                <th width="5%">TP</th>
                <th width="9%">ESTIMADO</th>
                <th width="6%">FPC</th>
                <th width="7%">ESTADO</th>
                <th width="8%">SITUACIÓN</th>
            </tr>
        </thead>
        <tbody>
    ';

    $n = 1;
    foreach ($items as $item) {
        $html .= '<tr>';
        $html .= '<td style="text-align:center;">' . $n++ . '</td>';
        $html .= '<td style="text-align:center;">' . h((string)($item['nopac'] ?? '')) . '</td>';
        $html .= '<td style="text-align:center;">' . h((string)($item['obac'] ?? '')) . '</td>';
        $html .= '<td style="text-align:left;">' . nl2br(h((string)($item['historial'] ?? ''))) . '</td>';
        $html .= '<td style="text-align:left;">' . nl2br(h((string)($item['descripcion'] ?? ''))) . '</td>';
        $html .= '<td style="text-align:center;">' . h((string)($item['ff'] ?? '')) . '</td>';
        $html .= '<td style="text-align:center;">' . h((string)($item['tp'] ?? '')) . '</td>';
        $html .= '<td style="text-align:right;">' . fmtMoney($item['estimado'] ?? 0) . '</td>';
        $html .= '<td style="text-align:center;">' . h((string)($item['fpc'] ?? '')) . '</td>';
        $html .= '<td style="text-align:center;">' . h((string)($item['estado'] ?? '')) . '</td>';
        $html .= '<td style="text-align:left;">' . h((string)($item['situacion'] ?? '')) . '</td>';
        $html .= '</tr>';
    }

    if (empty($items)) {
        $html .= '
            <tr>
                <td colspan="11" style="text-align:center; font-style:italic;">Sin registros</td>
            </tr>
        ';
    }

    $html .= '
        </tbody>
    </table>
    ';

    return $html;
}

/*
|--------------------------------------------------------------------------
| TCPDF EXTENDIDO
|--------------------------------------------------------------------------
*/
class ReporteEstadoPDF extends TCPDF
{
    public string $tituloReporte = '';
    public string $subtitulo = '';

    public function Header(): void
    {
        $this->SetY(8);

        $html = '
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="20%" style="font-size:11pt; font-weight:bold; color:#0F2F5A;">ACFFAA</td>
                <td width="60%" style="font-size:11pt; font-weight:bold; text-align:center; color:#111827;">' . h($this->tituloReporte) . '</td>
                <td width="20%" style="font-size:11pt; font-weight:bold; text-align:right; color:#0F2F5A;">OPP</td>
            </tr>
            <tr>
                <td colspan="3" style="font-size:8pt; text-align:center; color:#6B7280;">' . h($this->subtitulo) . '</td>
            </tr>
        </table>
        <hr>
        ';

        $this->writeHTML($html, false, false, false, false, '');
    }

    public function Footer(): void
    {
        $this->SetY(-10);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 0, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'R');
    }
}

/*
|--------------------------------------------------------------------------
| PDF
|--------------------------------------------------------------------------
*/
$pdf = new ReporteEstadoPDF('L', 'mm', 'A4', true, 'UTF-8', false);

$pdf->tituloReporte = 'SITUACIÓN DE LOS EXPEDIENTES Y PROCESOS DE CONTRATACIÓN A CARGO DE LA ACFFAA AF-' . $anio;
$pdf->subtitulo = 'Reporte premium generado desde la misma fuente de datos del resumen ejecutivo';

$pdf->SetCreator('Sistema');
$pdf->SetAuthor('Andres');
$pdf->SetTitle('Reporte resumen ' . $anio);
$pdf->SetSubject('Reporte PDF resumen');
$pdf->SetKeywords('ACFFAA, PAC, Procesos, Resumen, PDF');

$pdf->SetMargins(8, 22, 8);
$pdf->SetHeaderMargin(6);
$pdf->SetFooterMargin(6);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetFont('helvetica', '', 9);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

/*
|--------------------------------------------------------------------------
| PORTADA RESUMEN
|--------------------------------------------------------------------------
*/
$pdf->AddPage();

$totExpedientes = safeInt($totales['EXPEDIENTES'] ?? 0);
$totProcesos    = safeInt($totales['PROCESOS'] ?? 0);
$totEstimado    = safeFloat($totales['ESTIMADO'] ?? 0);
$totalFases     = count($fases);

$htmlIntro = '
<table border="0" cellpadding="6" cellspacing="0" width="100%">
    <tr>
        <td width="25%" style="background-color:#F8FAFC; border:1px solid #D1D5DB; text-align:center;">
            <div style="font-size:8pt; color:#6B7280;">Fases</div>
            <div style="font-size:16pt; font-weight:bold; color:#0F172A;">' . $totalFases . '</div>
        </td>
        <td width="25%" style="background-color:#F8FAFC; border:1px solid #D1D5DB; text-align:center;">
            <div style="font-size:8pt; color:#6B7280;">Expedientes PAC</div>
            <div style="font-size:16pt; font-weight:bold; color:#0F172A;">' . $totExpedientes . '</div>
        </td>
        <td width="25%" style="background-color:#F8FAFC; border:1px solid #D1D5DB; text-align:center;">
            <div style="font-size:8pt; color:#6B7280;">Procesos</div>
            <div style="font-size:16pt; font-weight:bold; color:#0F172A;">' . $totProcesos . '</div>
        </td>
        <td width="25%" style="background-color:#F8FAFC; border:1px solid #D1D5DB; text-align:center;">
            <div style="font-size:8pt; color:#6B7280;">Estimado total</div>
            <div style="font-size:14pt; font-weight:bold; color:#0F172A;">S/ ' . fmtMoney($totEstimado) . '</div>
        </td>
    </tr>
</table>
<br>
';

$pdf->writeHTML($htmlIntro, true, false, true, false, '');

$htmlResumen = renderResumenTable(
    $fases,
    $detalle,
    $subtotales,
    $totales,
    $valorObac,
    $modalidadesPorFase
);

$pdf->writeHTML($htmlResumen, true, false, true, false, '');

/*
|--------------------------------------------------------------------------
| DETALLE POR FASE
|--------------------------------------------------------------------------
*/
foreach ($fases as $fase) {
    if (empty($detallePlano[$fase]) || !is_array($detallePlano[$fase])) {
        continue;
    }

    $pdf->AddPage();

    $tituloFase = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="background-color:#0F2F5A; color:#FFFFFF; font-weight:bold; text-align:left; padding:8px; font-size:11pt;">
                DETALLE DE FASE: ' . h(mb_strtoupper($fase, 'UTF-8')) . '
            </td>
        </tr>
    </table>
    <br>
    ';
    $pdf->writeHTML($tituloFase, true, false, true, false, '');

    foreach (['Corporativo', 'Individual'] as $tipo) {
        $items = $detallePlano[$fase][$tipo] ?? [];

        if (empty($items) || !is_array($items)) {
            continue;
        }

        $htmlBloque = renderDetalleBloque($fase, $tipo, $anio, $items);
        $pdf->writeHTML($htmlBloque, true, false, true, false, '');
        $pdf->Ln(3);
    }
}

/*
|--------------------------------------------------------------------------
| SALIDA
|--------------------------------------------------------------------------
*/
$filename = 'REPORTE_ESTADO_' . $anio . '_' . date('d-m') . '.pdf';

if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output($filename, 'D');
exit;