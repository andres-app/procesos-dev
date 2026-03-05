<?php
// Vista/modulos/admin/pac.php (MAQUETA / SIN BD)
// Apple-like UI + responsive desktop
$titulo = 'PAC';
$active = 'pac';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$pacs = [
  [
    'id' => 1,
    'nopac' => '0002',
    'pn' => 'NP',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICION DE EQUIPO Y MATERIAL DE COMUNICACIONES PARA LA CIRDS CUI: 2385483',
    'obac' => 'CCFFAA',
    'tp' => 'LP',
    'fuente' => '-',
    'estimado' => 1494900.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 2,
    'nopac' => '0001',
    'pn' => 'NP',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICION DE MATERIAL PARA TRABAJOS DE RESCATE (REPEL) CUI: 2385483',
    'obac' => 'CCFFAA',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 3777218.90,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 3,
    'nopac' => '24',
    'pn' => 'NP',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICIÓN DE AERONAVE DE MEDIANO ALCANCE Y MEDIANA CAPACIDAD DE CARGA PARA LA RECUPERACIÓN DE LA CAPACIDAD DE MOVILIDAD AÉREA EFICAZ DE LA FUERZA AÉREA DEL PERÚ - GRUPO AÉREO N 8 CUI 2234934 PP-0135',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 239850000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 4,
    'nopac' => '0043',
    'pn' => 'P',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE EMBARCACIONES MARÍTIMAS Y FLUVIALES /SERVICIO PP 0135/REQ.0009-2026-DIRECOMAR',
    'obac' => 'MGP',
    'tp' => 'CPA',
    'fuente' => '-',
    'estimado' => 90000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 5,
    'nopac' => '0041',
    'pn' => 'P',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE VEHÍCULOS /SERVICIO PP 0135/REQ.0007-2026-DIRECOMAR',
    'obac' => 'MGP',
    'tp' => 'CP',
    'fuente' => '-',
    'estimado' => 570000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 6,
    'nopac' => '0040',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO OBLIGATORIO DE ACCIDENTES DE TRÁNSITO (SOAT) /SERVICIO PP 0135/REQ.0006-2026-DIRECOMAR',
    'obac' => 'MGP',
    'tp' => 'SIE',
    'fuente' => '-',
    'estimado' => 44860.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 7,
    'nopac' => '0028',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE MANTENIMIENTO Y REPARACIÓN DE VEHÍCULOS TÁCTICOS DE LA COMANDANCIA DE LA FUERZA DE INFANTERÍA DE MARINA /SERVICIO PP 0135/REQ.0008-2026-COMFUINMAR',
    'obac' => 'MGP',
    'tp' => 'CPS',
    'fuente' => '-',
    'estimado' => 610000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 8,
    'nopac' => '0008',
    'pn' => 'P',
    'estado' => 'OBSERVADO',
    'descripcion' => 'ADQUISICIÓN DE KITS DE 1500 HORAS PARA LOS DIÉSEL PROPULSORES Y DIÉSEL ALTERNADORES DE LAS UNIDADES DE LA FUERZA DE SUPERFICIE /BIEN PP0135/REQ.0007-2026-COMFAS',
    'obac' => 'MGP',
    'tp' => 'LP',
    'fuente' => '-',
    'estimado' => 490000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 9,
    'nopac' => '0086',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE EXPLOSIVOS PARA EL SISTEMA DE EYECCIÓN MKPE-16LF KT-1P PP-0135',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 5943990.63,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 10,
    'nopac' => '0085',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE COMPONENTES PARA EL MANTENIMIENTO DEL SISTEMA DE EYECCIÓN MKPE-16LF KT-1P PP-0135',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 5141785.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 11,
    'nopac' => '0067',
    'pn' => 'P',
    'estado' => 'OBSERVADO',
    'descripcion' => 'ADQUISICION DE KIT DE EQUIPAMIENTO PARA EXTINCION DE INCENDIOS APLICABLE A LOS HELICOPTEROS MI-17/171SH Y HELICOPTEROS BELL-212/412 PP-0135 CUI 2662544',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 5703825.12,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 12,
    'nopac' => '0066',
    'pn' => 'P',
    'estado' => 'OBSERVADO',
    'descripcion' => 'SERVICIO DE OVERHAUL DE MOTOR PT6A-34 APLICABLE A LA FLOTA DHC-6-400 PP-0135',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 11333280.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 13,
    'nopac' => '0062',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICION DE LUCES DE BALIZAMIENTO PORTATIL - EN EL GRUPO AEREO NUMERO 11 PARA LA OPTIMIZACION DEL SERVICIO DE APROXIMACION DE AERONAVES EN OPERACIONES AEREAS NOCTURNAS MEDIANTE LA ADQUISICION DE UN SISTEMA MOVIL EN EL DISTRITO DE PARIÑAS PROVINCIA DE TALARA, DEPARTAMENTO DE PIURA PP-0135',
    'obac' => 'FAP',
    'tp' => 'LP',
    'fuente' => '-',
    'estimado' => 3181162.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 14,
    'nopac' => '0022',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => '0135 - SEGURO OBLIGATORIO DE ACCIDENTES DE TRANSITO - SOAT PP-0135',
    'obac' => 'FAP',
    'tp' => 'CC',
    'fuente' => '-',
    'estimado' => 29113.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 15,
    'nopac' => '0005',
    'pn' => 'P',
    'estado' => 'ESTUDIO DE MERCADO',
    'descripcion' => 'ADQUISICIÓN DE EQUIPOS DE SEGURIDAD DE LAS COMUNICACIONES Y LICENCIA DE OPERACIÓN PARA EL SISTEMA CRIPTOLOGICO DE LA FAP PP-0135',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 5240386.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 16,
    'nopac' => '0004',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICION DE EQUIPAMIENTO DE COMUNICACIÓN SATELITAL TACTICO TIPO MANPACK PP-0135',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 1390640.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 17,
    'nopac' => '0001',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE RASTREO SATELITAL UUDD FAP Y VRAEM PP-0135 PP-032',
    'obac' => 'FAP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 1230441.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 18,
    'nopac' => '0136',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE EQUIPOS DE PARACAIDAS AF-2026',
    'obac' => 'EP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 5999999.99,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 19,
    'nopac' => '0083',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE RENOVACIÓN DEL PROGRAMA PRO ADVANTAGE (POWER ADVANTAGE + PRO PARTS) PARA EL AVIÓN CESSNA CITATION XLS EP-861 DE LA AVIACIÓN DEL EJÉRCITO',
    'obac' => 'EP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 1275000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 20,
    'nopac' => '0076',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE CAMIONETAS PARA EL PROYECTO DE INVERSIÓN MI PERU',
    'obac' => 'EP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 5356144.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 21,
    'nopac' => '0075',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE VEHÍCULOS DE APOYO DE COMBATE PARA EL PROYECTO DE INVERSIÓN PURISUNCHU',
    'obac' => 'EP',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 14000000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 22,
    'nopac' => '0060',
    'pn' => 'P',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE VEHÍCULOS E HIDROCARBUROS-135',
    'obac' => 'EP',
    'tp' => 'CPS',
    'fuente' => '-',
    'estimado' => 1300000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 23,
    'nopac' => '0059',
    'pn' => 'P',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE EMBARCACIONES FLUVIALES -PP 135',
    'obac' => 'EP',
    'tp' => 'CPS',
    'fuente' => '-',
    'estimado' => 849963.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 24,
    'nopac' => '0057',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO OBLIGATORIO DE ACCIDENTES DE TRÁNSITO (SOAT)-PP 135-',
    'obac' => 'EP',
    'tp' => 'SIE',
    'fuente' => '-',
    'estimado' => 120867.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
  [
    'id' => 25,
    'nopac' => '0001',
    'pn' => 'P',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE SOPORTE TECNICO PARA SISTEMA SATELITAL',
    'obac' => 'CONIDA',
    'tp' => 'RES',
    'fuente' => '-',
    'estimado' => 8200000.00,
    'publicacion' => '',
    'ejecucion' => '',
    'fpc' => '',
  ],
];

function pill($txt, $tone = 'slate')
{
  $map = [
    'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
    'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
    'blue'  => 'bg-blue-50 text-blue-700 border-blue-200',
    'rose'  => 'bg-rose-50 text-rose-700 border-rose-200',
  ];
  $c = $map[$tone] ?? $map['slate'];
  return '<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium ' . $c . '">' . $txt . '</span>';
}

function toneEstado($estado)
{
  $e = strtoupper($estado);
  if ($e === 'PUBLICADO') return 'green';
  if ($e === 'BORRADOR') return 'slate';
  if ($e === 'OBSERVADO') return 'amber';
  if ($e === 'ANULADO') return 'rose';
  return 'slate';
}

?>

<?php
// ===== KPIs (Programables / No programables) =====
$cntP  = 0;
$cntNP = 0;
$sumP  = 0.0;
$sumNP = 0.0;

foreach ($pacs as $r) {
  $pn  = strtoupper(trim((string)($r['pn'] ?? 'NP')));
  $est = (float)($r['estimado'] ?? 0);

  if ($pn === 'P') {
    $cntP++;
    $sumP += $est;
  } else {
    $cntNP++;
    $sumNP += $est;
  }
}
?>

<div class="space-y-5">

  <!-- Header -->
  <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
      <div class="text-xs text-slate-500">Administrador</div>
      <h1 class="text-xl font-semibold tracking-tight leading-tight">PAC</h1>
      <div class="text-xs text-slate-500">Mantenimiento (maqueta)</div>
    </div>

    <div class="flex flex-col sm:flex-row gap-2">
      <div class="relative">
        <input
          placeholder="Buscar por N° PAC, OBAC, descripción…"
          class="w-full sm:w-80 border border-slate-200 bg-white outline-none focus:ring-2 focus:ring-slate-200 inp" />
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">⌕</span>
      </div>

      <button class="border border-slate-200 bg-white btn btn-ghost">Filtros</button>

      <button id="btnNew" class="btn btn-primary">+ Nuevo PAC</button>
    </div>
  </div>

  <!-- KPIs -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
    <!-- Total PAC Programables -->
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Total PAC Programables</div>
      <div class="mt-1 text-2xl font-semibold"><?= (int)$cntP ?></div>
      <div class="mt-2 text-xs text-slate-500">P</div>
    </div>

    <!-- Total PAC No Programables -->
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Total PAC No Programables</div>
      <div class="mt-1 text-2xl font-semibold"><?= (int)$cntNP ?></div>
      <div class="mt-2 text-xs text-slate-500">NP</div>
    </div>

    <!-- Estimado total Programables -->
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Estimado total Programables</div>
      <div class="mt-1 text-2xl font-semibold">
        S/ <?= number_format($sumP, 2, '.', ',') ?>
      </div>
      <div class="mt-2 text-xs text-slate-500">Suma de estimados (P)</div>
    </div>

    <!-- Estimado total No Programables -->
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Estimado total No Programables</div>
      <div class="mt-1 text-2xl font-semibold">
        S/ <?= number_format($sumNP, 2, '.', ',') ?>
      </div>
      <div class="mt-2 text-xs text-slate-500">Suma de estimados (NP)</div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="card border border-slate-200 bg-white shadow-soft overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
      <div class="font-semibold">PAC registrados</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full thc tbc">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">N° PAC</th>
            <th class="text-left font-medium px-4 py-3">P/NP</th>
            <th class="text-left font-medium px-4 py-3">Descripción</th>
            <th class="text-left font-medium px-4 py-3">OBAC</th>
            <th class="text-left font-medium px-4 py-3">Fuente</th>
            <th class="text-left font-medium px-4 py-3">Estado</th>
            <th class="text-left font-medium px-4 py-3">Estimado</th>
            <th class="text-right font-medium px-4 py-3">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <?php foreach ($pacs as $r): ?>
            <tr class="hover:bg-slate-50">

              <!-- N° PAC -->
              <td class="px-4 py-3 font-semibold text-slate-900">
                <?= h($r['nopac']) ?>
              </td>

              <!-- P / NP -->
              <td class="px-4 py-3">
                <?= pill(h($r['pn'] ?? 'NP'), ($r['pn'] ?? 'NP') === 'P' ? 'blue' : 'slate') ?>
              </td>

              <!-- Descripción (3ra columna) -->
              <td class="px-4 py-3 max-w-[520px]">
                <div class="text-slate-900 line-clamp-2" title="<?= h($r['descripcion']) ?>">
                  <?= h($r['descripcion']) ?>
                </div>
                <div class="mt-1 text-xs text-slate-500">ID: <?= (int)$r['id'] ?></div>
              </td>
              <!-- OBAC -->
              <td class="px-4 py-3">
                <?= pill(h($r['obac']), 'blue') ?>
              </td>

              <!-- Fuente -->
              <td class="px-4 py-3">
                <?= pill(h($r['fuente']), 'amber') ?>
              </td>

              <!-- Estado -->
              <td class="px-4 py-3">
                <?= pill(h($r['estado']), toneEstado($r['estado'])) ?>
              </td>

              <!-- Estimado -->
              <td class="px-4 py-3 whitespace-nowrap">
                S/ <?= number_format((float)$r['estimado'], 2) ?>
              </td>

              <!-- Acciones -->
              <td class="px-4 py-3">
                <div class="flex justify-end gap-1.5">
                  <!-- Editar -->
                  <button
                    class="iconbtn"
                    title="Editar"
                    aria-label="Editar PAC <?= h($r['nopac']) ?>"
                    onclick="openEdit(
          <?= (int)$r['id'] ?>,
          '<?= h($r['nopac']) ?>',
          '<?= h($r['pn'] ?? 'NP') ?>',
          '<?= h($r['estado']) ?>',
          '<?= h($r['obac']) ?>',
          '<?= h($r['tp']) ?>',
          '<?= h($r['fuente']) ?>',
          '<?= h($r['descripcion']) ?>',
          '<?= h($r['estimado']) ?>',
          '<?= h($r['publicacion'] ?? '') ?>',
          '<?= h($r['ejecucion'] ?? '') ?>'
        )">
                    <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
                      <path fill="currentColor"
                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm2.92 
            2.83H5v-.92l9.06-9.06.92.92L5.92 
            20.08zM20.71 7.04a1 1 0 0 0 
            0-1.41l-2.34-2.34a1 1 0 0 
            0-1.41 0l-1.83 1.83 
            3.75 3.75 1.83-1.83z" />
                    </svg>
                  </button>

                  <!-- Eliminar -->
                  <button
                    class="iconbtn iconbtn--danger"
                    title="Eliminar"
                    aria-label="Eliminar PAC <?= h($r['nopac']) ?>"
                    onclick="openDelete(<?= (int)$r['id'] ?>, '<?= h($r['nopac']) ?>')">
                    <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
                      <path fill="currentColor"
                        d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z" />
                    </svg>
                  </button>
                </div>
              </td>

            </tr>
          <?php endforeach; ?>

          <?php if (count($pacs) === 0): ?>
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                No hay registros (maqueta).
              </td>
            </tr>
          <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Modal (Nuevo/Editar) -->
