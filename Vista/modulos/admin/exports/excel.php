<?php
// Vista/modulos/admin/exports/excel.php
declare(strict_types=1);

require_once __DIR__ . '/../../../../Config/config.php';
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../Modelo/MdPacAdmin.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$type = $type ?? ($_GET['type'] ?? 'resumen');
$anio = isset($_GET['anio']) && $_GET['anio'] !== '' ? (int)$_GET['anio'] : (int)date('Y');

if ($type !== 'resumen') {
    $type = 'resumen';
}

$resumen = MdPacAdmin::obtenerResumenSituacion($anio);

$fases              = $resumen['fases_orden'] ?? [];
$detalle            = $resumen['detalle'] ?? [];
$subtotales         = $resumen['subtotales'] ?? [];
$totales            = $resumen['totales'] ?? [];
$valorObac          = $resumen['valor_estimado_obac'] ?? [];
$modalidadesPorFase = $resumen['modalidades_por_fase'] ?? [];

function safeInt($value): int
{
    return (int)($value ?? 0);
}

function safeFloat($value): float
{
    return (float)($value ?? 0);
}

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator('Sistema')
    ->setLastModifiedBy('Sistema')
    ->setTitle('Reporte resumen ' . $anio)
    ->setSubject('Reporte Excel')
    ->setDescription('Reporte de situación de expedientes y procesos de contratación');

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('CUADRO-2026');

/*
|--------------------------------------------------------------------------
| ESTILOS BASE
|--------------------------------------------------------------------------
*/
$colorAzul = 'D9EAF7';
$colorAmarillo = 'ECEBBF';
$colorSub = 'F6F1C7';
$colorBorde = '000000';

$styleTituloGrande = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'name' => 'Arial',
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
];

$styleCabeceraTopLeft = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'name' => 'Arial',
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
];

$styleCabeceraTopRight = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'name' => 'Arial',
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
];

$styleHeader = [
    'font' => [
        'bold' => true,
        'size' => 10,
        'name' => 'Arial',
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorAmarillo],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => $colorBorde],
        ],
    ],
];

$styleCell = [
    'font' => [
        'size' => 10,
        'name' => 'Arial',
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => $colorBorde],
        ],
    ],
];

$styleSubTotal = [
    'font' => [
        'bold' => true,
        'size' => 10,
        'name' => 'Arial',
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => $colorSub],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => $colorBorde],
        ],
    ],
];

$styleLeft = $styleCell;
$styleLeft['alignment']['horizontal'] = Alignment::HORIZONTAL_LEFT;

$styleCenter = $styleCell;
$styleCenter['alignment']['horizontal'] = Alignment::HORIZONTAL_CENTER;

$styleMoney = $styleCell;
$styleMoney['alignment']['horizontal'] = Alignment::HORIZONTAL_RIGHT;

/*
|--------------------------------------------------------------------------
| HOJA 1: RESUMEN
|--------------------------------------------------------------------------
*/
$sheet->setCellValue('A1', 'ACFFAA');
$sheet->mergeCells('A1:D1');
$sheet->getStyle('A1:D1')->applyFromArray($styleCabeceraTopLeft);

$sheet->setCellValue('H1', 'OPP');
$sheet->mergeCells('H1:J1');
$sheet->getStyle('H1:J1')->applyFromArray($styleCabeceraTopRight);

