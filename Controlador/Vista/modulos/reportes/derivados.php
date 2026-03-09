<?php
$titulo = 'Derivados | Reportes';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

/* 1) funciones + data + totales (rows, totalEstimado, totalAdj, etc.) */

/* 2) EXPORT ANTES DE CUALQUIER HTML */
if (isset($_GET['export']) && $_GET['export'] === 'xls') {
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="reporte_derivados_af_2026.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "\xEF\xBB\xBF";

    $esc = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

    echo '<html><head><meta charset="UTF-8"><style>
      table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:10pt;}
      th,td{border:1px solid #999;padding:6px;vertical-align:top;}
      th{background:#f2f2f2;font-weight:bold;text-align:center;}
      .num{mso-number-format:"\\#\\,\\#\\#0\\.00"; text-align:right;}
      .txt{mso-number-format:"\\@";}
      .wrap{white-space:normal;}
    </style></head><body>';

    echo '<table><tr>
      <th>N°</th><th>EXP. PAC</th><th>OBAC</th><th>HISTORIAL</th><th>DESCRIPCIÓN</th>
      <th>FF</th><th>TP</th><th>ESTIMADO</th><th>ADJUDICADO</th><th>FPC</th><th>ESTADO</th><th>SITUACIÓN</th>
    </tr>';

    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td class="txt">' . $esc((int)$r['n']) . '</td>';
        echo '<td class="txt">' . $esc($r['exp']) . '</td>';
        echo '<td class="txt">' . $esc(strtoupper(trim($r['obac']))) . '</td>';
        echo '<td class="wrap">' . $esc((string)$r['hist']) . '</td>';
        echo '<td class="wrap">' . $esc((string)$r['desc']) . '</td>';
        echo '<td class="txt">' . $esc((string)$r['ff']) . '</td>';
        echo '<td class="txt">' . $esc((string)$r['tp']) . '</td>';
        echo '<td class="num">' . number_format((float)$r['est'], 2, '.', '') . '</td>';
        echo '<td class="num">' . number_format((float)$r['adj'], 2, '.', '') . '</td>';
        echo '<td class="txt">' . $esc((string)$r['fpc']) . '</td>';
        echo '<td class="txt">' . $esc((string)$r['estado']) . '</td>';
        echo '<td class="wrap">' . $esc((string)$r['sit']) . '</td>';
        echo '</tr>';
    }

    echo '<tr>
      <td colspan="7" style="font-weight:bold;text-align:right;">TOTAL</td>
      <td class="num" style="font-weight:bold;">' . number_format((float)$totalEstimado, 2, '.', '') . '</td>
      <td class="num" style="font-weight:bold;">' . number_format((float)$totalAdj, 2, '.', '') . '</td>
      <td colspan="3"></td>
    </tr>';

    echo '</table></body></html>';
    exit;
}

/* si necesitas csv, igual va aquí ANTES del header.php */

/* 3) recién aquí incluyes el layout */
require_once __DIR__ . '/../../layout/header.php';

function parseDateAny($s)
{
    // soporta: 30/01/26, 30/01/2026, 06-02-2026, 06-02-26
    if (!preg_match('/\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2}|\d{4})\b/', $s, $m)) return null;
    $d = (int)$m[1];
    $mo = (int)$m[2];
    $y = (int)$m[3];
    if ($y < 100) $y += 2000; // 2 dígitos -> 20xx
    if (!checkdate($mo, $d, $y)) return null;
    return sprintf('%04d-%02d-%02d', $y, $mo, $d);
}

function buildTimelineEvents($hist, $sit = '')
{
    $events = [];
    $pushLine = function ($line, $type = 'hist') use (&$events) {
        $line = trim($line);
        if ($line === '') return;

        $iso = parseDateAny($line);
        $events[] = [
            'date' => $iso,                // YYYY-MM-DD o null
            'label' => $line,              // texto completo
            'type' => $type,               // hist|sit
            'ts' => $iso ? strtotime($iso) : null,
        ];
    };

    // Divide por líneas y también por puntos cuando vienen en 1 sola línea
    $splitSmart = function ($text) {
        $text = str_replace(["\r\n", "\r"], "\n", (string)$text);
        $parts = preg_split('/\n+/', $text);
        $out = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') continue;
            // Si viene "AAA. BBB. CCC." lo separa en items
            $chunks = preg_split('/\.\s+(?=[A-ZÁÉÍÓÚÑ]|INVERSIÓN|PUBLICACIÓN|REGISTRO|SOLIC|RECEP|OBS|REITE|SUBSANA|EI|EC|CONV|ADJUD|BP|CONSENTIMIENTO)/u', $p);
            foreach ($chunks as $c) {
                $c = trim($c);
                if ($c === '') continue;
                // reponer punto si se perdió
                $out[] = rtrim($c, '.') . '.';
            }
        }
        return $out;
    };

    foreach ($splitSmart($sit) as $line) $pushLine($line, 'sit');
    foreach ($splitSmart($hist) as $line) $pushLine($line, 'hist');

    // Ordenar por fecha cuando exista; si no, queda al final manteniendo inserción
    usort($events, function ($a, $b) {
        if ($a['ts'] && $b['ts']) return $a['ts'] <=> $b['ts'];
        if ($a['ts'] && !$b['ts']) return -1;
        if (!$a['ts'] && $b['ts']) return 1;
        return 0;
    });

    // Quitar duplicados exactos (opcional)
    $seen = [];
    $clean = [];
    foreach ($events as $e) {
        $k = $e['type'] . '|' . $e['date'] . '|' . $e['label'];
        if (isset($seen[$k])) continue;
        $seen[$k] = true;
        $clean[] = $e;
    }

    return $clean;
}

function fmtIsoToShort($iso)
{
    if (!$iso) return '';
    // YYYY-MM-DD -> DD/MM/YYYY
    [$y, $m, $d] = explode('-', $iso);
    return $d . '/' . $m . '/' . $y;
}

function money($n)
{
    return 'S/ ' . number_format((float)$n, 2, '.', ',');
}
function badgeObac($obac)
{
    return strtoupper(trim($obac));
}
function statusClass($estado)
{
    $e = strtoupper(trim($estado));
    switch ($e) {
        case 'ADJUDICADO':
            return 'st-ok';
        case 'CONVOCADO':
            return 'st-warn';
        case 'DESIERTO':
            return 'st-muted';
        default:
            return 'st-muted';
    }
}

