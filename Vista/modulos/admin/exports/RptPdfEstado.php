<?php
// Vista/modulos/admin/exports/RptPdfEstado.php
declare(strict_types=1);

require_once __DIR__ . '/../../../../Config/config.php';
require_once __DIR__ . '/../../../../vendor/tcpdf/tcpdf.php';
require_once __DIR__ . '/../../../../Modelo/MdPacAdmin.php';

$type = $type ?? ($_GET['type'] ?? 'resumen');
$anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? (int)$anio : (int)($_GET['anio'] ?? date('Y'));

if ($type !== 'resumen') {
    $type = 'resumen';
}

$ejecucion = 4; // ACFFAA
$resumen   = MdPacAdmin::obtenerResumenSituacion($anio, $ejecucion);

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

function txt($value): string
{
    $v = trim((string)($value ?? ''));
    return $v !== '' ? h($v) : '&nbsp;';
}

function txtMulti($value): string
{
    $v = trim((string)($value ?? ''));
    return $v !== '' ? nl2br(h($v)) : '&nbsp;';
}

function upper($value): string
{
    return mb_strtoupper((string)$value, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| ESTILO BASE
|--------------------------------------------------------------------------
*/
function baseCss(): string
{
    return '
    <style>
        .muted { color:#64748B; }
        .text-dark { color:#0F172A; }
        .text-brand { color:#0F2F5A; }

        .kpi-wrap{
            border:1px solid #C7D3DF;
            background-color:#F8FAFC;
            text-align:center;
            padding:14px 8px 12px 8px;
            height:56px;
            vertical-align:middle;
        }
        .kpi-primary{
            background-color:#F2F7FC;
        }
        .kpi-label{
            font-size:7.6pt;
            color:#64748B;
            letter-spacing:0.2px;
        }
        .kpi-value{
            font-size:17.5pt;
            font-weight:bold;
            color:#0F172A;
            line-height:1.0;
        }
        .kpi-value-primary{
            font-size:21pt;
        }
        .kpi-value-money{
            font-size:13pt;
            font-weight:bold;
            color:#0F172A;
            line-height:1.0;
        }

        .summary-table{
            border-collapse:collapse;
            font-size:7.9pt;
            line-height:1.18;
        }
        .summary-table th,
        .summary-table td{
            border:1px solid #6B7280;
            padding:4px 3px;
            vertical-align:middle;
        }
        .summary-head-1 th{
            background-color:#DCE6F1;
            text-align:center;
            font-weight:bold;
            color:#0F172A;
        }
        .summary-head-2 th{
            background-color:#EEF3F8;
            text-align:center;
            font-weight:bold;
            color:#0F172A;
        }
        .summary-fase{
            background-color:#F8FAFC;
            font-weight:bold;
            text-align:left;
        }
        .summary-subtotal td{
            background-color:#FFF5CC;
            font-weight:bold;
        }
        .summary-total td{
            background-color:#E6F4EA;
            font-weight:bold;
        }
        .left{ text-align:left; }
        .center{ text-align:center; }
        .right{ text-align:right; }

        .mini-table{
            border-collapse:collapse;
            font-size:8pt;
            line-height:1.2;
        }
        .mini-table th,
        .mini-table td{
            border:1px solid #94A3B8;
            padding:5px 4px;
            vertical-align:middle;
        }
        .mini-table th{
            background-color:#EAF1FB;
            font-weight:bold;
            text-align:center;
        }

        .fase-banner{
            background-color:#0F2F5A;
            color:#FFFFFF;
            border:1px solid #0B2344;
            font-weight:bold;
            font-size:10pt;
            padding:7px 8px;
        }

        .block-banner{
            background-color:#ECEBBF;
            color:#1F2937;
            border:1px solid #8A8657;
            font-weight:bold;
            font-size:9.2pt;
            text-align:center;
            padding:6px 6px;
        }

        .detail-table{
            border-collapse:collapse;
            font-size:6.6pt;
            line-height:1.08;
        }
        .detail-table th,
        .detail-table td{
            border:1px solid #7C8EA3;
            padding:2.4px 2.2px;
            vertical-align:middle;
        }
        .detail-head th{
            background-color:#E6EEF7;
            text-align:center;
            font-weight:bold;
            font-size:6.8pt;
        }
        .detail-odd td{
            background-color:#FFFFFF;
        }
        .detail-even td{
            background-color:#FAFCFE;
        }
        .detail-empty td{
            text-align:center;
            font-style:italic;
            color:#64748B;
            padding:8px;
        }
    </style>
    ';
}

function mapTp($tp): string
{
    $tpRaw = trim((string)$tp);
    $tp    = mb_strtoupper($tpRaw, 'UTF-8');

    if ($tp === '') {
        return '-';
    }

    return match (true) {
        str_contains($tp, 'LICITACIÓN') || str_contains($tp, 'LICITACION') => 'LP',
        str_contains($tp, 'RÉGIMEN')    || str_contains($tp, 'REGIMEN')    => 'RES',
        str_contains($tp, 'DIRECTA')                                     => 'CD',
        str_contains($tp, 'SUBASTA')                                     => 'SIE',
        str_contains($tp, 'COMPARACIÓN') || str_contains($tp, 'COMPARACION') => 'CP',
        default => $tpRaw,
    };
}

function detailWidths(): array
{
    return [
        'n'       => 4.0,
        'pn'      => 5.0,
        'exp'     => 6.0,
        'obac'    => 5.0,
        'hist'    => 16.0,
        'desc'    => 24.0,
        'ff'      => 4.0,
        'tp'      => 5.0,
        'est'     => 10.0,
        'fpc'     => 6.0,
        'estado'  => 7.0,
        'sit'     => 10.0,
    ];
}

function renderDetalleTituloHtml(string $fase, string $tipo, int $anio): string
{
    $tipoTitulo = ($tipo === 'Corporativo') ? 'CORPORATIVOS' : 'INDIVIDUALES';
    $titulo = 'PROCESOS ' . $tipoTitulo . ' ' . upper($fase) . ' AF-' . $anio;

    return baseCss() . '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td class="block-banner">' . h($titulo) . '</td>
        </tr>
    </table>
    <br>
    ';
}

function renderDetalleHeadHtml(): string
{
    $w = detailWidths();

    return baseCss() . '
    <table class="detail-table" width="100%">
        <thead>
            <tr class="detail-head">
                <th width="' . $w['n'] . '%">N° PROC</th>
                <th width="' . $w['pn'] . '%">P/NP</th>
                <th width="' . $w['exp'] . '%">EXP. PAC</th>
                <th width="' . $w['obac'] . '%">OBAC</th>
                <th width="' . $w['hist'] . '%">HISTORIAL</th>
                <th width="' . $w['desc'] . '%">DESCRIPCIÓN</th>
                <th width="' . $w['ff'] . '%">FF</th>
                <th width="' . $w['tp'] . '%">TP</th>
                <th width="' . $w['est'] . '%">ESTIMADO</th>
                <th width="' . $w['fpc'] . '%">FPC</th>
                <th width="' . $w['estado'] . '%">ESTADO</th>
                <th width="' . $w['sit'] . '%">SITUACIÓN</th>
            </tr>
        </thead>
    </table>
    ';
}

function renderDetalleRowHtml(array $item, int $n, bool $even = false): string
{
    $w   = detailWidths();
    $cls = $even ? 'detail-even' : 'detail-odd';

    return baseCss() . '
    <table class="detail-table" width="100%">
        <tbody>
            <tr class="' . $cls . '">
                <td width="' . $w['n'] . '%" class="center">' . $n . '</td>
                <td width="' . $w['pn'] . '%" class="center">' . txt($item['pn'] ?? '') . '</td>
                <td width="' . $w['exp'] . '%" class="center">' . txt($item['nopac'] ?? '') . '</td>
                <td width="' . $w['obac'] . '%" class="center">' . txt($item['obac'] ?? '') . '</td>
                <td width="' . $w['hist'] . '%" class="left">' . txtMulti($item['historial'] ?? '') . '</td>
                <td width="' . $w['desc'] . '%" class="left">' . txtMulti($item['descripcion'] ?? '') . '</td>
                <td width="' . $w['ff'] . '%" class="center">' . txt($item['ff'] ?? '') . '</td>
                <td width="' . $w['tp'] . '%" class="center">' . h(mapTp($item['tp'] ?? '')) . '</td>
                <td width="' . $w['est'] . '%" class="right">' . fmtMoney($item['estimado'] ?? 0) . '</td>
                <td width="' . $w['fpc'] . '%" class="center">' . txt($item['fpc'] ?? '') . '</td>
                <td width="' . $w['estado'] . '%" class="center">' . txt($item['estado'] ?? '') . '</td>
                <td width="' . $w['sit'] . '%" class="left">' . txtMulti($item['situacion'] ?? '') . '</td>
            </tr>
        </tbody>
    </table>
    ';
}

function renderDetalleEmptyHtml(): string
{
    return baseCss() . '
    <table class="detail-table" width="100%">
        <tbody>
            <tr class="detail-empty">
                <td colspan="12">Sin registros</td>
            </tr>
        </tbody>
    </table>
    ';
}

/*
|--------------------------------------------------------------------------
| RESUMEN EJECUTIVO
|--------------------------------------------------------------------------
*/
function renderResumenTable(
    array $fases,
    array $detalle,
    array $subtotales,
    array $totales,
    array $valorObac,
    array $modalidadesPorFase
): string {
    // Total exacto = 100
    $wFase      = 13;
    $wModalidad = 17;
    $wObac      = 5.6; // x5 = 28
    $wExpPac    = 9;
    $wProcesos  = 9;
    $wEstimado  = 24;

    $html  = baseCss();
    $html .= '
    <table class="summary-table" width="100%">
        <thead>
            <tr class="summary-head-1">
                <th rowspan="2" width="' . $wFase . '%">FASES</th>
                <th rowspan="2" width="' . $wModalidad . '%">MODALIDAD</th>
                <th colspan="5" width="' . ($wObac * 5) . '%">OBAC</th>
                <th rowspan="2" width="' . $wExpPac . '%">EXP. PAC</th>
                <th rowspan="2" width="' . $wProcesos . '%">PROCESOS</th>
                <th rowspan="2" width="' . $wEstimado . '%">ESTIMADOS (SOLES)</th>
            </tr>
            <tr class="summary-head-2">
                <th width="' . $wObac . '%">CCFFAA</th>
                <th width="' . $wObac . '%">EP</th>
                <th width="' . $wObac . '%">FAP</th>
                <th width="' . $wObac . '%">MGP</th>
                <th width="' . $wObac . '%">CONIDA</th>
            </tr>
        </thead>
        <tbody>
    ';

    foreach ($fases as $fase) {
        $mods = $modalidadesPorFase[$fase] ?? [];
        if (empty($mods)) {
            $mods = ['-'];
        }

        $rowspan = count($mods) + 1;
        $first   = true;

        foreach ($mods as $modalidad) {
            $r = $detalle[$fase][$modalidad] ?? [];

            $html .= '<tr>';

            if ($first) {
                $html .= '<td rowspan="' . $rowspan . '" width="' . $wFase . '%" class="summary-fase">' . h($fase) . '</td>';
                $first = false;
            }

            $html .= '<td width="' . $wModalidad . '%" class="left">' . h((string)$modalidad) . '</td>';
            $html .= '<td width="' . $wObac . '%" class="center">' . safeInt($r['CCFFAA'] ?? 0) . '</td>';
            $html .= '<td width="' . $wObac . '%" class="center">' . safeInt($r['EP'] ?? 0) . '</td>';
            $html .= '<td width="' . $wObac . '%" class="center">' . safeInt($r['FAP'] ?? 0) . '</td>';
            $html .= '<td width="' . $wObac . '%" class="center">' . safeInt($r['MGP'] ?? 0) . '</td>';
            $html .= '<td width="' . $wObac . '%" class="center">' . safeInt($r['CONIDA'] ?? 0) . '</td>';
            $html .= '<td width="' . $wExpPac . '%" class="center">' . safeInt($r['EXPEDIENTES'] ?? 0) . '</td>';
            $html .= '<td width="' . $wProcesos . '%" class="center">' . safeInt($r['PROCESOS'] ?? 0) . '</td>';
            $html .= '<td width="' . $wEstimado . '%" class="right">' . fmtMoney($r['ESTIMADO'] ?? 0) . '</td>';
            $html .= '</tr>';
        }

        $s = $subtotales[$fase] ?? [];

        $html .= '
        <tr class="summary-subtotal">
            <td width="' . $wModalidad . '%">SUB TOTAL</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($s['CCFFAA'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($s['EP'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($s['FAP'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($s['MGP'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($s['CONIDA'] ?? 0) . '</td>
            <td width="' . $wExpPac . '%" class="center">' . safeInt($s['EXPEDIENTES'] ?? 0) . '</td>
            <td width="' . $wProcesos . '%" class="center">' . safeInt($s['PROCESOS'] ?? 0) . '</td>
            <td width="' . $wEstimado . '%" class="right">' . fmtMoney($s['ESTIMADO'] ?? 0) . '</td>
        </tr>
        ';
    }

    $html .= '
        <tr class="summary-total">
            <td colspan="2" width="' . ($wFase + $wModalidad) . '%" class="center">TOTAL</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($totales['CCFFAA'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($totales['EP'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($totales['FAP'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($totales['MGP'] ?? 0) . '</td>
            <td width="' . $wObac . '%" class="center">' . safeInt($totales['CONIDA'] ?? 0) . '</td>
            <td width="' . $wExpPac . '%" class="center">' . safeInt($totales['EXPEDIENTES'] ?? 0) . '</td>
            <td width="' . $wProcesos . '%" class="center">' . safeInt($totales['PROCESOS'] ?? 0) . '</td>
            <td width="' . $wEstimado . '%" class="right">' . fmtMoney($totales['ESTIMADO'] ?? 0) . '</td>
        </tr>
        </tbody>
    </table>

    <br><br>

    <table class="mini-table" width="72%">
        <tr>
            <th width="28%">VALOR ESTIMADO (SOLES)</th>
            <th width="14.4%">CCFFAA</th>
            <th width="14.4%">EP</th>
            <th width="14.4%">FAP</th>
            <th width="14.4%">MGP</th>
            <th width="14.4%">CONIDA</th>
        </tr>
        <tr>
            <td><b>Monto acumulado</b></td>
            <td class="right">' . fmtMoney($valorObac['CCFFAA'] ?? 0) . '</td>
            <td class="right">' . fmtMoney($valorObac['EP'] ?? 0) . '</td>
            <td class="right">' . fmtMoney($valorObac['FAP'] ?? 0) . '</td>
            <td class="right">' . fmtMoney($valorObac['MGP'] ?? 0) . '</td>
            <td class="right">' . fmtMoney($valorObac['CONIDA'] ?? 0) . '</td>
        </tr>
    </table>
    ';

    return $html;
}

/*
|--------------------------------------------------------------------------
| DETALLE
|--------------------------------------------------------------------------
| Distribución más fina para que se vea mejor en A4 horizontal:
| N° / EXP / OBAC compactos
| HISTORIAL y DESCRIPCIÓN grandes
| SITUACIÓN con mejor aire
|--------------------------------------------------------------------------
*/
function renderDetalleBloque(string $fase, string $tipo, int $anio, array $items): string
{
    $tipoTitulo = ($tipo === 'Corporativo') ? 'CORPORATIVOS' : 'INDIVIDUALES';
    $titulo = 'PROCESOS ' . $tipoTitulo . ' ' . upper($fase) . ' AF-' . $anio;

    // TOTAL = 100
    // Se le da MÁS ancho a TP, ESTADO y SITUACIÓN
    // y se compacta un poco HISTORIAL / DESCRIPCIÓN
    $wN        = 4.0;
    $wPn       = 5.0;
    $wExp      = 6.5;
    $wObac     = 5.0;
    $wHist     = 17.0;
    $wDesc     = 22.0;
    $wFf       = 4.0;
    $wTp       = 8.5;
    $wEst      = 9.5;
    $wFpc      = 6.0;
    $wEstado   = 7.5;
    $wSit      = 7.5;

    $html  = baseCss();
    $html .= '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td class="block-banner">' . h($titulo) . '</td>
        </tr>
    </table>
    <br>
    ';

    $html .= '
    <table class="detail-table" width="100%">
        <thead>
            <tr class="detail-head">
                <th width="' . $wN . '%">N° PROC</th>
                <th width="' . $wPn . '%">P/NP</th>
                <th width="' . $wExp . '%">EXP. PAC</th>
                <th width="' . $wObac . '%">OBAC</th>
                <th width="' . $wHist . '%">HISTORIAL</th>
                <th width="' . $wDesc . '%">DESCRIPCIÓN</th>
                <th width="' . $wFf . '%">FF</th>
                <th width="' . $wTp . '%">TP</th>
                <th width="' . $wEst . '%">ESTIMADO</th>
                <th width="' . $wFpc . '%">FPC</th>
                <th width="' . $wEstado . '%">ESTADO</th>
                <th width="' . $wSit . '%">SITUACIÓN</th>
            </tr>
        </thead>
        <tbody>
    ';

    if (empty($items)) {
        $html .= '
        <tr class="detail-empty">
            <td colspan="11">Sin registros</td>
        </tr>
        ';
    } else {
        $n = 1;
        foreach ($items as $i => $item) {
            $cls = ($i % 2 === 0) ? 'detail-odd' : 'detail-even';

            $tp = trim((string)($item['tp'] ?? ''));

            // Evita que TCPDF rompa palabra por palabra
            $tp = str_replace(
                ['Licitación Pública', 'Régimen Especial', 'Comparación de Precios', 'Contratación Directa', 'Subasta Inversa Electrónica'],
                ['Licitación Pública', 'Régimen Especial', 'Comparación de Precios', 'Contratación Directa', 'Subasta Inversa Electrónica'],
                $tp
            );

            $html .= '
            <tr class="' . $cls . '">
                <td width="' . $wN . '%" class="center">' . $n++ . '</td>
                <td width="' . $wPn . '%" class="center">' . txt($item['pn'] ?? '') . '</td>
                <td width="' . $wExp . '%" class="center">' . txt($item['nopac'] ?? '') . '</td>
                <td width="' . $wObac . '%" class="center">' . txt($item['obac'] ?? '') . '</td>
                <td width="' . $wHist . '%" class="left">' . txtMulti($item['historial'] ?? '') . '</td>
                <td width="' . $wDesc . '%" class="left">' . txtMulti($item['descripcion'] ?? '') . '</td>
                <td width="' . $wFf . '%" class="center">' . txt($item['ff'] ?? '') . '</td>
                <td width="' . $wTp . '%" class="center">' . mapTp($item['tp'] ?? '') . '</td>
                <td width="' . $wEst . '%" class="right">' . fmtMoney($item['estimado'] ?? 0) . '</td>
                <td width="' . $wFpc . '%" class="center">' . txt($item['fpc'] ?? '') . '</td>
                <td width="' . $wEstado . '%" class="center">' . txt($item['estado'] ?? '') . '</td>
                <td width="' . $wSit . '%" class="left">' . txtMulti($item['situacion'] ?? '') . '</td>
            </tr>
            ';
        }
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
        $this->SetY(7);

        $html = '
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="18%" style="font-size:10.8pt; font-weight:bold; color:#0F2F5A;">ACFFAA</td>
                <td width="64%" style="font-size:10.3pt; font-weight:bold; text-align:center; color:#111827;">' . h($this->tituloReporte) . '</td>
                <td width="18%" style="font-size:10.8pt; font-weight:bold; text-align:right; color:#0F2F5A;">OPP</td>
            </tr>
            <tr>
                <td colspan="3" style="font-size:7.5pt; text-align:center; color:#64748B;">' . h($this->subtitulo) . '</td>
            </tr>
            <tr>
                <td colspan="3" style="border-bottom:1px solid #CBD5E1;"></td>
            </tr>
        </table>
        ';

        $this->writeHTML($html, false, false, false, false, '');
    }

    public function Footer(): void
    {
        $this->SetY(-9);
        $this->SetFont('helvetica', '', 7.6);
        $this->SetTextColor(100, 116, 139);
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
$pdf->subtitulo     = 'Reporte ejecutivo';

$pdf->SetCreator('Sistema');
$pdf->SetAuthor('Andres');
$pdf->SetTitle('Reporte resumen ' . $anio);
$pdf->SetSubject('Reporte PDF resumen');
$pdf->SetKeywords('ACFFAA, PAC, Procesos, Resumen, PDF');

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

$pdf->SetMargins(7, 20, 7);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(5);
$pdf->SetAutoPageBreak(true, 8);
$pdf->SetFont('helvetica', '', 7.6);
$pdf->SetTextColor(17, 24, 39);
$pdf->SetDrawColor(148, 163, 184);
$pdf->SetLineWidth(0.15);
$pdf->setCellHeightRatio(1.05);

// Menos espacio fantasma en HTML
$pdf->setHtmlVSpace([
    'p'   => ['h' => 0, 'n' => 0],
    'div' => ['h' => 0, 'n' => 0],
    'br'  => ['h' => 0, 'n' => 0],
]);

/*
|--------------------------------------------------------------------------
| PORTADA / RESUMEN
|--------------------------------------------------------------------------
*/
$pdf->AddPage();

$totExpedientes = safeInt($totales['EXPEDIENTES'] ?? 0);
$totProcesos    = safeInt($totales['PROCESOS'] ?? 0);
$totEstimado    = safeFloat($totales['ESTIMADO'] ?? 0);
$totalFases     = count($fases);

$htmlIntro  = baseCss();
$htmlIntro .= '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="6%">&nbsp;</td>

        <td width="18%" style="padding-right:4px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="kpi-wrap kpi-primary">
                        <div class="kpi-label">Fases</div>
                        <div style="height:7px;"></div>
                        <div class="kpi-value kpi-value-primary">' . $totalFases . '</div>
                    </td>
                </tr>
            </table>
        </td>

        <td width="4%">&nbsp;</td>

        <td width="18%" style="padding-right:4px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="kpi-wrap">
                        <div class="kpi-label">Expedientes PAC</div>
                        <div style="height:7px;"></div>
                        <div class="kpi-value">' . $totExpedientes . '</div>
                    </td>
                </tr>
            </table>
        </td>

        <td width="4%">&nbsp;</td>

        <td width="18%" style="padding-right:4px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="kpi-wrap">
                        <div class="kpi-label">Procesos</div>
                        <div style="height:7px;"></div>
                        <div class="kpi-value">' . $totProcesos . '</div>
                    </td>
                </tr>
            </table>
        </td>

        <td width="4%">&nbsp;</td>

        <td width="22%">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="kpi-wrap">
                        <div class="kpi-label">Estimado total</div>
                        <div style="height:7px;"></div>
                        <div class="kpi-value-money">S/ ' . fmtMoney($totEstimado) . '</div>
                    </td>
                </tr>
            </table>
        </td>

        <td width="6%">&nbsp;</td>
    </tr>
</table>
<br>
';

$pdf->writeHTML($htmlIntro, true, false, true, false, '');

$pdf->writeHTML(
    renderResumenTable(
        $fases,
        $detalle,
        $subtotales,
        $totales,
        $valorObac,
        $modalidadesPorFase
    ),
    true,
    false,
    true,
    false,
    ''
);

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

    $faseTitle  = baseCss();
    $faseTitle .= '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td class="fase-banner">DETALLE DE FASE: ' . h(upper($fase)) . '</td>
        </tr>
    </table>
    <br>
    ';

    $pdf->writeHTML($faseTitle, true, false, false, false, '');

    foreach (['Corporativo', 'Individual'] as $tipo) {
        $items = $detallePlano[$fase][$tipo] ?? [];

        if (!is_array($items)) {
            $items = [];
        }

        // omitir bloque vacío
        if (empty($items)) {
            continue;
        }

        // Antes de empezar el bloque, si queda poco espacio, nueva página
        if ($pdf->GetY() > 150) {
            $pdf->AddPage();

            $faseCont  = baseCss();
            $faseCont .= '
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="fase-banner">DETALLE DE FASE: ' . h(upper($fase)) . '</td>
                </tr>
            </table>
            <br>
            ';

            $pdf->writeHTML($faseCont, true, false, false, false, '');
        }

        $pdf->writeHTML(renderDetalleTituloHtml($fase, $tipo, $anio), true, false, false, false, '');
        $pdf->writeHTML(renderDetalleHeadHtml(), true, false, false, false, '');

        $n = 1;
        foreach ($items as $i => $item) {
            $rowHtml = renderDetalleRowHtml($item, $n, $i % 2 !== 0);

            // Medimos altura aproximada de la fila antes de imprimirla
            $rowHeight = $pdf->getStringHeight(
                277, // ancho útil aprox. en A4 horizontal con márgenes pequeños
                trim(
                    (string)($item['historial'] ?? '') . ' ' .
                        (string)($item['descripcion'] ?? '') . ' ' .
                        (string)($item['situacion'] ?? '')
                )
            );

            // base mínima razonable para filas cortas
            $rowHeight = max($rowHeight, 8);

            $bottomLimit = 200; // zona segura antes del footer

            if (($pdf->GetY() + $rowHeight) > $bottomLimit) {
                $pdf->AddPage();

                $faseCont  = baseCss();
                $faseCont .= '
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td class="fase-banner">DETALLE DE FASE: ' . h(upper($fase)) . '</td>
                    </tr>
                </table>
                <br>
                ';

                $pdf->writeHTML($faseCont, true, false, false, false, '');
                $pdf->writeHTML(renderDetalleTituloHtml($fase, $tipo, $anio), true, false, false, false, '');
                $pdf->writeHTML(renderDetalleHeadHtml(), true, false, false, false, '');
            }

            $pdf->writeHTML($rowHtml, true, false, false, false, '');
            $n++;
        }

        $pdf->Ln(2);
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