$sheet->setCellValue('A3', 'SITUACIÓN DE LOS EXPEDIENTES Y PROCESOS DE CONTRATACIÓN A CARGO DE LA ACFFAA AF-' . $anio);
$sheet->mergeCells('A3:J3');
$sheet->getStyle('A3:J3')->applyFromArray($styleTituloGrande);
$sheet->getStyle('A3:J3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($colorAmarillo);
$sheet->getStyle('A3:J3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

$sheet->setCellValue('A5', 'FASES');
$sheet->mergeCells('A5:A6');

$sheet->setCellValue('B5', 'MODALIDAD');
$sheet->mergeCells('B5:B6');

$sheet->setCellValue('C5', 'OBAC');
$sheet->mergeCells('C5:G5');

$sheet->setCellValue('H5', 'EXPEDIENTES PAC');
$sheet->mergeCells('H5:H6');

$sheet->setCellValue('I5', 'PROCESOS');
$sheet->mergeCells('I5:I6');

$sheet->setCellValue('J5', 'ESTIMADOS (SOLES)');
$sheet->mergeCells('J5:J6');

$sheet->setCellValue('C6', 'CCFFAA');
$sheet->setCellValue('D6', 'EP');
$sheet->setCellValue('E6', 'FAP');
$sheet->setCellValue('F6', 'MGP');
$sheet->setCellValue('G6', 'CONIDA');

$sheet->getStyle('A5:J6')->applyFromArray($styleHeader);

$row = 7;

foreach ($fases as $fase) {
    $mods = $modalidadesPorFase[$fase] ?? [];
    $rowInicioFase = $row;
    $cantidadFilasFase = count($mods) + 1;

    foreach ($mods as $modalidad) {
        $r = $detalle[$fase][$modalidad] ?? [];

        $sheet->setCellValue('B' . $row, (string)$modalidad);
        $sheet->setCellValue('C' . $row, safeInt($r['CCFFAA'] ?? 0));
        $sheet->setCellValue('D' . $row, safeInt($r['EP'] ?? 0));
        $sheet->setCellValue('E' . $row, safeInt($r['FAP'] ?? 0));
        $sheet->setCellValue('F' . $row, safeInt($r['MGP'] ?? 0));
        $sheet->setCellValue('G' . $row, safeInt($r['CONIDA'] ?? 0));
        $sheet->setCellValue('H' . $row, safeInt($r['EXPEDIENTES'] ?? 0));
        $sheet->setCellValue('I' . $row, safeInt($r['PROCESOS'] ?? 0));
        $sheet->setCellValue('J' . $row, safeFloat($r['ESTIMADO'] ?? 0));

        $sheet->getStyle('B' . $row . ':J' . $row)->applyFromArray($styleCell);
        $sheet->getStyle('B' . $row)->applyFromArray($styleLeft);
        $sheet->getStyle('C' . $row . ':I' . $row)->applyFromArray($styleCenter);
        $sheet->getStyle('J' . $row)->applyFromArray($styleMoney);

        $row++;
    }

    $sheet->setCellValue('A' . $rowInicioFase, (string)$fase);
    $sheet->mergeCells('A' . $rowInicioFase . ':A' . ($rowInicioFase + $cantidadFilasFase - 1));
    $sheet->getStyle('A' . $rowInicioFase . ':A' . ($rowInicioFase + $cantidadFilasFase - 1))->applyFromArray($styleLeft);

    $s = $subtotales[$fase] ?? [];

    $sheet->setCellValue('B' . $row, 'SUB TOTAL');
    $sheet->setCellValue('C' . $row, safeInt($s['CCFFAA'] ?? 0));
    $sheet->setCellValue('D' . $row, safeInt($s['EP'] ?? 0));
    $sheet->setCellValue('E' . $row, safeInt($s['FAP'] ?? 0));
    $sheet->setCellValue('F' . $row, safeInt($s['MGP'] ?? 0));
    $sheet->setCellValue('G' . $row, safeInt($s['CONIDA'] ?? 0));
    $sheet->setCellValue('H' . $row, safeInt($s['EXPEDIENTES'] ?? 0));
    $sheet->setCellValue('I' . $row, safeInt($s['PROCESOS'] ?? 0));
    $sheet->setCellValue('J' . $row, safeFloat($s['ESTIMADO'] ?? 0));

    $sheet->getStyle('B' . $row . ':J' . $row)->applyFromArray($styleSubTotal);
    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $row++;
}

$sheet->setCellValue('A' . $row, 'TOTAL');
$sheet->mergeCells('A' . $row . ':B' . $row);
$sheet->setCellValue('C' . $row, safeInt($totales['CCFFAA'] ?? 0));
$sheet->setCellValue('D' . $row, safeInt($totales['EP'] ?? 0));
$sheet->setCellValue('E' . $row, safeInt($totales['FAP'] ?? 0));
$sheet->setCellValue('F' . $row, safeInt($totales['MGP'] ?? 0));
$sheet->setCellValue('G' . $row, safeInt($totales['CONIDA'] ?? 0));
$sheet->setCellValue('H' . $row, safeInt($totales['EXPEDIENTES'] ?? 0));
$sheet->setCellValue('I' . $row, safeInt($totales['PROCESOS'] ?? 0));
$sheet->setCellValue('J' . $row, safeFloat($totales['ESTIMADO'] ?? 0));

$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($styleSubTotal);
$sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$row++;

$sheet->setCellValue('A' . $row, 'VALOR ESTIMADO (SOLES)');
$sheet->mergeCells('A' . $row . ':B' . $row);
$sheet->setCellValue('C' . $row, safeFloat($valorObac['CCFFAA'] ?? 0));
$sheet->setCellValue('D' . $row, safeFloat($valorObac['EP'] ?? 0));
$sheet->setCellValue('E' . $row, safeFloat($valorObac['FAP'] ?? 0));
$sheet->setCellValue('F' . $row, safeFloat($valorObac['MGP'] ?? 0));
$sheet->setCellValue('G' . $row, safeFloat($valorObac['CONIDA'] ?? 0));
$sheet->setCellValue('H' . $row, '');
$sheet->setCellValue('I' . $row, '');
$sheet->setCellValue('J' . $row, '');

$sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($styleSubTotal);
$sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('C' . $row . ':G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle('C' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

/*
|--------------------------------------------------------------------------
| FORMATOS HOJA 1
|--------------------------------------------------------------------------
*/
$sheet->getStyle('J7:J' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(false);
}

$sheet->getColumnDimension('A')->setWidth(28);
$sheet->getColumnDimension('B')->setWidth(24);
$sheet->getColumnDimension('C')->setWidth(12);
$sheet->getColumnDimension('D')->setWidth(12);
$sheet->getColumnDimension('E')->setWidth(12);
$sheet->getColumnDimension('F')->setWidth(12);
$sheet->getColumnDimension('G')->setWidth(12);
$sheet->getColumnDimension('H')->setWidth(16);
$sheet->getColumnDimension('I')->setWidth(14);
$sheet->getColumnDimension('J')->setWidth(22);

$sheet->getRowDimension(1)->setRowHeight(24);
$sheet->getRowDimension(3)->setRowHeight(24);
$sheet->getRowDimension(5)->setRowHeight(26);
$sheet->getRowDimension(6)->setRowHeight(24);

$sheet->freezePane('C7');

/*
|--------------------------------------------------------------------------
| HOJA 2: TOTALES OBAC
|--------------------------------------------------------------------------
*/
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Totales OBAC');

$sheet2->setCellValue('A1', 'VALOR ESTIMADO POR OBAC - AF-' . $anio);
$sheet2->mergeCells('A1:C1');
$sheet2->getStyle('A1:C1')->applyFromArray($styleTituloGrande);
$sheet2->getStyle('A1:C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($colorAmarillo);
$sheet2->getStyle('A1:C1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

$sheet2->setCellValue('A3', 'OBAC');
$sheet2->setCellValue('B3', 'VALOR ESTIMADO');
$sheet2->setCellValue('C3', 'OBSERVACIÓN');
$sheet2->getStyle('A3:C3')->applyFromArray($styleHeader);

$obacs = ['CCFFAA', 'EP', 'FAP', 'MGP', 'CONIDA'];
$r2 = 4;

foreach ($obacs as $obac) {
    $sheet2->setCellValue('A' . $r2, $obac);
    $sheet2->setCellValue('B' . $r2, safeFloat($valorObac[$obac] ?? 0));
    $sheet2->setCellValue('C' . $r2, 'Monto acumulado por OBAC');

    $sheet2->getStyle('A' . $r2 . ':C' . $r2)->applyFromArray($styleCell);
    $sheet2->getStyle('A' . $r2)->applyFromArray($styleCenter);
    $sheet2->getStyle('B' . $r2)->applyFromArray($styleMoney);
    $sheet2->getStyle('C' . $r2)->applyFromArray($styleLeft);

    $r2++;
}

$sheet2->setCellValue('A' . $r2, 'TOTAL GENERAL');
$sheet2->mergeCells('A' . $r2 . ':B' . $r2);
$sheet2->setCellValue('C' . $r2, array_sum([
    safeFloat($valorObac['CCFFAA'] ?? 0),
    safeFloat($valorObac['EP'] ?? 0),
    safeFloat($valorObac['FAP'] ?? 0),
    safeFloat($valorObac['MGP'] ?? 0),
    safeFloat($valorObac['CONIDA'] ?? 0),
]));

$sheet2->getStyle('A' . $r2 . ':C' . $r2)->applyFromArray($styleSubTotal);
$sheet2->getStyle('C4:C' . $r2)->getNumberFormat()->setFormatCode('#,##0.00');
$sheet2->getColumnDimension('A')->setWidth(18);
$sheet2->getColumnDimension('B')->setWidth(18);
$sheet2->getColumnDimension('C')->setWidth(24);

/*
|--------------------------------------------------------------------------
| HOJA 3: DETALLE PLANO
|--------------------------------------------------------------------------
*/
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('Detalle plano');

$sheet3->setCellValue('A1', 'DETALLE PLANO DE MODALIDADES - AF-' . $anio);
$sheet3->mergeCells('A1:J1');
$sheet3->getStyle('A1:J1')->applyFromArray($styleTituloGrande);
$sheet3->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($colorAmarillo);
$sheet3->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

$headers3 = [
    'A3' => 'FASE',
    'B3' => 'MODALIDAD',
    'C3' => 'CCFFAA',
    'D3' => 'EP',
    'E3' => 'FAP',
    'F3' => 'MGP',
    'G3' => 'CONIDA',
    'H3' => 'EXPEDIENTES',
    'I3' => 'PROCESOS',
    'J3' => 'ESTIMADO',
];

foreach ($headers3 as $cell => $text) {
    $sheet3->setCellValue($cell, $text);
}
$sheet3->getStyle('A3:J3')->applyFromArray($styleHeader);

$r3 = 4;
foreach ($fases as $fase) {
    $mods = $modalidadesPorFase[$fase] ?? [];
    foreach ($mods as $modalidad) {
        $d = $detalle[$fase][$modalidad] ?? [];

        $sheet3->setCellValue('A' . $r3, (string)$fase);
        $sheet3->setCellValue('B' . $r3, (string)$modalidad);
        $sheet3->setCellValue('C' . $r3, safeInt($d['CCFFAA'] ?? 0));
        $sheet3->setCellValue('D' . $r3, safeInt($d['EP'] ?? 0));
        $sheet3->setCellValue('E' . $r3, safeInt($d['FAP'] ?? 0));
        $sheet3->setCellValue('F' . $r3, safeInt($d['MGP'] ?? 0));
        $sheet3->setCellValue('G' . $r3, safeInt($d['CONIDA'] ?? 0));
        $sheet3->setCellValue('H' . $r3, safeInt($d['EXPEDIENTES'] ?? 0));
        $sheet3->setCellValue('I' . $r3, safeInt($d['PROCESOS'] ?? 0));
        $sheet3->setCellValue('J' . $r3, safeFloat($d['ESTIMADO'] ?? 0));

        $sheet3->getStyle('A' . $r3 . ':J' . $r3)->applyFromArray($styleCell);
        $sheet3->getStyle('A' . $r3 . ':B' . $r3)->applyFromArray($styleLeft);
        $sheet3->getStyle('C' . $r3 . ':I' . $r3)->applyFromArray($styleCenter);
        $sheet3->getStyle('J' . $r3)->applyFromArray($styleMoney);

        $r3++;
    }
}

$sheet3->getStyle('J4:J' . max(4, $r3))->getNumberFormat()->setFormatCode('#,##0.00');
$sheet3->freezePane('A4');

$sheet3->getColumnDimension('A')->setWidth(28);
$sheet3->getColumnDimension('B')->setWidth(24);
$sheet3->getColumnDimension('C')->setWidth(10);
$sheet3->getColumnDimension('D')->setWidth(10);
$sheet3->getColumnDimension('E')->setWidth(10);
$sheet3->getColumnDimension('F')->setWidth(10);
$sheet3->getColumnDimension('G')->setWidth(10);
$sheet3->getColumnDimension('H')->setWidth(14);
$sheet3->getColumnDimension('I')->setWidth(12);
$sheet3->getColumnDimension('J')->setWidth(18);

/*
|--------------------------------------------------------------------------
| HOJA ACTIVA Y SALIDA
|--------------------------------------------------------------------------
*/
$spreadsheet->setActiveSheetIndex(0);

$filename = "REPORTE_ESTADO_{$anio}_" . date('d-m') . ".xlsx";

if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;