/* =========================
   DATA
   ========================= */
$rows = [
    [
        'n' => 1,
        'exp' => '10',
        'obac' => 'EP',
        'hist' => "ACTA 01-2025 EP DEL 22/01/25.\nPUBLICACIÓN: RCGE 031 VERSIÓN 01 DEL 22/01/25.\nREGISTRO SIGCO: MM-000004-2025-DJ DEL 23/01/25.\nSOLIC 16/09/25, RECEP 25/09/25.\nOBS 30/09/25, REITE (06), SUBSANA (05) 07/11/25.\nEI APROBADO: 07/11/25, EC APROBADO: 19/12/25.\nCONV: 19/12/25, ADJUD: 30/01/26.",
        'desc' => 'SEGURO DE AVIACIÓN AF-2026/2027 PP-135',
        'ff' => 'RO',
        'tp' => 'CPS-06-2025',
        'est' => 21845640.00,
        'adj' => 6105723.19,
        'fpc' => 'NOV',
        'estado' => 'ADJUDICADO',
        'sit' => 'ADJUDICADO 30/01/26 A POSTOR GANADOR: RIMAC SEGUROS Y REASEGUROS'
    ],
    [
        'n' => 1,
        'exp' => '335',
        'obac' => 'MGP',
        'hist' => "ACTA 44-2025 MGP DEL 30/10/25.\nPUBLICACIÓN: DEL 31/10/25.\nREGISTRO SIGCO: DEL 03/11/25.\nSOLIC 05/11/25, RECEP 06/11/25.\nOBS 06/11/25, SUBSANA (01) 07/11/25.\nEI APROBADO: 10/11/25, EC APROBADO: 19/12/25.\nCONV: 19/12/25, ADJUD: 30/01/26.",
        'desc' => 'SEGURO DE AVIACIÓN AF-2026/2027 PP-135',
        'ff' => 'RO',
        'tp' => 'CPS-06-2025',
        'est' => 13686211.25,
        'adj' => 2891688.47,
        'fpc' => 'NOV',
        'estado' => 'ADJUDICADO',
        'sit' => 'ADJUDICADO 30/01/26 A POSTOR GANADOR: RIMAC SEGUROS Y REASEGUROS'
    ],
    [
        'n' => 1,
        'exp' => '275',
        'obac' => 'FAP',
        'hist' => "PUBLICACIÓN: DEL 17/10/25.\nACTA 41-2025 FAP DEL 17/10/25.\nREGISTRO SIGCO: DEL 20/10/25.\nSOLIC 20/10/25, RECEP 20/10/25.\nOBS 23/10/25, REITE (01).\nEI APROBADO: 10/11/25, EC APROBADO: 19/12/25.\nCONV: 19/12/25, ADJUD: 30/01/26.",
        'desc' => 'SEGURO DE AVIACIÓN AF-2026/2027 PP-135',
        'ff' => 'RO',
        'tp' => 'CPS-06-2025',
        'est' => 25835317.01,
        'adj' => 10530728.04,
        'fpc' => 'NOV',
        'estado' => 'ADJUDICADO',
        'sit' => 'ADJUDICADO 30/01/26 A POSTOR GANADOR: RIMAC SEGUROS Y REASEGUROS'
    ],
    [
        'n' => 2,
        'exp' => '333',
        'obac' => 'MGP',
        'hist' => "INVERSIÓN: CUI 2630221.\nACTA 39-2025 MGP DEL 06/10/25.\nPUBLICACIÓN: RD 1019-2025-DIRECOMAR DEL 09/10/25.\nREGISTRO SIGCO: DEL 13/10/25.\nSOLIC 14/10/25, RECEP 20/10/25.\nOBS 21/10/25, REITE (01), SUBSANA (02) 11/11/25.\nEI APROBADO: 11/11/25, EC APROBADO: 19/12/25.\nCONV: 23/12/25, BP: 24/02/26.",
        'desc' => 'ELABORACIÓN DE EXPEDIENTE TÉCNICO DEL PI RECUPERACIÓN DE CONTROL, VIGILANCIA Y DEFENSA TERRESTRE, AÉREO Y MARITIMO, FLUVIAL, LACUSTRE DEL TERRITORIO NACIONAL EN AERÓDROMO DE LA BASE NAVAL SAN JUNA DE MARCONA CUI. 2630221 /CONSULTORIA OBRA PP 0135',
        'ff' => 'RO',
        'tp' => 'CPC-07-2025',
        'est' => 903068.34,
        'adj' => 0.00,
        'fpc' => 'OCT',
        'estado' => 'CONVOCADO',
        'sit' => 'SE POSTERGA ABSOLUCIÓN DE CONSULTAS HASTA EL 06-02-2026 POR NECESIDAD DEL COMITÉ. BUENA PRO 24-02-2026'
    ],
    [
        'n' => 3,
        'exp' => '338',
        'obac' => 'MGP',
        'hist' => "ACTA 48-2025 MGP DEL 14/11/25.\nPUBLICACIÓN: RD 1213-2025-DIRECOMAR DEL 19/11/25.\nREGISTRO SIGCO: DEL 20/11/25.\nSOLIC 20/11/25, RECEP 25/11/25.\nOBS 02/12/25, REITE (01), SUBSANA (02) 04/12/25.\nEI APROBADO: 05/12/25, EC APROBADO: 19/12/25.\nCONV: 31/12/25, BP: 24/02/26.",
        'desc' => 'SUPERVISIÓN DE LA EJECUCIÓN DE LA OBRA 03 PARQUEADEROS DEL PROYECTO DE INVERSIÓN AMPLIACIÓN Y MEJORAMIENTO DE LOS SERVICIOS DEL ASTILLERO DEL ARSENAL DE LA MARINA DE GUERRA DEL PERÚ EN LA BASE NAVAL DEL CALLAO_ 0018-2025-DIRPRONAV /CONSULTORÍA DE OBRA PP 0135',
        'ff' => 'RO',
        'tp' => 'CPC-08-2025',
        'est' => 2266776.72,
        'adj' => 0.00,
        'fpc' => 'NOV',
        'estado' => 'CONVOCADO',
        'sit' => ''
    ],
    [
        'n' => 4,
        'exp' => '274',
        'obac' => 'FAP',
        'hist' => "INVERSIÓN: CUI 2652526.\nACTA 38-2025 FAP DEL 26/09/25.\nREGISTRO SIGCO: DEL 29/09/25.\nSOLIC 29/09/25, RECEP 06/10/25.\nOBS 13/10/25, REITE (01), SUBSANA (02) 21/10/25.\nPUBLICACIÓN: RD-0530-DIGLO DEL 29/09/25.\nEI APROBADO: 21/10/25, EC APROBADO: 04/12/25.\nCONV: 05/12/25, ADJUD: 04/02/26.",
        'desc' => 'ADQUISICION DE SISTEMA DE TECNOLOGIA, INFORMACIÓN Y COMUNICACIÓN EN EL SERVICIO DE INFORMATICA, MEDIANTE LA REPOSICIÓN DE EQUIPOS DE CIBERSEGURIDAD, DE CONECTIVIDAD DE RED DE DATOS A NIVEL DATA CENTER Y DE ALMACENAMIENTO',
        'ff' => 'RO',
        'tp' => 'LP-07-2025',
        'est' => 5135151.00,
        'adj' => 5129990.00,
        'fpc' => 'NOV',
        'estado' => 'ADJUDICADO',
        'sit' => 'CONVOCADO 05/12/2025, BUENA PRO 04-02-2026, CONSENTIMIENTO 16-02-26'
    ],
    [
        'n' => 5,
        'exp' => '331',
        'obac' => 'MGP',
        'hist' => "INVERSIÓN: CUI 2188537.\nACTA 37-2025 MGP DEL 24/09/25.\nPUBLICACIÓN: RD 974-2025-DIRECOMAR DEL 27/09/25.\nREGISTRO SIGCO: DEL 02/10/25.\nSOLIC 07/10/25, RECEP 10/10/25.\nOBS 17/10/25, REITE (01), SUBSANA (02) 28/10/25.\nEI APROBADO: 28/10/25, EC APROBADO: 10/12/25.\nCONV: 16/12/25, ADJUD: 18/02/26.",
        'desc' => 'EJECUCIÓN DE LA OBRA DE CONSTRUCCIÓN DE 03 PARQUEADEROS DE LA FASE 1C DEL PROYECTO DE INVERSIÓN: AMPLIACIÓN Y MEJORAMIENTO DE LOS SERVICIOS DEL ASTILLERO DEL ARSENAL NAVAL DE LA MARINA DE GUERRA DEL PERÚ EN LA BASE NAVAL DEL CALLAO CUI. 2188537 /OBRAS PP 0135',
        'ff' => 'ROOC',
        'tp' => 'LP-08-2025',
        'est' => 41214122.11,
        'adj' => 0.00,
        'fpc' => 'SEP',
        'estado' => 'ADJUDICADO',
        'sit' => 'SE POSTERGA ABSOLUCIÓN DE CONSULTAS HASTA EL 30/01/2026. SUSPENDIDO. 05-02-26 SE REGISTRA ELEVACIÓN'
    ],
    [
        'n' => 6,
        'exp' => '422',
        'obac' => 'EP',
        'hist' => "ACTA 43-2025 EP DEL 30/10/25.\nPUBLICACIÓN: DEL 03/11/25.\nREGISTRO SIGCO: DEL 03/11/25.\nSOLIC 04/11/25, REITE (01), RECEP 19/11/25.\nOBS 19/11/25, SUBSANA (01) 20/11/25.\nEI APROBADO: 21/11/25, EC APROBADO: 10/12/25.\nCONV: 12/12/25, ADJUD: 06/02/26.",
        'desc' => 'ADQUISICION DE VEHICULOS DE FLOTA LIVIANA AF-2026',
        'ff' => 'RO',
        'tp' => 'LP-09-2025',
        'est' => 18213000.00,
        'adj' => 0.00,
        'fpc' => 'NOV',
        'estado' => 'ADJUDICADO',
        'sit' => 'CONVOCADO 12/12/2025, BUENA PRO POSTERGADA PARA EL 06-02-2026'
    ],
    [
        'n' => 7,
        'exp' => '332',
        'obac' => 'MGP',
        'hist' => "ACTA 37-2025 MGP DEL 24/09/25.\nPUBLICACIÓN: DEL 27/09/25.\nREGISTRO SIGCO: DEL 02/10/25.\nSOLIC 02/10/25, RECEP 10/10/25.\nOBS 22/10/25, SUBSANA (01) 28/10/25.\nEI APROBADO: 30/10/25, EC APROBADO: 16/12/25.\nCONV: 17/12/25, ADJUD: 11/02/26.",
        'desc' => 'ADQUISICIÓN PARA LA OPTIMIZACIÓN Y REGULACIÓN DE LA CAPACIDAD DE ADMINISTRACIÓN DE DATA CENTER INSTITUCIONAL /BIEN PP 0135',
        'ff' => 'RO',
        'tp' => 'LP-10-2025',
        'est' => 16320849.47,
        'adj' => 0.00,
        'fpc' => 'OCT',
        'estado' => 'ADJUDICADO',
        'sit' => ''
    ],
    [
        'n' => 8,
        'exp' => '334',
        'obac' => 'MGP',
        'hist' => "ACTA 44-2025 MGP DEL 30/10/25.\nPUBLICACIÓN: DEL 31/10/25.\nREGISTRO SIGCO: DEL 03/11/25.\nSOLIC 04/11/25, RECEP 05/11/25.\nOBS 10/11/25, REITE (01), SUBSANA (02) 14/11/25.\nEI APROBADO: 14/11/25, EC APROBADO: 16/12/25.\nCONV: 19/12/25, ADJUD: 06/02/26.",
        'desc' => 'ADQUISICIÓN DE VEHÍCULOS DE FLOTA PESADA PARA LA INSTITUCIÓN /BIEN PP 0135',
        'ff' => 'RO',
        'tp' => 'LP-12-2025',
        'est' => 10091022.36,
        'adj' => 0.00,
        'fpc' => 'NOV',
        'estado' => 'ADJUDICADO',
        'sit' => 'CONVOCADO 19/12/2025, BUENA PRO 06-02-2026'
    ],
    [
        'n' => 9,
        'exp' => '268',
        'obac' => 'FAP',
        'hist' => "ACTA 23-2025 FAP DEL 20/06/25.\nPUBLICACIÓN: DEL 23/06/25.\nREGISTRO SIGCO: DEL 25/06/25.\nSOLIC 25/06/25, RECEP 30/06/25.\nOBS 08/07/25, REITE (01), SUBSANA (02) 04/08/25.\nEI APROBADO: 05/08/25, EC APROBADO: 01/09/25.\nCONV: 19/12/25, ADJUD: 03/02/26.",
        'desc' => 'ADQUISICIÓN DE 2 CAMIONETAS PARA LAS ALAS AÉREAS DE PROVINCIA (ALAR1 ALAR 3 ALAR4 ALAR5) PP-0135 (ITEM 2)',
        'ff' => 'RO',
        'tp' => 'LPA-06-2025',
        'est' => 356800.00,
        'adj' => 0.00,
        'fpc' => 'JUL',
        'estado' => 'ADJUDICADO',
        'sit' => 'CONVOCATORIA 19/12/2026, BUENA PRO 03/02/2026.'
    ],
    [
        'n' => 10,
        'exp' => '210',
        'obac' => 'FAP',
        'hist' => "ACTA 13-2025 FAP DEL 04/04/25.\nPUBLICACIÓN: RCGFA-0294 VERSIÓN 06 DEL 07/04/25.\nREGISTRO SIGCO: DEL 08/04/25.\nSOLIC 08/04/25, RECEP 15/04/25.\nOBS 25/04/25, SUBSANA (01) 08/05/25.\nEI APROBADO: 08/05/25, EC APROBADO: 14/12/25.\nCONV: 16/12/25, ADJUD: 23/01/26.",
        'desc' => 'SERVICIO DE OVERHAUL DE COMPONENTES APLICABLE A LOS HELICOPTEROS MI-17/171SH PP-0135',
        'ff' => 'RO',
        'tp' => 'RES-19-2025',
        'est' => 2720415.60,
        'adj' => 2684320.00,
        'fpc' => 'ABR',
        'estado' => 'ADJUDICADO',
        'sit' => 'ADJUDICADO 23/01/2026, POSTOR GANADOR: CONSORCIO MOTOR SICH SA Y COMPAÑIA VIP AVIA CORPORATION'
    ],
    [
        'n' => 11,
        'exp' => '420',
        'obac' => 'EP',
        'hist' => "ACTA 42-2025 EP DEL 21/10/25.\nPUBLICACIÓN: RD-0187-COLOGE DEL 23/10/25.\nREGISTRO SIGCO: DEL 27/10/25.\nSOLIC 28/10/25, REITE (01), RECEP 12/11/25.\nOBS 14/11/25, REITE (01), SUBSANA (01) 12/12/25.\nEI APROBADO: 12/12/25, EC APROBADO: 22/12/25.\nCONV: 30/12/25, ADJUD: 06/02/26.",
        'desc' => 'ADQUISICIÓN DE EQUIPOS CONTRA INCENDIOS HELIBALDE PARA EL BAT NO 821-AE - AF 2026',
        'ff' => 'RO',
        'tp' => 'RES-36-2025',
        'est' => 9031433.08,
        'adj' => 0.00,
        'fpc' => 'OCT',
        'estado' => 'ADJUDICADO',
        'sit' => 'CONVOCADO 30/12/2025, BUENA PRO 06/02/2026'
    ],
    [
        'n' => 12,
        'exp' => '325',
        'obac' => 'MGP',
        'hist' => "ACTA 26-2025 MGP DEL 09/07/25.\nPUBLICACIÓN: RD 772-2025-MGP VERSIÓN 1 NO PROGRAMADOS DEL 15/07/25.\nREGISTRO SIGCO: DEL 16/07/25.\nSOLIC 16/07/25, REITE (01), RECEP 24/07/25.\nOBS 01/08/25, SUBSANA (01) 05/08/25.\nEI APROBADO: 07/08/25, EC APROBADO: 25/11/25.\nDESIERTO: 07/01/26.",
        'desc' => 'SERVICIO DE MANTENIMIENTO Y OVERHAUL DE TRES (3) MOTORES DEUTZ BF6M1015CP / 0009-2025-COMCONAVRAEM /SERVICIO PP 0032',
        'ff' => 'RO',
        'tp' => 'CPS-05-2025',
        'est' => 700000.00,
        'adj' => 0.00,
        'fpc' => 'AGO',
        'estado' => 'DESIERTO',
        'sit' => 'DECLARADO DESIERTO 07/01/2026 AL NO HABER NINGUNA OFERTA.'
    ],
    [
        'n' => 13,
        'exp' => '408',
        'obac' => 'EP',
        'hist' => "ACTA 19-2025 EP DEL 04/06/25.\nPUBLICACIÓN: RD N°084 COLOGE DEL 05/06/25.\nREGISTRO SIGCO: DEL 11/06/25.\nSOLIC 11/06/25, REITE (04), RECEP 08/08/25.\nOBS 14/08/25, REITE (02), SUBSANA (02) 10/09/25.\nEI APROBADO: 12/09/25, EC APROBADO: 22/10/25.\nDESIERTO: 02/12/25.",
        'desc' => 'ADQUISICIÓN DE ACCESORIO Y MATERIALES PARA LOS COMBATIENTES DEL COOEE CE-VRAEM',
        'ff' => 'RO',
        'tp' => 'RES-28-2025',
        'est' => 1370460.00,
        'adj' => 0.00,
        'fpc' => 'JUL',
        'estado' => 'DESIERTO',
        'sit' => "SE DECLARA DESIERTO 02/12/2025. OFICIO N°000451-2025 DJ-ACFFAA (29/12/2025) SOLICITA INDICAR SITUACIÓN."
    ],
];

