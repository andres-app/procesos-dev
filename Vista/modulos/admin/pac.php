<?php
// Vista/modulos/admin/pac.php (MAQUETA / SIN BD)
// Apple-like UI + responsive desktop
$titulo = 'PAC';
$active = 'pac';
require __DIR__ . '/../../layout/admin_header.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$pacs = [
  [
    'id' => 1,
    'nopac' => '0043',
    'estado' => 'PUBLICADO',
    'obac' => 'MGP',
    'tp' => 'CATAL',
    'fuente' => 'RO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE EMBARCACIONES MARÍTIMAS Y FLUVIALES /SERVICIO PP 0135/REQ.0009-2026-DIRECOMAR',
    'estimado' => 50699.30,
    'publicacion' => '23/01/26',
    'ejecucion' => 'FEB',
    'fpc' => 'bien',
  ],
  [
    'id' => 2,
    'nopac' => '0044',
    'estado' => 'PUBLICADO',
    'obac' => 'EP',
    'tp' => 'CPA',
    'fuente' => 'RO',
    'descripcion' => 'ADQUISICIÓN DE PAPELERÍA EN GENERAL, ÚTILES DE OFICINA A TRAVÉS DE CATÁLOGOS ELECTRÓNICOS DE ACUERDOS MARCO',
    'estimado' => 14775.70,
    'publicacion' => '23/01/26',
    'ejecucion' => 'MAR',
    'fpc' => 'pend',
  ],
];

function pill($txt, $tone='slate'){
  $map = [
    'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
    'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
    'blue'  => 'bg-blue-50 text-blue-700 border-blue-200',
    'rose'  => 'bg-rose-50 text-rose-700 border-rose-200',
  ];
  $c = $map[$tone] ?? $map['slate'];
  return '<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium '.$c.'">'.$txt.'</span>';
}

function toneEstado($estado){
  $e = strtoupper($estado);
  if ($e === 'PUBLICADO') return 'green';
  if ($e === 'BORRADOR') return 'slate';
  if ($e === 'OBSERVADO') return 'amber';
  if ($e === 'ANULADO') return 'rose';
  return 'slate';
}
?>

<style>
  .glass { background: rgba(255,255,255,.75); backdrop-filter: blur(10px); }
  .shadow-soft { box-shadow: 0 12px 30px rgba(15,23,42,.08); }
</style>

