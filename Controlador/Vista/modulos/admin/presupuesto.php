<?php
// Vista/modulos/admin/presupuesto.php (MAQUETA / SIN BD)
// Módulo: Planeamiento y Presupuesto (Compras ACFFAA)
// Apple-like UI + responsive desktop
$titulo = 'Presupuesto';
$active = 'presupuesto';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n){ return 'S/ ' . number_format((float)$n, 2, '.', ','); }

$ejercicio = 'AF-2026';

/**
 * DATA MAQUETA
 * - techos por OBAC
 * - asignaciones por categoría
 * - ejecución mensual (compromiso/devengado/pagado) para gráfico simple
 * - detalle por "líneas presupuestales" (lo que se aprueba/ajusta)
 */
$techo_obac = [
  ['obac'=>'EP',  'techo'=> 18500000.00, 'asignado'=> 17240000.00],
  ['obac'=>'MGP', 'techo'=> 14200000.00, 'asignado'=> 12830000.00],
  ['obac'=>'FAP', 'techo'=> 12150000.00, 'asignado'=> 11990000.00],
];

$categorias = [
  ['cat'=>'Bienes',             'asignado'=> 23850000.00, 'compromiso'=> 14620000.00, 'devengado'=> 11240000.00, 'pagado'=>  9850000.00],
  ['cat'=>'Servicios',          'asignado'=> 16280000.00, 'compromiso'=>  9860000.00, 'devengado'=>  8050000.00, 'pagado'=>  7440000.00],
  ['cat'=>'Obras/Infraestructura','asignado'=>  1900000.00, 'compromiso'=>   420000.00, 'devengado'=>   210000.00, 'pagado'=>   120000.00],
];

$meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SET','OCT','NOV','DIC'];
$serie = [
  ['mes'=>'ENE','comp'=> 1800000,'dev'=> 1200000,'pag'=>  900000],
  ['mes'=>'FEB','comp'=> 2400000,'dev'=> 1700000,'pag'=> 1200000],
  ['mes'=>'MAR','comp'=> 3200000,'dev'=> 2400000,'pag'=> 1900000],
  ['mes'=>'ABR','comp'=> 4100000,'dev'=> 3000000,'pag'=> 2500000],
  ['mes'=>'MAY','comp'=> 5200000,'dev'=> 3800000,'pag'=> 3100000],
  ['mes'=>'JUN','comp'=> 6100000,'dev'=> 4600000,'pag'=> 3700000],
  ['mes'=>'JUL','comp'=> 7200000,'dev'=> 5400000,'pag'=> 4200000],
  ['mes'=>'AGO','comp'=> 8200000,'dev'=> 6100000,'pag'=> 4800000],
  ['mes'=>'SET','comp'=> 9100000,'dev'=> 6900000,'pag'=> 5400000],
  ['mes'=>'OCT','comp'=> 9900000,'dev'=> 7500000,'pag'=> 6100000],
  ['mes'=>'NOV','comp'=>10900000,'dev'=> 8200000,'pag'=> 6700000],
  ['mes'=>'DIC','comp'=>12050000,'dev'=> 9050000,'pag'=> 7440000],
];