<div id="modalForm" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalForm')"></div>

  <div class="relative w-full max-w-3xl rounded-3xl border border-slate-200 glass shadow-soft">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <div class="text-xs text-slate-500">PAC</div>
        <div id="modalTitle" class="text-lg font-semibold">Nuevo PAC</div>
      </div>
      <button class="rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50"
        onclick="closeModal('modalForm')">Cerrar</button>
    </div>

    <form class="p-5 grid grid-cols-1 md:grid-cols-6 gap-3">
      <input type="hidden" id="pac_id" value="">

      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">P/NP</label>
        <select id="pac_pn" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option value="P">P</option>
          <option value="NP">NP</option>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Estado</label>
        <input id="pac_estado" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
      </div>

      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">OBAC</label>
        <input id="pac_obac" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
      </div>

      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">TP</label>
        <input id="pac_tp" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
      </div>

      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">Fuente</label>
        <input id="pac_fuente" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
      </div>

      <div class="md:col-span-6">
        <label class="block text-xs text-slate-500 mb-1">Descripción</label>
        <textarea id="pac_desc" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5"></textarea>
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Estimado (S/.)</label>
        <input id="pac_estimado" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Publicación</label>
        <input id="pac_pub" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="dd/mm/aa">
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Ejecución</label>
        <input id="pac_eje" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="FEB/MAR/...">
      </div>

      <div class="md:col-span-6 flex items-center justify-end gap-2 pt-2">
        <button type="button" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm hover:bg-slate-50"
          onclick="closeModal('modalForm')">Cancelar</button>
        <button type="button" class="rounded-2xl bg-slate-900 text-white px-4 py-2.5 text-sm font-medium hover:bg-slate-800"
          onclick="fakeSave()">
          Guardar (maqueta)
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal delete -->
<div id="modalDelete" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalDelete')"></div>

  <div class="relative w-full max-w-md rounded-3xl border border-slate-200 glass shadow-soft">
    <div class="px-5 py-4 border-b border-slate-200">
      <div class="text-xs text-slate-500">Eliminar</div>
      <div class="text-lg font-semibold">Confirmación</div>
    </div>
    <div class="p-5 text-sm text-slate-700">
      ¿Eliminar el PAC <span id="delPac" class="font-semibold"></span>? (solo maqueta)
    </div>
    <div class="p-5 pt-0 flex justify-end gap-2">
      <button class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm hover:bg-slate-50"
        onclick="closeModal('modalDelete')">Cancelar</button>
      <button class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 px-4 py-2.5 text-sm hover:bg-rose-100"
        onclick="fakeDelete()">
        Eliminar (maqueta)
      </button>
    </div>
  </div>
