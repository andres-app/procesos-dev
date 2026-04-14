<?php
// Vista/modulos/admin/exports/RptExcelEstado.php
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
$detallePlano       = $resumen['detalle_plano'] ?? [];

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
| HOJAS DINÁMICAS POR FASE
|--------------------------------------------------------------------------
*/
foreach ($fases as $fase) {
    if (empty($detallePlano[$fase]) || !is_array($detallePlano[$fase])) {
        continue;
    }

    $nombreHoja = mb_substr($fase, 0, 31, 'UTF-8');
    $sheetFase = $spreadsheet->createSheet();
    $sheetFase->setTitle($nombreHoja);

    $rowF = 1;

    foreach (['Corporativo', 'Individual'] as $tipo) {
        if (empty($detallePlano[$fase][$tipo]) || !is_array($detallePlano[$fase][$tipo])) {
            continue;
        }

        $tipoTitulo = $tipo === 'Corporativo' ? 'CORPORATIVOS' : 'INDIVIDUALES';
        $tituloTipo = 'PROCESOS ' . $tipoTitulo . ' ' . mb_strtoupper($fase, 'UTF-8') . ' AF-' . $anio;

        $sheetFase->setCellValue('A' . $rowF, $tituloTipo);
        $sheetFase->mergeCells("A{$rowF}:K{$rowF}");
        $sheetFase->getStyle("A{$rowF}:K{$rowF}")->applyFromArray($styleTituloGrande);
        $sheetFase->getStyle("A{$rowF}:K{$rowF}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($colorAmarillo);
        $sheetFase->getStyle("A{$rowF}:K{$rowF}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $rowF++;

        $headers = [
            'A' => 'N° PROC',
            'B' => 'EXP. PAC',
            'C' => 'OBAC',
            'D' => 'HISTORIAL',
            'E' => 'DESCRIPCION',
            'F' => 'FF',
            'G' => 'TP',
            'H' => 'ESTIMADO SOLES',
            'I' => 'FPC',
            'J' => 'ESTADO',
            'K' => 'SITUACION',
        ];

        foreach ($headers as $col => $text) {
            $sheetFase->setCellValue($col . $rowF, $text);
        }

        $sheetFase->getStyle("A{$rowF}:K{$rowF}")->applyFromArray($styleHeader);
        $sheetFase->getRowDimension($rowF)->setRowHeight(28);
        $rowF++;

        $n = 1;

        foreach ($detallePlano[$fase][$tipo] as $item) {
            $sheetFase->setCellValue('A' . $rowF, $n++);
            $sheetFase->setCellValue('B' . $rowF, (string)($item['nopac'] ?? ''));
            $sheetFase->setCellValue('C' . $rowF, (string)($item['obac'] ?? ''));
            $sheetFase->setCellValue('D' . $rowF, (string)($item['historial'] ?? ''));
            $sheetFase->setCellValue('E' . $rowF, (string)($item['descripcion'] ?? ''));
            $sheetFase->setCellValue('F' . $rowF, (string)($item['ff'] ?? ''));
            $sheetFase->setCellValue('G' . $rowF, (string)($item['tp'] ?? ''));
            $sheetFase->setCellValue('H' . $rowF, safeFloat($item['estimado'] ?? 0));
            $sheetFase->setCellValue('I' . $rowF, (string)($item['fpc'] ?? ''));
            $sheetFase->setCellValue('J' . $rowF, (string)($item['estado'] ?? ''));
            $sheetFase->setCellValue('K' . $rowF, (string)($item['situacion'] ?? ''));

            $sheetFase->getStyle("A{$rowF}:K{$rowF}")->applyFromArray($styleCell);

            $sheetFase->getStyle("A{$rowF}:C{$rowF}")->applyFromArray($styleCenter);
            $sheetFase->getStyle("D{$rowF}:E{$rowF}")->applyFromArray($styleLeft);
            $sheetFase->getStyle("F{$rowF}:G{$rowF}")->applyFromArray($styleCenter);
            $sheetFase->getStyle("H{$rowF}")->applyFromArray($styleMoney);
            $sheetFase->getStyle("I{$rowF}:J{$rowF}")->applyFromArray($styleCenter);
            $sheetFase->getStyle("K{$rowF}")->applyFromArray($styleLeft);

            $rowF++;
        }

        $sheetFase->getStyle('H1:H' . max(1, $rowF))->getNumberFormat()->setFormatCode('#,##0.00');

        $rowF += 2;
    }

    $sheetFase->getColumnDimension('A')->setWidth(8);
    $sheetFase->getColumnDimension('B')->setWidth(10);
    $sheetFase->getColumnDimension('C')->setWidth(8);
    $sheetFase->getColumnDimension('D')->setWidth(38);
    $sheetFase->getColumnDimension('E')->setWidth(38);
    $sheetFase->getColumnDimension('F')->setWidth(8);
    $sheetFase->getColumnDimension('G')->setWidth(8);
    $sheetFase->getColumnDimension('H')->setWidth(16);
    $sheetFase->getColumnDimension('I')->setWidth(8);
    $sheetFase->getColumnDimension('J')->setWidth(14);
    $sheetFase->getColumnDimension('K')->setWidth(36);

}

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