// Líneas / partidas / iniciativas (maqueta)
$lineas = [
  [
    'id'=>101, 'obac'=>'EP', 'cat'=>'Bienes', 'fuente'=>'RO', 'cod'=>'2.3.2.1.1',
    'descripcion'=>'Adquisición de papelería y útiles de oficina (catálogos electrónicos)',
    'aprobado'=>  750000.00, 'ajustes'=>  50000.00, 'compromiso'=> 420000.00, 'devengado'=> 280000.00, 'pagado'=> 240000.00,
    'estado'=>'VIGENTE'
  ],
  [
    'id'=>102, 'obac'=>'MGP', 'cat'=>'Servicios', 'fuente'=>'RO', 'cod'=>'2.3.2.2.3',
    'descripcion'=>'Servicio de mantenimiento preventivo de equipos de comunicaciones',
    'aprobado'=> 1280000.00, 'ajustes'=> -80000.00, 'compromiso'=> 640000.00, 'devengado'=> 510000.00, 'pagado'=> 490000.00,
    'estado'=>'VIGENTE'
  ],
  [
    'id'=>103, 'obac'=>'FAP', 'cat'=>'Bienes', 'fuente'=>'RO', 'cod'=>'2.3.2.1.4',
    'descripcion'=>'Compra de repuestos críticos (stock estratégico)',
    'aprobado'=>  980000.00, 'ajustes'=>      0.00, 'compromiso'=> 310000.00, 'devengado'=> 110000.00, 'pagado'=>  80000.00,
    'estado'=>'EN REV.'
  ],
  [
    'id'=>104, 'obac'=>'EP', 'cat'=>'Obras/Infraestructura', 'fuente'=>'RO', 'cod'=>'2.6.2.1.1',
    'descripcion'=>'Adecuación de almacenes para bienes estratégicos (tramo 1)',
    'aprobado'=> 1900000.00, 'ajustes'=> 150000.00, 'compromiso'=> 420000.00, 'devengado'=> 210000.00, 'pagado'=> 120000.00,
    'estado'=>'PROGRAM.'
  ],
];

function toneEstado($s){
  $x = strtoupper(trim($s));
  if ($x === 'VIGENTE') return 'green';
  if ($x === 'EN REV.' || $x === 'EN REV') return 'amber';
  if ($x === 'PROGRAM.' || $x === 'PROGRAM') return 'blue';
  return 'slate';
}
function pill($txt, $tone='slate'){
  $map = [
    'slate'=>'bg-slate-100 text-slate-700 border-slate-200',
    'green'=>'bg-emerald-50 text-emerald-700 border-emerald-200',
    'amber'=>'bg-amber-50 text-amber-800 border-amber-200',
    'blue' =>'bg-blue-50 text-blue-700 border-blue-200',
    'rose' =>'bg-rose-50 text-rose-700 border-rose-200',
  ];
  $c = $map[$tone] ?? $map['slate'];
  return '<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium '.$c.'">'.h($txt).'</span>';
}

$techo_total = 0; $asignado_total = 0;
foreach($techo_obac as $t){ $techo_total += $t['techo']; $asignado_total += $t['asignado']; }

$comp_total = 0; $dev_total = 0; $pag_total = 0;
foreach($categorias as $c){ $comp_total += $c['compromiso']; $dev_total += $c['devengado']; $pag_total += $c['pagado']; }

$saldo_disponible = max(0, $asignado_total - $comp_total);
$porc_comp = $asignado_total > 0 ? round(($comp_total/$asignado_total)*100,1) : 0;
$porc_dev  = $asignado_total > 0 ? round(($dev_total/$asignado_total)*100,1) : 0;
$porc_pag  = $asignado_total > 0 ? round(($pag_total/$asignado_total)*100,1) : 0;