/* =========================
   TOTALES
   ========================= */
$totalEstimado = 0;
$totalAdj = 0;
foreach ($rows as $r) {
    $totalEstimado += (float)$r['est'];
    $totalAdj += (float)$r['adj'];
}
$totalProc = count($rows);

$cnt = ['EP' => 0, 'FAP' => 0, 'MGP' => 0];
foreach ($rows as $r) {
    $k = strtoupper(trim($r['obac']));
    if (isset($cnt[$k])) $cnt[$k]++;
}


/* =========================
   EXPORT: PRINT (PDF)
   URL: ?export=print
   ========================= */
$isPrint = (isset($_GET['export']) && $_GET['export'] === 'print');
?>

<main class="page page-shell flex-1 px-5 pt-4 overflow-y-auto">

    <section class="mb-4">
        <div class="headbox">
            <a class="back noprint" href="<?= BASE_URL ?>/reportes" aria-label="Volver">‹</a>

            <div class="min-w-0">
                <p class="topline">AGENCIA • OFICINA DE PLANEAMIENTO Y PRESUPUESTO</p>
                <h2 class="title">Contrataciones derivadas • AF-2025</h2>
            </div>

            <div class="actions noprint">
                <a class="icon-btn icon-btn--excel" href="?export=xls" title="Exportar a Excel" aria-label="Excel">
                    <!-- Excel SVG -->
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 4a2 2 0 0 1 2-2h9l5 5v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4zm10 0v4h4" />
                        <path d="M8.2 10h1.6l1 1.8L11.8 10h1.6l-1.9 3 2 3h-1.6l-1.1-1.9L9.6 16H8l2-3-1.8-3z" />
                    </svg>
                </a>

                <a class="icon-btn icon-btn--pdf" href="?export=print" title="Exportar a PDF" aria-label="PDF">
                    <!-- PDF SVG -->
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm8 1v5h5" />
                        <path d="M8 15h2a2 2 0 1 0 0-4H8v4zm6 0h2a2 2 0 1 0 0-4h-2v4z" />
                    </svg>
                </a>
            </div>

            <div class="pill">AF-2026</div>
        </div>

        <div class="toolbar noprint">
            <div class="search">
                <span class="s-ico" aria-hidden="true">⌕</span>
                <input id="q" type="search" placeholder="Buscar: exp., OBAC, descripción, estado…" autocomplete="off">
                <button class="s-clear" type="button" id="qClear" aria-label="Limpiar búsqueda">×</button>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="kpi">
            <p class="kpi-l">Procesos</p>
            <p class="kpi-v"><?= (int)$totalProc ?></p>
        </div>
        <div class="kpi">
            <p class="kpi-l">Estimado</p>
            <p class="kpi-v"><?= money($totalEstimado) ?></p>
        </div>
        <div class="kpi">
            <p class="kpi-l">Adjudicado</p>
            <p class="kpi-v"><?= money($totalAdj) ?></p>
        </div>
        <div class="kpi">
            <p class="kpi-l">OBAC (EP/FAP/MGP)</p>
            <p class="kpi-v"><?= (int)$cnt['EP'] ?>/<?= (int)$cnt['FAP'] ?>/<?= (int)$cnt['MGP'] ?></p>
        </div>
    </section>

    <!-- =========================
     VISTA MÓVIL (CARDS) ÚNICA
     ========================= -->
    <section class="mb-6">
        <div class="cards" id="cards">
            <?php foreach ($rows as $r): ?>
                <?php
                $haystack = strtoupper(trim(
                    $r['exp'] . ' ' . $r['obac'] . ' ' . $r['desc'] . ' ' . $r['estado'] . ' ' . $r['tp'] . ' ' . $r['ff'] . ' ' . $r['fpc'] . ' ' . $r['hist'] . ' ' . $r['sit']
                ));
                ?>
                <article class="card card-v2" data-hay="<?= htmlspecialchars($haystack) ?>">

                    <!-- HEADER SIMPLE -->
                    <div class="card-head">
                        <div class="card-head-left">
                            <span class="obac"><?= htmlspecialchars(badgeObac($r['obac'])) ?></span>
                            <span class="tp"><?= htmlspecialchars($r['tp']) ?></span>
                        </div>

                        <span class="st <?= statusClass($r['estado']) ?>">
                            <?= htmlspecialchars($r['estado']) ?>
                        </span>
                    </div>

                    <!-- TÍTULO -->
                    <h3 class="card-title">
                        <?= htmlspecialchars($r['desc']) ?>
                    </h3>

                    <!-- META INFO COMPACTA -->
                    <div class="meta-line">
                        <span>Exp: <?= htmlspecialchars($r['exp']) ?></span>
                        <span>FF: <?= htmlspecialchars($r['ff']) ?></span>
                        <span>FPC: <?= htmlspecialchars($r['fpc']) ?></span>
                    </div>

                    <!-- BLOQUE FINANCIERO DESTACADO -->
                    <div class="finance">
                        <div>
                            <small>Estimado</small>
                            <strong><?= money($r['est']) ?></strong>
                        </div>
                        <div>
                            <small>Adjudicado</small>
                            <strong><?= money($r['adj']) ?></strong>
                        </div>
                    </div>

                    <!-- SITUACIÓN -->
                    <?php if (!empty(trim((string)$r['sit']))): ?>
                        <details class="timeline timeline--sit">
                            <summary>Ver situación</summary>
                            <div class="timeline-body">
                                <?= nl2br(htmlspecialchars($r['sit'])) ?>
                            </div>
                        </details>
                    <?php endif; ?>

                    <!-- HISTORIAL -->
                    <?php if (!empty(trim((string)$r['hist']))): ?>
                        <details class="timeline timeline--hist">
                            <summary>Ver cronología</summary>
                            <div class="timeline-body mono">
                                <?= nl2br(htmlspecialchars($r['hist'])) ?>
                            </div>
                        </details>
                    <?php endif; ?>

                    <?php $events = buildTimelineEvents($r['hist'] ?? '', $r['sit'] ?? ''); ?>

                    <?php if (!empty($events)): ?>
                        <details class="tlx">
                            <summary class="tlx-sum">
                                <span class="tlx-title">Línea de tiempo</span>
                                <span class="tlx-meta"><?= count($events) ?> eventos</span>
                                <span class="tlx-chev" aria-hidden="true"></span>
                            </summary>

                            <div class="tlx-body">
                                <ol class="timeline2-list">
                                    <?php foreach ($events as $e): ?>
                                        <li class="timeline2-item <?= $e['type'] === 'sit' ? 'is-sit' : 'is-hist' ?>">
                                            <div class="timeline2-dot" aria-hidden="true"></div>

                                            <div class="timeline2-content">
                                                <?php if (!empty($e['date'])): ?>
                                                    <div class="timeline2-date"><?= htmlspecialchars(fmtIsoToShort($e['date'])) ?></div>
                                                <?php endif; ?>

                                                <div class="timeline2-text">
                                                    <?= htmlspecialchars($e['label']) ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                        </details>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>

            <div id="emptyCards" style="display:none;padding:12px;text-align:center;color:#64748b;font-weight:800;">
                Sin resultados.
            </div>
        </div>
    </section>

    <!-- RESUMEN (COMPLETO COMO TU EXCEL) -->
    <section class="tbl-card mb-10">
        <div class="tbl-top">
            <p class="text-sm font-black text-slate-900">Resumen</p>
            <p class="text-xs text-slate-500 font-bold">Contrataciones derivadas AF-2026</p>
        </div>

        <div class="res-wrap">
            <!-- Título superior -->
            <div class="res-title">
                CONTRATACIONES DERIVADAS BAJO ÁMBITO DE LA ACFFAA AF-2026
            </div>

            <!-- Cabecera -->
            <div class="res-grid">
                <div class="res-cell res-h">FASES</div>
                <div class="res-cell res-h">MODALIDAD</div>

                <div class="res-cell res-h res-h-center res-h-obac" style="grid-column: span 3;">OBAC</div>

                <div class="res-cell res-h">EXPEDIENTES PAC</div>
                <div class="res-cell res-h">PROCESOS</div>
                <div class="res-cell res-h">ESTIMADOS (SOLES)</div>

                <div class="res-cell res-h"></div>
                <div class="res-cell res-h"></div>
                <div class="res-cell res-h center">EP</div>
                <div class="res-cell res-h center">FAP</div>
                <div class="res-cell res-h center">MGP</div>
                <div class="res-cell res-h"></div>
                <div class="res-cell res-h"></div>
                <div class="res-cell res-h"></div>

                <!-- PROCESO DE COMPRAS (3 filas) -->
                <div class="res-cell strong" style="grid-row: span 3;">PROCESO DE COMPRAS</div>

                <div class="res-cell">Corporativo</div>
                <div class="res-cell center">1</div>
                <div class="res-cell center">1</div>
                <div class="res-cell center">1</div>
                <div class="res-cell center">3</div>
                <div class="res-cell center">1</div>
                <div class="res-cell num"><?= money(61367168.26) ?></div>

                <div class="res-cell">Individual</div>
                <div class="res-cell center">3</div>
                <div class="res-cell center">3</div>
                <div class="res-cell center">6</div>
                <div class="res-cell center">12</div>
                <div class="res-cell center">12</div>
                <div class="res-cell num"><?= money(108323098.68) ?></div>

                <div class="res-cell res-sub strong">SUB TOTAL</div>
                <div class="res-cell res-sub center strong">4</div>
                <div class="res-cell res-sub center strong">4</div>
                <div class="res-cell res-sub center strong">7</div>
                <div class="res-cell res-sub center strong">15</div>
                <div class="res-cell res-sub center strong">13</div>
                <div class="res-cell res-sub num strong"><?= money(169690266.94) ?></div>

                <!-- TOTAL (fila completa) -->
                <div class="res-cell res-total strong right" style="grid-column: span 2;">TOTAL</div>
                <div class="res-cell res-total center strong">4</div>
                <div class="res-cell res-total center strong">4</div>
                <div class="res-cell res-total center strong">7</div>
                <div class="res-cell res-total center strong">15</div>
                <div class="res-cell res-total center strong">13</div>
                <div class="res-cell res-total num strong"><?= money(170099832.94) ?></div>

                <!-- VALOR ESTIMADO (solo EP/FAP/MGP) -->
                <div class="res-cell res-foot strong" style="grid-column: span 2;">VALOR ESTIMADO (SOLES)</div>
                <div class="res-cell res-foot num strong"><?= money(50460533.08) ?></div>
                <div class="res-cell res-foot num strong"><?= money(34457249.61) ?></div>
                <div class="res-cell res-foot num strong"><?= money(85182050.25) ?></div>
                <div class="res-cell res-foot" style="grid-column: span 3;"></div>
            </div>
        </div>
    </section>

