<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../../Config/config.php';
require_once __DIR__ . '/../../../../Modelo/MdPacAdmin.php';

$type = $type ?? ($_GET['type'] ?? 'resumen');
$anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? (int)$_GET['anio'] : (int)date('Y');

if ($type !== 'resumen') {
    $type = 'resumen';
}

$resumen = MdPacAdmin::obtenerResumenSituacion($anio);

$filename = 'reporte_resumen_' . $anio . '_' . date('Ymd_His') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

function h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function n($v): string
{
    return number_format((float)$v, 2, '.', ',');
}

$fases              = $resumen['fases_orden'];
$detalle            = $resumen['detalle'];
$subtotales         = $resumen['subtotales'];
$totales            = $resumen['totales'];
$valorObac          = $resumen['valor_estimado_obac'];
$modalidadesPorFase = $resumen['modalidades_por_fase'];

echo "\xEF\xBB\xBF";
?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            width: 100%;
        }

        td, th {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .top-left {
            font-weight: bold;
            text-align: left;
            font-size: 16px;
        }

        .top-right {
            font-weight: bold;
            text-align: center;
            font-size: 16px;
        }

        .title {
            background: #ecebbf;
            font-weight: bold;
            text-align: center;
            font-size: 13px;
        }

        .head {
            background: #ecebbf;
            font-weight: bold;
            text-align: center;
        }

        .sub {
            background: #ecebbf;
            font-weight: bold;
            text-align: center;
        }

        .sub-left {
            background: #ecebbf;
            font-weight: bold;
            text-align: left;
        }

        .left {
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .num {
            text-align: center;
            mso-number-format: "0";
        }

        .money {
            text-align: right;
            mso-number-format: "#,##0.00";
        }

        .blank td {
            border: none !important;
            height: 10px;
            padding: 0;
        }
    </style>
</head>
<body>
    <table>
        <colgroup>
            <col style="width: 220px;">
            <col style="width: 120px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 80px;">
            <col style="width: 100px;">
            <col style="width: 100px;">
            <col style="width: 140px;">
        </colgroup>

        <tr>
            <td colspan="4" class="top-left">AGENCIA</td>
            <td style="border:none;"></td>
            <td style="border:none;"></td>
            <td style="border:none;"></td>
            <td colspan="3" class="top-right">OFICINA DE PLANEAMIENTO Y PRESUPUESTO</td>
        </tr>

        <tr class="blank">
            <td></td><td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
            <td colspan="10" class="title">
                SITUACIÓN DE LOS EXPEDIENTES Y PROCESOS DE CONTRATACIÓN A CARGO DE LA ACFFAA AF-<?= h((string)$anio) ?>
            </td>
        </tr>

        <tr>
            <td rowspan="2" class="head">FASES</td>
            <td rowspan="2" class="head">MODALIDAD</td>
            <td colspan="5" class="head">OBAC</td>
            <td rowspan="2" class="head">EXPEDIENTE<br>S PAC</td>
            <td rowspan="2" class="head">PROCESOS</td>
            <td rowspan="2" class="head">ESTIMADOS<br>(SOLES)</td>
        </tr>
        <tr>
            <td class="head">CCFFAA</td>
            <td class="head">EP</td>
            <td class="head">FAP</td>
            <td class="head">MGP</td>
            <td class="head">CONIDA</td>
        </tr>

        <?php foreach ($fases as $fase): ?>
            <?php
            $mods = $modalidadesPorFase[$fase];
            $rowspan = count($mods) + 1;
            $pintarFase = true;
            ?>

            <?php foreach ($mods as $modalidad): ?>
                <?php $r = $detalle[$fase][$modalidad]; ?>
                <tr>
                    <?php if ($pintarFase): ?>
                        <td rowspan="<?= (int)$rowspan ?>" class="left"><?= h($fase) ?></td>
                        <?php $pintarFase = false; ?>
                    <?php endif; ?>

                    <td class="left"><?= h($modalidad) ?></td>
                    <td class="num"><?= (int)$r['CCFFAA'] ?></td>
                    <td class="num"><?= (int)$r['EP'] ?></td>
                    <td class="num"><?= (int)$r['FAP'] ?></td>
                    <td class="num"><?= (int)$r['MGP'] ?></td>
                    <td class="num"><?= (int)$r['CONIDA'] ?></td>
                    <td class="num"><?= (int)$r['EXPEDIENTES'] ?></td>
                    <td class="num"><?= (int)$r['PROCESOS'] ?></td>
                    <td class="money"><?= n($r['ESTIMADO']) ?></td>
                </tr>
            <?php endforeach; ?>

            <?php $s = $subtotales[$fase]; ?>
            <tr>
                <td class="sub-left">SUB TOTAL</td>
                <td class="sub"><?= (int)$s['CCFFAA'] ?></td>
                <td class="sub"><?= (int)$s['EP'] ?></td>
                <td class="sub"><?= (int)$s['FAP'] ?></td>
                <td class="sub"><?= (int)$s['MGP'] ?></td>
                <td class="sub"><?= (int)$s['CONIDA'] ?></td>
                <td class="sub"><?= (int)$s['EXPEDIENTES'] ?></td>
                <td class="sub"><?= (int)$s['PROCESOS'] ?></td>
                <td class="sub money"><?= n($s['ESTIMADO']) ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="2" class="sub center">TOTAL</td>
            <td class="sub"><?= (int)$totales['CCFFAA'] ?></td>
            <td class="sub"><?= (int)$totales['EP'] ?></td>
            <td class="sub"><?= (int)$totales['FAP'] ?></td>
            <td class="sub"><?= (int)$totales['MGP'] ?></td>
            <td class="sub"><?= (int)$totales['CONIDA'] ?></td>
            <td class="sub"><?= (int)$totales['EXPEDIENTES'] ?></td>
            <td class="sub"><?= (int)$totales['PROCESOS'] ?></td>
            <td class="sub money"><?= n($totales['ESTIMADO']) ?></td>
        </tr>

        <tr>
            <td colspan="2" class="sub-left">VALOR ESTIMADO (SOLES)</td>
            <td class="sub money"><?= n($valorObac['CCFFAA']) ?></td>
            <td class="sub money"><?= n($valorObac['EP']) ?></td>
            <td class="sub money"><?= n($valorObac['FAP']) ?></td>
            <td class="sub money"><?= n($valorObac['MGP']) ?></td>
            <td class="sub money"><?= n($valorObac['CONIDA']) ?></td>
            <td class="sub"></td>
            <td class="sub"></td>
            <td class="sub"></td>
        </tr>
    </table>
</body>
</html>