</div>

<style>
  .glass {
    background: rgba(255, 255, 255, .78);
    backdrop-filter: blur(10px);
  }

  .shadow-soft {
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
  }

  /* Compact helpers */
  .card {
    border-radius: 14px;
  }

  .btn {
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 13px;
  }

  .btn-primary {
    background: #0f172a;
    color: #fff;
  }

  .btn-primary:hover {
    background: #111827;
  }

  .btn-ghost:hover {
    background: #f8fafc;
  }

  .inp {
    border-radius: 14px;
    padding: 9px 12px;
    font-size: 13px;
  }

  .thc th {
    padding: 10px 12px;
    font-size: 12px;
  }

  .tbc td {
    padding: 10px 12px;
    font-size: 13px;
  }

  /* Icon actions (sutil + compacto) */
  .iconbtn {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    border: 1px solid rgba(148, 163, 184, .45);
    background: rgba(255, 255, 255, .9);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: rgb(71, 85, 105);
    /* slate-600 */
    transition: background .15s ease, border-color .15s ease, color .15s ease, transform .05s ease;
  }

  .iconbtn:hover {
    background: rgb(248, 250, 252);
    border-color: rgba(148, 163, 184, .7);
    color: rgb(15, 23, 42);
    /* slate-900 */
  }

  .iconbtn:active {
    transform: scale(.98);
  }

  .iconbtn:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(148, 163, 184, .35);
  }

  .iconbtn .ico {
    width: 16px;
    height: 16px;
    display: block;
  }

  /* Danger solo en hover (no “grita” por defecto) */
  .iconbtn--danger {
    color: rgb(71, 85, 105);
  }

  .iconbtn--danger:hover {
    background: rgba(244, 63, 94, .08);
    border-color: rgba(244, 63, 94, .35);
    color: rgb(190, 18, 60);
  }