</main>

<?php if (!$isPrint) require_once __DIR__ . '/../../layout/bottom-nav.php'; ?>

<style>
    /* =========================
   TOKENS / BASE
   ========================= */
    :root {
        --primary: #0F2F5A;
        --accent: #6B1C26;

        --bg: #f1f5f9;
        --card: rgba(255, 255, 255, .96);

        --text-strong: #0f172a;
        --text: #1f2937;
        --muted: #64748b;
        --muted2: #94a3b8;

        --line: rgba(148, 163, 184, .22);
        --line2: rgba(148, 163, 184, .18);

        --shadow-sm: 0 10px 20px rgba(0, 0, 0, .10);
        --shadow-md: 0 14px 28px rgba(0, 0, 0, .10);
        --shadow-lg: 0 18px 40px rgba(0, 0, 0, .10);

        --r-lg: 22px;
        --r-md: 18px;
        --r-sm: 14px;
    }

    * {
        box-sizing: border-box;
    }

    img,
    svg,
    video,
    canvas {
        max-width: 100%;
        height: auto;
    }

    html {
        -webkit-text-size-adjust: 100%;
    }

    body {
        background: var(--bg);
        color: var(--text);
    }

    /* =========================
   LAYOUT BASE
   ========================= */
    .page-shell {
        width: 100%;
        padding-left: 20px;
        padding-right: 20px;
        padding-bottom: calc(92px + env(safe-area-inset-bottom));
    }

    @media (min-width:1024px) {
        .page-shell {
            max-width: 1240px;
            margin: 0 auto;
            padding-left: 24px;
            padding-right: 24px;
        }
    }

    @media (max-width:420px) {
        .page-shell {
            padding-left: 14px;
            padding-right: 14px;
        }
    }

    /* =========================
   HEADER
   ========================= */
    .headbox {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 10px;
        background: rgba(255, 255, 255, .92);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 12px;
        box-shadow: var(--shadow-md);
    }

    .back {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: #f8fafc;
        border: 1px solid rgba(148, 163, 184, .35);
        font-weight: 800;
        font-size: 1.35rem;
        color: var(--text-strong);
        text-decoration: none;
        flex: 0 0 auto;
    }

    .headbox .min-w-0 {
        flex: 1 1 220px;
        min-width: 0;
    }

    .headbox p {
        line-height: 1.1;
    }

    .topline {
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        line-height: 1.2;
    }

    .title {
        margin-top: 6px;
        font-size: 1.05rem;
        font-weight: 750;
        color: var(--text-strong);
        line-height: 1.15;
        word-break: break-word;
    }

    @media (min-width:640px) {
        .title {
            font-size: 1.35rem;
        }
    }

    .pill {
        margin-left: auto;
        padding: 10px 12px;
        border-radius: 999px;
        background: rgba(107, 28, 38, .10);
        border: 1px solid rgba(107, 28, 38, .18);
        color: rgba(107, 28, 38, .95);
        font-weight: 700;
        white-space: nowrap;
    }

    @media (max-width:520px) {
        .pill {
            order: 2;
            padding: 8px 10px;
            font-size: .75rem;
            align-self: flex-start;
        }
    }

    /* =========================
   ACTIONS / BOTONES EXPORT
   ========================= */
    .actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    @media (max-width:520px) {
        .actions {
            flex: 1 1 100%;
            order: 3;
        }
    }

    .btnx {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        height: 38px;
        padding: 0 14px;
        border-radius: 999px;

        text-decoration: none;
        user-select: none;
        white-space: nowrap;

        font-weight: 700;
        font-size: .82rem;

        border: 1px solid transparent;
        box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
        transition: transform .08s ease, filter .15s ease, box-shadow .15s ease;
    }

    .btnx:hover {
        filter: brightness(1.03);
        box-shadow: 0 12px 26px rgba(0, 0, 0, .10);
    }

    .btnx:active {
        transform: scale(.99);
    }

    .btnx--primary {
        background: var(--primary);
        color: #fff;
        border-color: rgba(15, 47, 90, .18);
    }

    .btnx--ghost {
        background: #fff;
        color: var(--text-strong);
        border-color: rgba(148, 163, 184, .35);
    }

    .ico {
        width: 18px;
        height: 18px;
        display: grid;
        place-items: center;
        font-weight: 900;
        opacity: .9;
    }

    .btnx--ghost .ico {
        color: #166534;
    }

    .btnx--primary .ico {
        color: #fff;
    }

    @media (max-width:520px) {
        .btnx {
            flex: 1 1 0;
            height: 34px;
            padding: 0 10px;
            font-size: .78rem;
        }
    }

    /* =========================
   TOOLBAR (BUSCAR)
   ========================= */
    .toolbar {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search {
        position: relative;
        flex: 1 1 260px;
    }

    @media (max-width:520px) {
        .search {
            flex: 1 1 100%;
        }
    }

    .search input {
        width: 100%;
        height: 40px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, .35);
        background: #fff;
        padding: 0 44px 0 42px;
        /* icono + clear */
        outline: none;

        font-weight: 600;
        color: var(--text);

        transition: box-shadow .15s ease, border-color .15s ease;
    }

    .search input::placeholder {
        color: var(--muted2);
        font-weight: 500;
    }

    .search input:focus {
        border-color: rgba(15, 47, 90, .45);
        box-shadow: 0 0 0 4px rgba(15, 47, 90, .10);
    }

    @media (max-width:420px) {
        .search input {
            height: 38px;
        }
    }

    .s-ico {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        display: grid;
        place-items: center;
        color: var(--muted);
        opacity: .9;
        pointer-events: none;
        font-weight: 900;
    }

    .s-clear {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, .25);
        background: #f8fafc;
        color: #475569;
        display: none;
        /* JS */
        place-items: center;
        cursor: pointer;
        font-weight: 900;
    }

    .s-clear:hover {
        filter: brightness(1.03);
    }

    /* =========================
   KPI
   ========================= */
    .kpi {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 14px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, .08);
        min-width: 0;
    }

    @media (max-width:420px) {
        .kpi {
            padding: 12px;
        }
    }

    .kpi-l {
        font-size: .75rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .kpi-v {
        margin-top: 6px;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-strong);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width:420px) {
        .kpi-v {
            font-size: 1rem;
        }
    }

    /* =========================
   CARDS
   ========================= */
    .cards {
        display: grid;
        gap: 12px;
    }

    .card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 12px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .08);
        min-width: 0;
    }

    /* =========================
   STATUS / BADGES
   ========================= */
    .obac {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 52px;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: rgba(107, 28, 38, .10);
        border: 1px solid rgba(107, 28, 38, .18);
        color: var(--accent);
        font-weight: 700;
        white-space: nowrap;
    }

    .st {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: .72rem;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .st-ok {
        background: rgba(22, 163, 74, .12);
        color: #166534;
        border-color: rgba(22, 163, 74, .20);
    }

    .st-warn {
        background: rgba(107, 28, 38, .12);
        color: var(--accent);
        border-color: rgba(107, 28, 38, .20);
    }

    .st-muted {
        background: rgba(148, 163, 184, .16);
        color: #475569;
        border-color: rgba(148, 163, 184, .25);
    }

    /* =========================
   CARD V2
   ========================= */
    .card-v2 {
        padding: 18px;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .card-head-left {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .tp {
        font-size: .75rem;
        color: var(--muted);
        font-weight: 600;
    }

    .card-title {
        margin-top: 10px;
        font-weight: 750;
        color: var(--text-strong);
        line-height: 1.25;
        font-size: .95rem;
        word-break: break-word;
    }

    .meta-line {
        display: flex;
        gap: 18px;
        font-size: .75rem;
        color: var(--muted);
        margin-top: 6px;
        margin-bottom: 14px;
    }

    .finance {
        display: flex;
        justify-content: space-between;
        padding: 14px;
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, .18);
    }

    .finance small {
        display: block;
        font-size: .70rem;
        color: var(--muted);
        margin-bottom: 4px;
        font-weight: 600;
    }

    .finance strong {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-strong);
    }

    /* =========================
   DETAILS: timeline simple (hist/sit)
   ========================= */
    .timeline {
        margin-top: 14px;
        border-top: 1px solid rgba(148, 163, 184, .18);
        padding-top: 10px;
    }

    .timeline summary {
        font-size: .80rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
    }

    .timeline-body {
        margin-top: 8px;
        font-size: .80rem;
        color: #475569;
        line-height: 1.4;
    }

    .mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        color: #475569;
    }

    /* =========================
   TIMELINE DESPLEGABLE (DETAILS)
   ========================= */
    .tlx {
        margin-top: 14px;
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }

    .tlx-sum {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        cursor: pointer;
        user-select: none;
        list-style: none;
        background: rgba(15, 47, 90, .05);
    }

    .tlx-sum::-webkit-details-marker {
        display: none;
    }

    .tlx-title {
        font-weight: 650;
        color: var(--text-strong);
    }

    .tlx-meta {
        margin-left: auto;
        font-size: .75rem;
        font-weight: 600;
        color: var(--muted);
    }

    .tlx-chev {
        width: 10px;
        height: 10px;
        border-right: 3px solid rgba(15, 47, 90, .45);
        border-bottom: 3px solid rgba(15, 47, 90, .45);
        transform: rotate(45deg);
        transition: transform .16s ease;
    }

    .tlx[open] .tlx-chev {
        transform: rotate(-135deg);
    }

    .tlx-body {
        padding: 10px 12px;
        border-top: 1px solid rgba(148, 163, 184, .16);
    }

    /* =========================
   TIMELINE POR PROCESO
   ========================= */
    .timeline2-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .timeline2-item {
        position: relative;
        display: grid;
        grid-template-columns: 16px 1fr;
        gap: 10px;
        padding: 8px 0;
    }

    .timeline2-item:not(:last-child)::after {
        content: "";
        position: absolute;
        left: 7px;
        top: 18px;
        bottom: -8px;
        width: 2px;
        background: rgba(148, 163, 184, .22);
    }

    .timeline2-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        margin-top: 2px;
        border: 2px solid rgba(148, 163, 184, .55);
        background: #fff;
    }

    .timeline2-item.is-hist .timeline2-dot {
        border-color: rgba(15, 47, 90, .45);
    }

    .timeline2-item.is-sit .timeline2-dot {
        border-color: rgba(107, 28, 38, .50);
    }

    .timeline2-date {
        font-size: .72rem;
        font-weight: 700;
        color: var(--text-strong);
        margin-bottom: 2px;
    }

    .timeline2-text {
        font-size: .82rem;
        color: #475569;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    /* =========================
   RESUMEN (TABLA)
   ========================= */
    .tbl-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .tbl-top {
        padding: 14px 16px;
        border-bottom: 1px solid var(--line2);
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 10px;
    }

    .res-wrap {
        overflow: auto;
    }

    .res-title {
        min-width: 980px;
        padding: 14px 16px;
        text-align: center;
        font-weight: 750;
        color: var(--text-strong);
        background: rgba(15, 47, 90, .06);
        border-bottom: 1px solid var(--line2);
        letter-spacing: .02em;
    }

    .res-grid {
        display: grid;
        min-width: 980px;
        grid-template-columns: 220px 140px 110px 110px 110px 160px 120px 190px;
        gap: 0;
    }

    .res-cell {
        padding: 12px 10px;
        border-right: 1px solid var(--line2);
        border-bottom: 1px solid var(--line2);
        background: #fff;
        font-size: .85rem;
        color: #334155;
    }

    .res-cell:last-child {
        border-right: none;
    }

    .res-h {
        background: rgba(15, 47, 90, .06);
        font-weight: 650;
        color: var(--text-strong);
    }

    .res-sub,
    .res-total,
    .res-foot {
        background: #fff7d6;
    }

    /* =========================
   UTILIDADES
   ========================= */
    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .num {
        text-align: right;
        font-variant-numeric: tabular-nums;
        color: var(--text-strong);
    }

    .strong {
        font-weight: 750;
        color: var(--text-strong);
    }

    /* =========================
   PRINT (PDF)
   ========================= */
    @media print {
        .noprint {
            display: none !important;
        }

        .page-shell {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            padding-bottom: 0 !important;
        }

        .tbl-card,
        .kpi,
        .card {
            box-shadow: none !important;
        }

        details {
            open: true;
        }
    }

    .actions {
        margin-left: auto;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, .35);
        color: #475569;
        transition: all .15s ease;
    }

    .icon-btn svg {
        width: 18px;
        height: 18px;
    }

    .icon-btn:hover {
        background: #f8fafc;
        transform: translateY(-1px);
    }

    /* Colores sutiles */
    .icon-btn--excel {
        color: #166534;
        /* verde suave */
    }

    .icon-btn--pdf {
        color: #7f1d1d;
        /* rojo vino sutil */
    }

    /* iOS: evitar zoom al enfocar input (font-size >= 16px) */
    @media (max-width: 1024px) {
        .search input {
            font-size: 16px !important;
        }
    }

    .search input {
        -webkit-text-size-adjust: 100%;
    }