<div class="space-y-5">

  <!-- Header -->
  <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
      <div class="text-sm text-slate-500">Administrador</div>
      <h1 class="text-2xl font-semibold tracking-tight">PAC</h1>
      <div class="text-sm text-slate-500">Mantenimiento (maqueta)</div>
    </div>

    <div class="flex flex-col sm:flex-row gap-2">
      <div class="relative">
        <input
          placeholder="Buscar por N° PAC, OBAC, descripción..."
          class="w-full sm:w-96 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pr-10 outline-none focus:ring-2 focus:ring-slate-200"
        />
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">⌕</span>
      </div>

      <button class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium hover:bg-slate-50">
        Filtros
      </button>

      <button id="btnNew" class="rounded-2xl bg-slate-900 text-white px-4 py-2.5 text-sm font-medium hover:bg-slate-800">
        + Nuevo PAC
      </button>
    </div>
  </div>

  <!-- KPIs -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Total PAC</div>
      <div class="mt-1 text-2xl font-semibold"><?= count($pacs) ?></div>
      <div class="mt-2 text-xs text-slate-500">Vista maqueta</div>
    </div>
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Publicados</div>
      <div class="mt-1 text-2xl font-semibold">—</div>
      <div class="mt-2 text-xs text-slate-500">Pendiente de data real</div>
    </div>
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Estimado total</div>
      <div class="mt-1 text-2xl font-semibold">—</div>
      <div class="mt-2 text-xs text-slate-500">Pendiente de data real</div>
    </div>
    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-soft">
      <div class="text-xs text-slate-500">Alertas</div>
      <div class="mt-1 text-2xl font-semibold">—</div>
      <div class="mt-2 text-xs text-slate-500">Pendiente de reglas</div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="rounded-3xl border border-slate-200 bg-white shadow-soft overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
      <div class="font-semibold">PAC registrados</div>
      <div class="text-sm text-slate-500">Acciones: editar / eliminar (maqueta)</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">N° PAC</th>
            <th class="text-left font-medium px-4 py-3">Estado</th>
            <th class="text-left font-medium px-4 py-3">OBAC</th>
            <th class="text-left font-medium px-4 py-3">TP</th>
            <th class="text-left font-medium px-4 py-3">Fuente</th>
            <th class="text-left font-medium px-4 py-3">Descripción</th>
            <th class="text-left font-medium px-4 py-3">Estimado</th>
            <th class="text-left font-medium px-4 py-3">Publicación</th>
            <th class="text-left font-medium px-4 py-3">Ejecución</th>
            <th class="text-right font-medium px-4 py-3">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <?php foreach ($pacs as $r): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 font-semibold text-slate-900"><?= h($r['nopac']) ?></td>
              <td class="px-4 py-3">
                <?= pill(h($r['estado']), toneEstado($r['estado'])) ?>
              </td>
              <td class="px-4 py-3"><?= pill(h($r['obac']), 'blue') ?></td>
              <td class="px-4 py-3"><?= pill(h($r['tp']), 'slate') ?></td>
              <td class="px-4 py-3"><?= pill(h($r['fuente']), 'amber') ?></td>
              <td class="px-4 py-3 max-w-[520px]">
                <div class="text-slate-900 line-clamp-2"><?= h($r['descripcion']) ?></div>
                <div class="mt-1 text-xs text-slate-500">ID: <?= (int)$r['id'] ?></div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                S/ <?= number_format((float)$r['estimado'], 2) ?>
              </td>
              <td class="px-4 py-3 whitespace-nowrap"><?= h($r['publicacion']) ?></td>
              <td class="px-4 py-3 whitespace-nowrap"><?= h($r['ejecucion']) ?></td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  <button class="rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-xs hover:bg-slate-50"
                          onclick="openEdit(<?= (int)$r['id'] ?>, '<?= h($r['nopac']) ?>', '<?= h($r['estado']) ?>', '<?= h($r['obac']) ?>', '<?= h($r['tp']) ?>', '<?= h($r['fuente']) ?>', '<?= h($r['descripcion']) ?>', '<?= h($r['estimado']) ?>', '<?= h($r['publicacion']) ?>', '<?= h($r['ejecucion']) ?>')">
                    Editar
                  </button>
                  <button class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 px-3 py-1.5 text-xs hover:bg-rose-100"
                          onclick="openDelete(<?= (int)$r['id'] ?>, '<?= h($r['nopac']) ?>')">
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (count($pacs) === 0): ?>
            <tr>
              <td colspan="10" class="px-4 py-10 text-center text-slate-500">No hay registros (maqueta).</td>
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
        <label class="block text-xs text-slate-500 mb-1">N° PAC</label>
        <input id="pac_nopac" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
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

<script>
  const $ = (id) => document.getElementById(id);

  function openModal(id){ $(id).classList.remove('hidden'); $(id).classList.add('flex'); }
  function closeModal(id){ $(id).classList.add('hidden'); $(id).classList.remove('flex'); }

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

  function openEdit(id, nopac, estado, obac, tp, fuente, desc, estimado, pub, eje){
    $('modalTitle').textContent = 'Editar PAC #' + id;
    $('pac_id').value = id;
    $('pac_nopac').value = nopac;
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

  function openDelete(id, nopac){
    $('delPac').textContent = nopac + ' (ID ' + id + ')';
    openModal('modalDelete');
  }

  function fakeSave(){
    alert('Guardado (maqueta). Con BD, aquí harías POST al controlador.');
    closeModal('modalForm');
  }

  function fakeDelete(){
    alert('Eliminado (maqueta). Con BD, aquí harías POST al controlador.');
    closeModal('modalDelete');
  }
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>