?>
<div class="space-y-5">

  <!-- Header -->
  <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
      <div class="text-xs text-slate-500">Oficina de Planeamiento y Presupuesto</div>
      <h1 class="text-xl font-semibold tracking-tight leading-tight">Presupuesto de Compras</h1>
      <div class="text-xs text-slate-500">Ejercicio <?= h($ejercicio) ?> · Vista maqueta (sin BD)</div>
    </div>

    <div class="flex flex-col sm:flex-row gap-2">
      <div class="relative">
        <input
          id="q"
          placeholder="Buscar por código, OBAC, descripción…"
          class="w-full sm:w-80 border border-slate-200 bg-white outline-none focus:ring-2 focus:ring-slate-200 inp" />
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">⌕</span>
      </div>

      <select id="f_obac" class="border border-slate-200 bg-white inp sm:w-36">
        <option value="">OBAC: Todas</option>
        <option>EP</option>
        <option>MGP</option>
        <option>FAP</option>
      </select>

      <select id="f_cat" class="border border-slate-200 bg-white inp sm:w-44">
        <option value="">Categoría: Todas</option>
        <option>Bienes</option>
        <option>Servicios</option>
        <option>Obras/Infraestructura</option>
      </select>

      <button class="btn btn-ghost border border-slate-200 bg-white" onclick="openModal('modalAjuste')">Ajuste</button>
      <button class="btn btn-primary" onclick="openModal('modalLinea')">+ Nueva línea</button>
    </div>
  </div>

  <!-- KPIs -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
    <div class="kpi">
      <div class="text-xs text-slate-500">Techo total</div>
      <div class="mt-1 text-2xl font-semibold"><?= money($techo_total) ?></div>
      <div class="mt-2 text-xs text-slate-500">Suma de techos por OBAC</div>
    </div>
    <div class="kpi">
      <div class="text-xs text-slate-500">Asignado total</div>
      <div class="mt-1 text-2xl font-semibold"><?= money($asignado_total) ?></div>
      <div class="mt-2 text-xs text-slate-500">Asignación aprobada</div>
    </div>
    <div class="kpi">
      <div class="text-xs text-slate-500">Compromiso</div>
      <div class="mt-1 text-2xl font-semibold"><?= money($comp_total) ?></div>
      <div class="mt-2 text-xs text-slate-500"><?= $porc_comp ?>% del asignado</div>
    </div>
    <div class="kpi">
      <div class="text-xs text-slate-500">Saldo disponible</div>
      <div class="mt-1 text-2xl font-semibold"><?= money($saldo_disponible) ?></div>
      <div class="mt-2 text-xs text-slate-500">Asignado − Compromiso</div>
    </div>
  </div>

  <!-- Resumen + gráfico simple -->
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-3">

    <div class="card border border-slate-200 bg-white shadow-soft overflow-hidden xl:col-span-2">
      <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
        <div class="font-semibold">Ejecución mensual (Compromiso / Devengado / Pagado)</div>
        <div class="text-xs text-slate-500">Maqueta</div>
      </div>

      <div class="p-4">
        <div class="grid grid-cols-3 gap-2 mb-3">
          <div class="mini">
            <div class="text-xs text-slate-500">Devengado</div>
            <div class="mt-0.5 font-semibold"><?= money($dev_total) ?> <span class="text-xs text-slate-500 font-normal">(<?= $porc_dev ?>%)</span></div>
          </div>
          <div class="mini">
            <div class="text-xs text-slate-500">Pagado</div>
            <div class="mt-0.5 font-semibold"><?= money($pag_total) ?> <span class="text-xs text-slate-500 font-normal">(<?= $porc_pag ?>%)</span></div>
          </div>
          <div class="mini">
            <div class="text-xs text-slate-500">Brecha (Comp − Pag)</div>
            <div class="mt-0.5 font-semibold"><?= money(max(0, $comp_total - $pag_total)) ?></div>
          </div>
        </div>

        <div class="chartwrap">
          <canvas id="chartExec" height="110"></canvas>
        </div>

        <div class="mt-3 text-xs text-slate-500">
          Interpretación típica: Compromiso = orden/contrato; Devengado = conformidad/obligación; Pagado = tesorería.
        </div>
      </div>
    </div>

    <div class="card border border-slate-200 bg-white shadow-soft overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
        <div class="font-semibold">Asignación por OBAC</div>
        <div class="text-xs text-slate-500">Techo vs Asignado</div>
      </div>

      <div class="p-4 space-y-3">
        <?php foreach($techo_obac as $t): ?>
          <?php
            $pct = $t['techo'] > 0 ? min(100, round(($t['asignado']/$t['techo'])*100,1)) : 0;
          ?>
          <div class="space-y-1">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <?= pill($t['obac'], 'blue') ?>
                <span class="text-sm font-semibold text-slate-900"><?= h($t['obac']) ?></span>
              </div>
              <div class="text-xs text-slate-500"><?= $pct ?>%</div>
            </div>
            <div class="text-xs text-slate-600">
              Asignado: <span class="font-medium"><?= money($t['asignado']) ?></span>
              <span class="text-slate-400">/</span>
              Techo: <span class="font-medium"><?= money($t['techo']) ?></span>
            </div>
            <div class="bar">
              <span style="width: <?= $pct ?>%"></span>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="pt-2 border-t border-slate-100">
          <div class="text-xs text-slate-500 mb-2">Asignación por categoría</div>
          <?php foreach($categorias as $c): ?>
            <div class="flex items-center justify-between py-1">
              <div class="text-sm text-slate-800"><?= h($c['cat']) ?></div>
              <div class="text-sm font-semibold"><?= money($c['asignado']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla detalle -->
  <div class="card border border-slate-200 bg-white shadow-soft overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
      <div class="font-semibold">Líneas presupuestales</div>
      <div class="text-sm text-slate-500">Acciones: ver / editar / anular (maqueta)</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full thc tbc" id="tbl">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Código</th>
            <th class="text-left font-medium px-4 py-3">OBAC</th>
            <th class="text-left font-medium px-4 py-3">Categoría</th>
            <th class="text-left font-medium px-4 py-3">Fuente</th>
            <th class="text-left font-medium px-4 py-3">Descripción</th>
            <th class="text-right font-medium px-4 py-3">Aprobado</th>
            <th class="text-right font-medium px-4 py-3">Ajustes</th>
            <th class="text-right font-medium px-4 py-3">Vigente</th>
            <th class="text-right font-medium px-4 py-3">Comp.</th>
            <th class="text-right font-medium px-4 py-3">Dev.</th>
            <th class="text-right font-medium px-4 py-3">Pag.</th>
            <th class="text-right font-medium px-4 py-3">Estado</th>
            <th class="text-right font-medium px-4 py-3">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <?php foreach($lineas as $r): ?>
            <?php $vigente = $r['aprobado'] + $r['ajustes']; ?>
            <tr class="hover:bg-slate-50" data-row
                data-obac="<?= h($r['obac']) ?>"
                data-cat="<?= h($r['cat']) ?>"
                data-q="<?= h(strtoupper($r['cod'].' '.$r['obac'].' '.$r['cat'].' '.$r['fuente'].' '.$r['descripcion'].' '.$r['estado'])) ?>">
              <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap"><?= h($r['cod']) ?></td>
              <td class="px-4 py-3"><?= pill($r['obac'],'blue') ?></td>
              <td class="px-4 py-3"><?= pill($r['cat'],'slate') ?></td>
              <td class="px-4 py-3"><?= pill($r['fuente'],'amber') ?></td>
              <td class="px-4 py-3 max-w-[520px]">
                <div class="text-slate-900 line-clamp-2"><?= h($r['descripcion']) ?></div>
                <div class="mt-1 text-xs text-slate-500">ID: <?= (int)$r['id'] ?></div>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap"><?= money($r['aprobado']) ?></td>
              <td class="px-4 py-3 text-right whitespace-nowrap <?= $r['ajustes'] < 0 ? 'text-rose-700' : 'text-slate-700' ?>">
                <?= money($r['ajustes']) ?>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap font-semibold"><?= money($vigente) ?></td>
              <td class="px-4 py-3 text-right whitespace-nowrap"><?= money($r['compromiso']) ?></td>
              <td class="px-4 py-3 text-right whitespace-nowrap"><?= money($r['devengado']) ?></td>
              <td class="px-4 py-3 text-right whitespace-nowrap"><?= money($r['pagado']) ?></td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <?= pill($r['estado'], toneEstado($r['estado'])) ?>
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-1.5">
                  <button class="iconbtn" title="Ver detalle" aria-label="Ver detalle <?= h($r['cod']) ?>"
                    onclick="openView('<?= h($r['cod']) ?>','<?= h($r['descripcion']) ?>','<?= h($r['obac']) ?>','<?= h($r['cat']) ?>','<?= h($r['fuente']) ?>','<?= money($r['aprobado']) ?>','<?= money($r['ajustes']) ?>','<?= money($vigente) ?>','<?= money($r['compromiso']) ?>','<?= money($r['devengado']) ?>','<?= money($r['pagado']) ?>','<?= h($r['estado']) ?>')">
                    <!-- eye -->
                    <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
                      <path fill="currentColor" d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2.5A2.5 2.5 0 1 0 12 9a2.5 2.5 0 0 0 0 5z"/>
                    </svg>
                  </button>

                  <button class="iconbtn" title="Editar" aria-label="Editar <?= h($r['cod']) ?>"
                    onclick="openEditLinea(<?= (int)$r['id'] ?>,'<?= h($r['cod']) ?>','<?= h($r['obac']) ?>','<?= h($r['cat']) ?>','<?= h($r['fuente']) ?>','<?= h($r['descripcion']) ?>','<?= h($r['aprobado']) ?>','<?= h($r['ajustes']) ?>','<?= h($r['estado']) ?>')">
                    <!-- pencil -->
                    <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
                      <path fill="currentColor" d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm2.92 2.83H5v-.92l9.06-9.06.92.92L5.92 20.08zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                  </button>

                  <button class="iconbtn iconbtn--danger" title="Anular" aria-label="Anular <?= h($r['cod']) ?>"
                    onclick="openAnular(<?= (int)$r['id'] ?>,'<?= h($r['cod']) ?>')">
                    <!-- trash -->
                    <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
                      <path fill="currentColor" d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if(count($lineas)===0): ?>
            <tr><td colspan="13" class="px-4 py-10 text-center text-slate-500">No hay registros (maqueta).</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
      <div class="text-xs text-slate-500">Tip: filtra por OBAC/categoría o busca por código/descripcion.</div>
      <div class="text-xs text-slate-500">Export (maqueta): Excel / PDF</div>
    </div>
  </div>
</div>

<!-- Modal: Nueva/Editar línea -->
<div id="modalLinea" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalLinea')"></div>

  <div class="relative w-full max-w-3xl rounded-3xl border border-slate-200 glass shadow-soft">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <div class="text-xs text-slate-500">Presupuesto</div>
        <div id="modalLineaTitle" class="text-lg font-semibold">Nueva línea</div>
      </div>
      <button class="rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50"
        onclick="closeModal('modalLinea')">Cerrar</button>
    </div>

    <form class="p-5 grid grid-cols-1 md:grid-cols-6 gap-3">
      <input type="hidden" id="ln_id" value="">

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Código</label>
        <input id="ln_cod" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="2.3.2.1.1">
      </div>

      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">OBAC</label>
        <select id="ln_obac" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option>EP</option><option>MGP</option><option>FAP</option>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Categoría</label>
        <select id="ln_cat" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option>Bienes</option><option>Servicios</option><option>Obras/Infraestructura</option>
        </select>
      </div>

      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">Fuente</label>
        <select id="ln_fuente" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option>RO</option><option>RDR</option><option>DON</option>
        </select>
      </div>

      <div class="md:col-span-6">
        <label class="block text-xs text-slate-500 mb-1">Descripción</label>
        <textarea id="ln_desc" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5"
          placeholder="Describe la necesidad / iniciativa..."></textarea>
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Aprobado (S/.)</label>
        <input id="ln_aprob" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="0.00">
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Ajuste (S/.)</label>
        <input id="ln_ajuste" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="0.00">
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Estado</label>
        <select id="ln_estado" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option>VIGENTE</option><option>EN REV.</option><option>PROGRAM.</option><option>ANULADO</option>
        </select>
      </div>

      <div class="md:col-span-6 flex items-center justify-end gap-2 pt-2">
        <button type="button" class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm hover:bg-slate-50"
          onclick="closeModal('modalLinea')">Cancelar</button>
        <button type="button" class="rounded-2xl bg-slate-900 text-white px-4 py-2.5 text-sm font-medium hover:bg-slate-800"
          onclick="fakeSaveLinea()">
          Guardar (maqueta)
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Ajuste (reprogramación) -->
<div id="modalAjuste" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalAjuste')"></div>

  <div class="relative w-full max-w-xl rounded-3xl border border-slate-200 glass shadow-soft overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200">
      <div class="text-xs text-slate-500">Ajuste presupuestal</div>
      <div class="text-lg font-semibold">Reprogramar / Transferir (maqueta)</div>
    </div>
    <div class="p-5 space-y-3">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs text-slate-500 mb-1">Desde OBAC</label>
          <select class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
            <option>EP</option><option>MGP</option><option>FAP</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Hacia OBAC</label>
          <select class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
            <option>MGP</option><option>EP</option><option>FAP</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs text-slate-500 mb-1">Monto (S/.)</label>
          <input class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="0.00">
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Sustento</label>
          <input class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5" placeholder="Memo / informe / necesidad…">
        </div>
      </div>

      <div class="text-xs text-slate-500">En sistema real: flujo de aprobación, trazabilidad y bitácora de cambios.</div>

      <div class="flex justify-end gap-2 pt-2">
        <button class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm hover:bg-slate-50"
          onclick="closeModal('modalAjuste')">Cancelar</button>
        <button class="rounded-2xl border border-slate-900 bg-slate-900 text-white px-4 py-2.5 text-sm hover:bg-slate-800"
          onclick="fakeAjuste()">Aplicar (maqueta)</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Ver detalle -->
<div id="modalView" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalView')"></div>

  <div class="relative w-full max-w-2xl rounded-3xl border border-slate-200 glass shadow-soft overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
      <div>
        <div class="text-xs text-slate-500">Detalle</div>
        <div id="vw_title" class="text-lg font-semibold">—</div>
      </div>
      <button class="rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50"
        onclick="closeModal('modalView')">Cerrar</button>
    </div>

    <div class="p-5 space-y-3 text-sm text-slate-800">
      <div id="vw_desc" class="text-slate-900"></div>
      <div class="grid grid-cols-2 gap-2">
        <div class="mini"><div class="text-xs text-slate-500">OBAC</div><div id="vw_obac" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Categoría</div><div id="vw_cat" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Fuente</div><div id="vw_fte" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Estado</div><div id="vw_est" class="font-semibold">—</div></div>
      </div>

      <div class="grid grid-cols-3 gap-2">
        <div class="mini"><div class="text-xs text-slate-500">Aprobado</div><div id="vw_apr" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Ajustes</div><div id="vw_aju" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Vigente</div><div id="vw_vig" class="font-semibold">—</div></div>
      </div>

      <div class="grid grid-cols-3 gap-2">
        <div class="mini"><div class="text-xs text-slate-500">Compromiso</div><div id="vw_comp" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Devengado</div><div id="vw_dev" class="font-semibold">—</div></div>
        <div class="mini"><div class="text-xs text-slate-500">Pagado</div><div id="vw_pag" class="font-semibold">—</div></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Anular -->
<div id="modalAnular" class="fixed inset-0 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalAnular')"></div>

  <div class="relative w-full max-w-md rounded-3xl border border-slate-200 glass shadow-soft overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200">
      <div class="text-xs text-slate-500">Anular</div>
      <div class="text-lg font-semibold">Confirmación</div>
    </div>
    <div class="p-5 text-sm text-slate-700">
      ¿Anular la línea <span id="an_cod" class="font-semibold"></span>? (solo maqueta)
    </div>
    <div class="p-5 pt-0 flex justify-end gap-2">
      <button class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm hover:bg-slate-50"
        onclick="closeModal('modalAnular')">Cancelar</button>
      <button class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 px-4 py-2.5 text-sm hover:bg-rose-100"
        onclick="fakeAnular()">
        Anular (maqueta)
      </button>
    </div>
  </div>
</div>

<style>
  .glass { background: rgba(255,255,255,.78); backdrop-filter: blur(10px); }
  .shadow-soft { box-shadow: 0 10px 24px rgba(15, 23, 42, .08); }
  .card { border-radius: 14px; }
  .btn { border-radius: 12px; padding: 8px 12px; font-size: 13px; }
  .btn-primary { background:#0f172a; color:#fff; }
  .btn-primary:hover { background:#111827; }
  .btn-ghost:hover { background:#f8fafc; }
  .inp { border-radius: 14px; padding: 9px 12px; font-size: 13px; }
  .thc th { padding: 10px 12px; font-size: 12px; }
  .tbc td { padding: 10px 12px; font-size: 13px; }

  .kpi{
    border-radius: 24px;
    background: #fff;
    border: 1px solid rgb(226,232,240);
    padding: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
  }
  .mini{
    border-radius: 16px;
    border: 1px solid rgb(241,245,249);
    background: rgba(248,250,252,.7);
    padding: 10px 12px;
  }
  .bar{
    height: 9px;
    border-radius: 999px;
    background: rgb(241,245,249);
    border: 1px solid rgb(226,232,240);
    overflow: hidden;
  }
  .bar > span{
    display:block;
    height:100%;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(15,23,42,.85), rgba(15,23,42,.55));
  }
  .chartwrap{
    border-radius: 18px;
    border: 1px solid rgb(241,245,249);
    background: rgba(248,250,252,.6);
    padding: 10px;
  }

  /* Icon actions (sutil + compacto) */
  .iconbtn{
    width: 34px; height: 34px;
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.45);
    background: rgba(255,255,255,.9);
    display:inline-flex; align-items:center; justify-content:center;
    color: rgb(71,85,105);
    transition: background .15s ease, border-color .15s ease, color .15s ease, transform .05s ease;
  }
  .iconbtn:hover{ background: rgb(248,250,252); border-color: rgba(148,163,184,.7); color: rgb(15,23,42); }
  .iconbtn:active{ transform: scale(.98); }
  .iconbtn:focus-visible{ outline:none; box-shadow: 0 0 0 3px rgba(148,163,184,.35); }
  .iconbtn .ico{ width:16px; height:16px; display:block; }
  .iconbtn--danger{ color: rgb(71,85,105); }
  .iconbtn--danger:hover{ background: rgba(244,63,94,.08); border-color: rgba(244,63,94,.35); color: rgb(190,18,60); }
</style>

<script>
  const $ = (id) => document.getElementById(id);

  function openModal(id){ $(id).classList.remove('hidden'); $(id).classList.add('flex'); }
  function closeModal(id){ $(id).classList.add('hidden'); $(id).classList.remove('flex'); }

  // Filtros front (maqueta)
  function applyFilters(){
    const q = ($('q')?.value || '').trim().toUpperCase();
    const ob = $('f_obac')?.value || '';
    const cat = $('f_cat')?.value || '';
    document.querySelectorAll('[data-row]').forEach(tr=>{
      const rq = tr.getAttribute('data-q') || '';
      const rob = tr.getAttribute('data-obac') || '';
      const rcat = tr.getAttribute('data-cat') || '';
      const okQ = !q || rq.includes(q);
      const okO = !ob || rob === ob;
      const okC = !cat || rcat === cat;
      tr.style.display = (okQ && okO && okC) ? '' : 'none';
    });
  }
  ['q','f_obac','f_cat'].forEach(id=>{
    $(id)?.addEventListener('input', applyFilters);
    $(id)?.addEventListener('change', applyFilters);
  });

  // Ver detalle
  function openView(cod, desc, obac, cat, fte, apr, aju, vig, comp, dev, pag, est){
    $('vw_title').textContent = cod;
    $('vw_desc').textContent = desc;
    $('vw_obac').textContent = obac;
    $('vw_cat').textContent = cat;
    $('vw_fte').textContent = fte;
    $('vw_est').textContent = est;
    $('vw_apr').textContent = apr;
    $('vw_aju').textContent = aju;
    $('vw_vig').textContent = vig;
    $('vw_comp').textContent = comp;
    $('vw_dev').textContent = dev;
    $('vw_pag').textContent = pag;
    openModal('modalView');
  }

  // Editar línea (maqueta)
  function openEditLinea(id, cod, obac, cat, fuente, desc, aprobado, ajuste, estado){
    $('modalLineaTitle').textContent = 'Editar línea #' + id;
    $('ln_id').value = id;
    $('ln_cod').value = cod;
    $('ln_obac').value = obac;
    $('ln_cat').value = cat;
    $('ln_fuente').value = fuente;
    $('ln_desc').value = desc;
    $('ln_aprob').value = aprobado;
    $('ln_ajuste').value = ajuste;
    $('ln_estado').value = estado;
    openModal('modalLinea');
  }

  // Nueva línea
  // (si quieres botón aparte, puedes llamarlo desde + Nueva línea)
  function resetLinea(){
    $('modalLineaTitle').textContent = 'Nueva línea';
    $('ln_id').value = '';
    $('ln_cod').value = '';
    $('ln_obac').value = 'EP';
    $('ln_cat').value = 'Bienes';
    $('ln_fuente').value = 'RO';
    $('ln_desc').value = '';
    $('ln_aprob').value = '';
    $('ln_ajuste').value = '0.00';
    $('ln_estado').value = 'VIGENTE';
  }
  // si abres modalLinea con el botón superior, resetea antes:
  document.querySelector('button.btn.btn-primary')?.addEventListener('click', resetLinea);

  function openAnular(id, cod){
    $('an_cod').textContent = cod + ' (ID ' + id + ')';
    openModal('modalAnular');
  }

  function fakeSaveLinea(){
    alert('Guardado (maqueta). Con BD: validar + POST a controlador + auditoría.');
    closeModal('modalLinea');
  }
  function fakeAjuste(){
    alert('Ajuste aplicado (maqueta). Con BD: flujo de aprobación + bitácora.');
    closeModal('modalAjuste');
  }
  function fakeAnular(){
    alert('Anulado (maqueta). Con BD: marcar ANULADO + registrar motivo.');
    closeModal('modalAnular');
  }

  // Chart simple (sin librerías)
  (function(){
    const canvas = document.getElementById('chartExec');
    if(!canvas) return;
    const ctx = canvas.getContext('2d');

    const series = <?= json_encode($serie, JSON_UNESCAPED_UNICODE) ?>;
    const W = canvas.width = canvas.parentElement.clientWidth - 2; // responsive
    const H = canvas.height;

    // valores
    const maxV = Math.max(...series.map(x=>x.comp)) * 1.05;

    // padding
    const padL = 26, padR = 10, padT = 12, padB = 18;
    const plotW = W - padL - padR;
    const plotH = H - padT - padB;

    // grid
    ctx.clearRect(0,0,W,H);
    ctx.strokeStyle = 'rgba(148,163,184,.35)';
    ctx.lineWidth = 1;

    for(let i=0;i<=4;i++){
      const y = padT + (plotH*(i/4));
      ctx.beginPath();
      ctx.moveTo(padL, y);
      ctx.lineTo(W-padR, y);
      ctx.stroke();
    }

    const xStep = plotW / (series.length - 1);
    function y(v){ return padT + plotH - (v/maxV)*plotH; }
    function x(i){ return padL + i*xStep; }

    function drawLine(key, alpha){
      ctx.strokeStyle = `rgba(15,23,42,${alpha})`;
      ctx.lineWidth = 2;
      ctx.beginPath();
      series.forEach((p,i)=>{
        const xx = x(i), yy = y(p[key]);
        if(i===0) ctx.moveTo(xx,yy); else ctx.lineTo(xx,yy);
      });
      ctx.stroke();
    }

    // Compromiso (más fuerte), Devengado, Pagado
    drawLine('comp', .85);
    drawLine('dev',  .55);
    drawLine('pag',  .35);

    // labels (cada 2 meses)
    ctx.fillStyle = 'rgba(71,85,105,.9)';
    ctx.font = '11px system-ui, -apple-system, Segoe UI, Roboto, Arial';
    series.forEach((p,i)=>{
      if(i % 2 === 0){
        ctx.fillText(p.mes, x(i)-10, H-6);
      }
    });

    // leyenda
    const lx = padL, ly = 12;
    ctx.fillStyle = 'rgba(15,23,42,.85)'; ctx.fillRect(lx, ly, 10, 3);
    ctx.fillStyle = 'rgba(71,85,105,.9)'; ctx.fillText('Comp', lx+14, ly+4);
    ctx.fillStyle = 'rgba(15,23,42,.55)'; ctx.fillRect(lx+58, ly, 10, 3);
    ctx.fillStyle = 'rgba(71,85,105,.9)'; ctx.fillText('Dev', lx+72, ly+4);
    ctx.fillStyle = 'rgba(15,23,42,.35)'; ctx.fillRect(lx+112, ly, 10, 3);
    ctx.fillStyle = 'rgba(71,85,105,.9)'; ctx.fillText('Pag', lx+126, ly+4);

    // re-render on resize
    let t;
    window.addEventListener('resize', ()=>{
      clearTimeout(t);
      t = setTimeout(()=>location.reload(), 250); // maqueta simple
    });
  })();
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>