</style>

<script>
    (function() {
        "use strict";

        /* =========================
           HELPERS
           ========================= */
        const $ = (sel, root = document) => root.querySelector(sel);
        const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

        /* =========================
           DOM
           ========================= */
        const input = $("#q");
        const clearBtn = $("#qClear");
        const empty = $("#emptyCards");
        const cards = $$(".card[data-hay]");

        /* =========================
           FILTRO CARDS
           ========================= */
        function toggleClear() {
            if (!clearBtn || !input) return;
            clearBtn.style.display = input.value.trim() ? "grid" : "none";
        }

        function applyFilter() {
            if (!input) return;

            const term = input.value.trim().toUpperCase();
            const showAll = term.length === 0;

            let visible = 0;
            for (const c of cards) {
                const hay = (c.dataset.hay || "").toUpperCase();
                const ok = showAll || hay.includes(term);
                c.style.display = ok ? "" : "none";
                if (ok) visible++;
            }

            if (empty) empty.style.display = (visible === 0 ? "" : "none");
            toggleClear();
        }

        /* =========================
           EVENTS
           ========================= */
        if (input) {
            input.addEventListener("input", applyFilter);
            input.addEventListener("keydown", (e) => {
                // ESC limpia
                if (e.key === "Escape") {
                    input.value = "";
                    applyFilter();
                    input.blur();
                }
            });
        }

        if (clearBtn && input) {
            clearBtn.addEventListener("click", () => {
                input.value = "";
                applyFilter();
                input.focus();
            });
        }

        /* =========================
           PRINT
           =========================
           Si estás en modo print, el PHP ya llama window.print().
           Este bloque no interfiere.
           ========================= */

        /* =========================
           INIT
           ========================= */
        window.addEventListener("load", applyFilter);
    })();
</script>