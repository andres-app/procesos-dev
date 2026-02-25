<?php
$titulo = 'Derivados | Reportes';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require_once __DIR__ . '/../../layout/header.php';

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
   EXPORT: CSV (Excel)
   URL: ?export=csv
   ========================= */
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="reporte_derivados_af_2026.csv"');

    $out = fopen('php://output', 'w');

    // BOM para Excel (evita problemas de tildes)
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, [
        'N°',
        'EXP. PAC',
        'OBAC',
        'HISTORIAL',
        'DESCRIPCIÓN',
        'FF',
        'TP',
        'ESTIMADO',
        'ADJUDICADO',
        'FPC',
        'ESTADO',
        'SITUACIÓN'
    ]);

    foreach ($rows as $r) {
        fputcsv($out, [
            (int)$r['n'],
            $r['exp'],
            badgeObac($r['obac']),
            str_replace(["\r\n", "\r"], ["\n", "\n"], (string)$r['hist']),
            (string)$r['desc'],
            (string)$r['ff'],
            (string)$r['tp'],
            (float)$r['est'],
            (float)$r['adj'],
            (string)$r['fpc'],
            (string)$r['estado'],
            str_replace(["\r\n", "\r"], ["\n", "\n"], (string)$r['sit']),
        ]);
    }

    // Fila totales (opcional)
    fputcsv($out, ['', '', '', '', '', '', 'TOTAL', (float)$totalEstimado, (float)$totalAdj, '', '', '']);

    fclose($out);
    exit;
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
                <p class="text-xs text-slate-500 font-bold">AGENCIA • OFICINA DE PLANEAMIENTO Y PRESUPUESTO</p>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Contrataciones derivadas • AF-2026</h2>
            </div>

            <div class="actions noprint">
                <a class="btnx" href="?export=csv" title="Exportar a Excel (CSV)">Excel</a>
                <a class="btnx" href="?export=print" title="Exportar a PDF (Imprimir)">PDF</a>
            </div>

            <div class="pill">AF-2026</div>
        </div>

        <div class="toolbar noprint">
            <div class="seg">
                <button type="button" class="segbtn is-on" data-view="cards">Móvil</button>
                <button type="button" class="segbtn" data-view="table">Tabla</button>
            </div>

            <div class="search">
                <input id="q" type="search" placeholder="Buscar (expediente, OBAC, descripción, estado)..." autocomplete="off">
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
       VISTA MÓVIL (CARDS)
       ========================= -->
    <section class="mb-6 view view-cards">
        <div class="cards">
            <?php foreach ($rows as $r): ?>
                <?php
                $haystack = strtoupper(
                    trim($r['exp'] . ' ' . $r['obac'] . ' ' . $r['desc'] . ' ' . $r['estado'] . ' ' . $r['tp'] . ' ' . $r['ff'] . ' ' . $r['fpc'] . ' ' . $r['hist'] . ' ' . $r['sit'])
                );
                ?>
                <article class="card" data-hay="<?= htmlspecialchars($haystack) ?>">
                    <div class="card-top">
                        <div class="card-left">
                            <div class="tag">EXP <?= htmlspecialchars($r['exp']) ?></div>
                            <div class="tag"><?= htmlspecialchars(badgeObac($r['obac'])) ?></div>
                            <div class="tag"><?= htmlspecialchars($r['tp']) ?></div>
                            <div class="tag"><?= htmlspecialchars($r['ff']) ?></div>
                            <div class="tag"><?= htmlspecialchars($r['fpc']) ?></div>
                        </div>
                        <div class="card-right">
                            <span class="st <?= statusClass($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></span>
                        </div>
                    </div>

                    <h3 class="card-title"><?= htmlspecialchars($r['desc']) ?></h3>

                    <div class="moneybox">
                        <div>
                            <div class="ml">Estimado</div>
                            <div class="mv"><?= money($r['est']) ?></div>
                        </div>
                        <div>
                            <div class="ml">Adjudicado</div>
                            <div class="mv"><?= money($r['adj']) ?></div>
                        </div>
                    </div>

                    <?php if (!empty(trim((string)$r['sit']))): ?>
                        <details class="dets">
                            <summary>Situación</summary>
                            <div class="preline"><?= nl2br(htmlspecialchars($r['sit'])) ?></div>
                        </details>
                    <?php endif; ?>

                    <?php if (!empty(trim((string)$r['hist']))): ?>
                        <details class="dets">
                            <summary>Historial</summary>
                            <div class="preline mono"><?= nl2br(htmlspecialchars($r['hist'])) ?></div>
                        </details>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- =========================
       VISTA TABLA (SCROLL)
       ========================= -->
    <section class="tbl-card mb-6 view view-table">
        <div class="tbl-top">
            <p class="text-sm font-black text-slate-900">Detalle</p>
            <p class="text-xs text-slate-500 font-bold">Desliza horizontalmente para ver todas las columnas</p>
        </div>

        <div class="tbl-wrap">
            <table class="tbl" id="tbl">
                <thead>
                    <tr>
                        <th class="sticky-col">N°</th>
                        <th class="sticky-col2">EXP. PAC</th>
                        <th>OBAC</th>
                        <th>HISTORIAL</th>
                        <th>DESCRIPCIÓN</th>
                        <th>FF</th>
                        <th>TP</th>
                        <th class="num">ESTIMADO</th>
                        <th class="num">ADJUDICADO</th>
                        <th>FPC</th>
                        <th>ESTADO</th>
                        <th>SITUACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <?php
                        $haystack = strtoupper(
                            trim($r['exp'] . ' ' . $r['obac'] . ' ' . $r['desc'] . ' ' . $r['estado'] . ' ' . $r['tp'] . ' ' . $r['ff'] . ' ' . $r['fpc'] . ' ' . $r['hist'] . ' ' . $r['sit'])
                        );
                        ?>
                        <tr data-hay="<?= htmlspecialchars($haystack) ?>">
                            <td class="center sticky-col"><?= (int)$r['n'] ?></td>
                            <td class="center sticky-col2"><?= htmlspecialchars($r['exp']) ?></td>
                            <td class="center">
                                <span class="obac"><?= htmlspecialchars(badgeObac($r['obac'])) ?></span>
                            </td>
                            <td class="mono pre"><?= nl2br(htmlspecialchars($r['hist'])) ?></td>
                            <td class="strong"><?= htmlspecialchars($r['desc']) ?></td>
                            <td class="center"><?= htmlspecialchars($r['ff']) ?></td>
                            <td class="center"><?= htmlspecialchars($r['tp']) ?></td>
                            <td class="num"><?= money($r['est']) ?></td>
                            <td class="num"><?= money($r['adj']) ?></td>
                            <td class="center"><?= htmlspecialchars($r['fpc']) ?></td>
                            <td class="center">
                                <span class="st <?= statusClass($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></span>
                            </td>
                            <td class="pre"><?= nl2br(htmlspecialchars($r['sit'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="right strong">TOTAL</td>
                        <td class="num strong"><?= money($totalEstimado) ?></td>
                        <td class="num strong"><?= money($totalAdj) ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <!-- RESUMEN (SE MANTIENE) -->
    <section class="tbl-card mb-10">
        <div class="tbl-top">
            <p class="text-sm font-black text-slate-900">Resumen</p>
            <p class="text-xs text-slate-500 font-bold">Estructura similar a tu cuadro</p>
        </div>

        <div class="res-grid">
            <div class="res-cell res-h">FASES</div>
            <div class="res-cell res-h">MODALIDAD</div>
            <div class="res-cell res-h">OBAC EP</div>
            <div class="res-cell res-h">OBAC FAP</div>
            <div class="res-cell res-h">OBAC MGP</div>
            <div class="res-cell res-h">EXPEDIENTES PAC</div>
            <div class="res-cell res-h">PROCESOS</div>
            <div class="res-cell res-h">ESTIMADOS (SOLES)</div>

            <div class="res-cell" style="grid-row: span 3;">PROCESO DE COMPRAS</div>

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

            <div class="res-cell strong">SUB TOTAL</div>
            <div class="res-cell center strong">4</div>
            <div class="res-cell center strong">4</div>
            <div class="res-cell center strong">7</div>
            <div class="res-cell center strong">15</div>
            <div class="res-cell center strong">13</div>
            <div class="res-cell num strong"><?= money(169690266.94) ?></div>
        </div>
    </section>

</main>

<?php if (!$isPrint) require_once __DIR__ . '/../../layout/bottom-nav.php'; ?>

<?php if ($isPrint): ?>
    <script>
        // Vista para imprimir -> el usuario guarda como PDF
        window.addEventListener('load', () => window.print());
    </script>
<?php endif; ?>

<script>
  const btns = document.querySelectorAll('.segbtn');
  const views = {
    cards: document.querySelector('.view-cards'),
    table: document.querySelector('.view-table'),
  };

  const q = document.getElementById('q');
  const cards = document.querySelectorAll('.card[data-hay]');
  const trs = document.querySelectorAll('.view-table tr[data-hay]');

  // mensaje "sin resultados" (para cards y tabla)
  function ensureEmptyState() {
    if (!document.getElementById('emptyCards')) {
      const d = document.createElement('div');
      d.id = 'emptyCards';
      d.style.display = 'none';
      d.style.padding = '12px';
      d.style.textAlign = 'center';
      d.style.color = '#64748b';
      d.style.fontWeight = '800';
      d.textContent = 'Sin resultados.';
      views.cards.querySelector('.cards')?.appendChild(d);
    }
    if (!document.getElementById('emptyTable')) {
      const d = document.createElement('div');
      d.id = 'emptyTable';
      d.style.display = 'none';
      d.style.padding = '12px 16px';
      d.style.textAlign = 'center';
      d.style.color = '#64748b';
      d.style.fontWeight = '800';
      d.textContent = 'Sin resultados.';
      views.table.querySelector('.tbl-top')?.after(d);
    }
  }

  function applyFilter() {
    const term = (q?.value || '').trim().toUpperCase();
    const showAll = term.length === 0;

    let visibleCards = 0;
    cards.forEach(c => {
      const ok = showAll || (c.dataset.hay || '').includes(term);
      c.style.display = ok ? '' : 'none';
      if (ok) visibleCards++;
    });

    let visibleRows = 0;
    trs.forEach(tr => {
      const ok = showAll || (tr.dataset.hay || '').includes(term);
      tr.style.display = ok ? '' : 'none';
      if (ok) visibleRows++;
    });

    // empty states
    ensureEmptyState();
    const emptyCards = document.getElementById('emptyCards');
    const emptyTable = document.getElementById('emptyTable');

    // cards: si NO hay cards visibles, mostramos mensaje
    if (emptyCards) emptyCards.style.display = (visibleCards === 0 ? '' : 'none');

    // tabla: si NO hay filas visibles, mostramos mensaje
    if (emptyTable) emptyTable.style.display = (visibleRows === 0 ? '' : 'none');
  }

  function setView(v) {
    btns.forEach(b => b.classList.toggle('is-on', b.dataset.view === v));

    // Importantísimo: forzar display correcto
    views.cards.style.display = (v === 'cards' ? '' : 'none');
    views.table.style.display = (v === 'table' ? '' : 'none');

    localStorage.setItem('rep_view', v);

    // Re-aplicar filtro al cambiar de vista (evita "tabla vacía")
    applyFilter();

    // forzar reflow (ayuda en algunos móviles)
    requestAnimationFrame(() => {
      document.body.offsetHeight;
    });
  }

  // init
  setView(localStorage.getItem('rep_view') || 'cards');
  btns.forEach(b => b.addEventListener('click', () => setView(b.dataset.view)));

  if (q) q.addEventListener('input', applyFilter);

  // aplica filtro al cargar por si el input tiene valor guardado por el navegador
  window.addEventListener('load', applyFilter);
</script>

<style>
  /* =========================
     LAYOUT BASE
     ========================= */
  .page-shell {
    width: 100%;
    padding-bottom: calc(92px + env(safe-area-inset-bottom)); /* evita que el bottom-nav tape */
  }

  @media (min-width:1024px) {
    .page-shell {
      max-width: 1240px;
      margin: 0 auto;
      padding-left: 24px;
      padding-right: 24px;
    }
  }

  @media (max-width:420px){
    .page-shell{
      padding-left: 14px !important;
      padding-right: 14px !important;
    }
  }

  /* =========================
     HEADER
     ========================= */
  .headbox {
    display: flex;
    flex-wrap: wrap;                 /* CLAVE: que rompa filas */
    align-items: flex-start;
    gap: 10px;
    background: rgba(255, 255, 255, .92);
    border: 1px solid rgba(148, 163, 184, .22);
    border-radius: 22px;
    padding: 12px;
    box-shadow: 0 14px 28px rgba(0, 0, 0, .10);
  }

  .back {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, .35);
    font-weight: 900;
    font-size: 1.4rem;
    color: #0f172a;
    text-decoration: none;
    flex: 0 0 auto;
  }

  /* El bloque del título debe poder ocupar todo el ancho */
  .headbox .min-w-0{
    flex: 1 1 220px;
    min-width: 0;
  }

  .headbox h2{
    line-height: 1.15;
    font-size: 1.05rem;  /* móvil */
    margin-top: 6px;
  }

  @media (min-width:640px){
    .headbox h2{ font-size: 1.35rem; }
  }

  /* acciones en móvil: bajan a otra fila */
  .actions {
    display: flex;
    gap: 8px;
  }

  @media (max-width:520px){
    .actions{
      flex: 1 1 100%;
      order: 3;                    /* debajo del título */
    }
  }

  .btnx {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px;
    padding: 0 12px;
    border-radius: 999px;
    background: #0F2F5A;
    color: #fff;
    text-decoration: none;
    font-weight: 900;
    font-size: .82rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
  }

  @media (max-width:520px){
    .btnx{
      flex: 1 1 0;
      height: 34px;
      padding: 0 10px;
      font-size: .78rem;
    }
  }

  .btnx:hover { filter: brightness(1.05); }

  .pill {
    margin-left: auto;
    padding: 10px 12px;
    border-radius: 999px;
    background: rgba(107, 28, 38, .10);
    border: 1px solid rgba(107, 28, 38, .18);
    color: #6B1C26;
    font-weight: 900;
    white-space: nowrap;
  }

  @media (max-width:520px){
    .pill{
      order: 2;
      padding: 8px 10px;
      font-size: .75rem;
      align-self: flex-start;
    }
  }

  /* =========================
     TOOLBAR (Móvil/Tabla + Buscar)
     ========================= */
  .toolbar {
    margin-top: 10px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .seg {
    display: inline-flex;
    border: 1px solid rgba(148, 163, 184, .25);
    border-radius: 999px;
    overflow: hidden;
    background: #fff;
    flex: 0 0 auto;
  }

  .segbtn {
    height: 38px;
    padding: 0 14px;
    font-weight: 900;
    font-size: .82rem;
    background: transparent;
    border: 0;
    cursor: pointer;
    color: #334155;
  }

  @media (max-width:420px){
    .segbtn{ height: 34px; padding: 0 12px; font-size: .78rem; }
  }

  .segbtn.is-on {
    background: rgba(15, 47, 90, .10);
    color: #0F2F5A;
  }

  .search {
    flex: 1 1 220px;
  }

  @media (max-width:520px){
    .search{ flex: 1 1 100%; } /* buscador full width */
  }

  #q {
    width: 100%;
    height: 38px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    padding: 0 14px;
    outline: none;
    background: #fff;
    font-weight: 700;
  }

  @media (max-width:420px){
    #q{ height: 36px; }
  }

  /* =========================
     KPI
     ========================= */
  .kpi {
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(148, 163, 184, .22);
    border-radius: 18px;
    padding: 14px;
    box-shadow: 0 12px 24px rgba(0, 0, 0, .08);
  }

  @media (max-width:420px){
    .kpi{ padding: 12px; }
  }

  .kpi-l {
    font-size: .75rem;
    font-weight: 900;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .03em;
  }

  .kpi-v {
    margin-top: 6px;
    font-size: 1.05rem;
    font-weight: 900;
    color: #0f172a;
    white-space: nowrap; /* evita que se rompa raro */
  }

  @media (max-width:420px){
    .kpi-v{ font-size: 1rem; }
  }

  /* =========================
     CARDS (mobile-first)
     ========================= */
  .cards {
    display: grid;
    gap: 12px;
  }

  .card {
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(148, 163, 184, .22);
    border-radius: 18px;
    padding: 12px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, .08);
  }

  .card-top {
    display: flex;
    gap: 10px;
    justify-content: space-between;
    align-items: flex-start;
  }

  .card-left {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    min-width: 0;
  }

  .tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 26px;
    padding: 0 10px;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, .25);
    font-weight: 900;
    font-size: .72rem;
    color: #334155;
    white-space: nowrap;
  }

  @media (max-width:420px){
    .tag{ height: 24px; padding: 0 8px; font-size: .68rem; }
  }

  .card-title {
    margin-top: 10px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.25;
    font-size: .95rem;
  }

  .moneybox {
    margin-top: 10px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, .18);
    border-radius: 14px;
    padding: 10px;
  }

  @media (max-width:360px){
    .moneybox{ grid-template-columns: 1fr; }
  }

  .ml {
    font-size: .72rem;
    font-weight: 900;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .03em;
  }

  .mv {
    margin-top: 4px;
    font-weight: 900;
    color: #0f172a;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }

  .dets {
    margin-top: 10px;
  }

  .dets summary {
    cursor: pointer;
    font-weight: 900;
    color: #0F2F5A;
    list-style: none;
    padding: 6px 0;
  }

  .dets summary::-webkit-details-marker { display: none; }

  .preline {
    margin-top: 6px;
    white-space: normal;
    color: #0f172a;
    font-size: .86rem;
  }

  .mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  }

  /* =========================
     TABLA
     ========================= */
  .tbl-card {
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(148, 163, 184, .22);
    border-radius: 22px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, .10);
    overflow: hidden;
  }

  .tbl-top {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148, 163, 184, .18);
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 10px;
  }

  .tbl-wrap {
    overflow: auto;
    -webkit-overflow-scrolling: touch;
  }

  .tbl {
    width: 1400px;
    border-collapse: separate;
    border-spacing: 0;
    font-size: .82rem;
  }

  .tbl thead th {
    position: sticky;
    top: 0;
    z-index: 3;
    background: #0F2F5A;
    color: #fff;
    text-align: left;
    padding: 12px 10px;
    font-weight: 900;
    white-space: nowrap;
  }

  .tbl td {
    padding: 10px;
    border-bottom: 1px solid rgba(148, 163, 184, .18);
    vertical-align: top;
    color: #0f172a;
    background: #fff;
  }

  .tbl tbody tr:hover td { background: #f8fafc; }

  .tbl tfoot td {
    background: #f8fafc;
    font-weight: 900;
  }

  .center { text-align: center; }
  .right { text-align: right; }
  .num { text-align: right; font-variant-numeric: tabular-nums; }
  .strong { font-weight: 900; }
  .pre { white-space: normal; }

  /* Sticky columnas */
  .sticky-col {
    position: sticky;
    left: 0;
    z-index: 2;
    background: inherit;
    box-shadow: 8px 0 14px rgba(0, 0, 0, .06);
  }

  .sticky-col2 {
    position: sticky;
    left: 54px;
    z-index: 2;
    background: inherit;
    box-shadow: 8px 0 14px rgba(0, 0, 0, .06);
  }

  .tbl thead .sticky-col { z-index: 4; }
  .tbl thead .sticky-col2 { z-index: 4; }

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
    color: #6B1C26;
    font-weight: 900;
  }

  .st {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    font-weight: 900;
    font-size: .72rem;
    white-space: nowrap;
  }

  .st-ok { background: rgba(22, 163, 74, .12); color: #166534; border: 1px solid rgba(22, 163, 74, .20); }
  .st-warn { background: rgba(107, 28, 38, .12); color: #6B1C26; border: 1px solid rgba(107, 28, 38, .20); }
  .st-muted { background: rgba(148, 163, 184, .16); color: #475569; border: 1px solid rgba(148, 163, 184, .25); }

  /* =========================
     RESUMEN
     ========================= */
  .res-grid {
    display: grid;
    grid-template-columns: 180px 140px repeat(3, 110px) 160px 120px 190px;
    gap: 0;
    overflow: auto;
  }

  .res-cell {
    padding: 12px 10px;
    border-right: 1px solid rgba(148, 163, 184, .18);
    border-bottom: 1px solid rgba(148, 163, 184, .18);
    background: #fff;
    font-size: .85rem;
  }

  .res-h {
    background: rgba(15, 47, 90, .06);
    font-weight: 900;
    color: #0f172a;
  }

  .res-cell:last-child { border-right: none; }

  /* Default: mostrar cards, ocultar tabla */
  .view-table { display: none; }

  /* =========================
     PRINT
     ========================= */
  @media print {
    .noprint { display: none !important; }
    .page-shell { max-width: none !important; margin: 0 !important; padding: 0 !important; }
    .tbl { width: 100% !important; font-size: 10px !important; }
    .tbl thead th { position: static !important; }
    .tbl-wrap { overflow: visible !important; }
    .tbl-card { box-shadow: none !important; border: 1px solid #ccc !important; }
    .kpi { box-shadow: none !important; }
    .view-cards { display: none !important; }
    .view-table { display: block !important; }
    .sticky-col, .sticky-col2 { position: static !important; box-shadow: none !important; }
  }
</style>