</style>

<script>
  const $ = (id) => document.getElementById(id);

  function openModal(id) {
    $(id).classList.remove('hidden');
    $(id).classList.add('flex');
  }

  function closeModal(id) {
    $(id).classList.add('hidden');
    $(id).classList.remove('flex');
  }

  $('btnNew')?.addEventListener('click', () => {
    $('modalTitle').textContent = 'Nuevo PAC';
    $('pac_id').value = '';
    $('pac_nopac').value = '';
    $('pac_estado').value = 'PUBLICADO';
    $('pac_obac').value = '';
    $('pac_tp').value = 'CATAL';
    $('pac_fuente').value = 'RO';
    $('pac_desc').value = '';
    $('pac_estimado').value = '';
    $('pac_pub').value = '';
    $('pac_eje').value = '';
    openModal('modalForm');
  });

  function openEdit(id, nopac, pn, estado, obac, tp, fuente, desc, estimado, pub, eje) {
    $('modalTitle').textContent = 'Editar PAC #' + id;
    $('pac_id').value = id;
    $('pac_nopac').value = nopac;
    $('pac_pn').value = pn;
    $('pac_estado').value = estado;
    $('pac_obac').value = obac;
    $('pac_tp').value = tp;
    $('pac_fuente').value = fuente;
    $('pac_desc').value = desc;
    $('pac_estimado').value = estimado;
    $('pac_pub').value = pub;
    $('pac_eje').value = eje;
    openModal('modalForm');
  }

  function openDelete(id, nopac) {
    $('delPac').textContent = nopac + ' (ID ' + id + ')';
    openModal('modalDelete');
  }

  function fakeSave() {
    alert('Guardado (maqueta). Con BD, aquí harías POST al controlador.');
    closeModal('modalForm');
  }

  function fakeDelete() {
    alert('Eliminado (maqueta). Con BD, aquí harías POST al controlador.');
    closeModal('modalDelete');
  }
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>