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
      <div class="text-xs text-slate-500">Mantenimiento</div>
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
      <table id="tblPac" class="min-w-full thc tbc">
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
              </td>
              <!-- OBAC -->
              <td class="px-4 py-3">
                <?= pill(h($r['obac_nombre'] ?? '-'), 'blue') ?>
              </td>

              <!-- Fuente -->
              <td class="px-4 py-3">
                <?= pill(h($r['fuente_nombre'] ?? '-'), 'amber') ?>
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
                No hay registros.
              </td>
            </tr>
          <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Modal (Nuevo/Editar) — ADAPTADO a columnas del DataTable -->
<div id="modalForm" class="fixed inset-0 hidden items-center justify-center p-4 z-50">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalForm')"></div>

  <div class="relative w-full max-w-3xl rounded-3xl border border-slate-200 glass shadow-soft overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
      <div class="min-w-0">
        <div class="text-xs text-slate-500">PAC</div>
        <div id="modalTitle" class="text-lg font-semibold truncate">Nuevo PAC</div>
      </div>

      <button
        type="button"
        class="rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50"
        onclick="closeModal('modalForm')">Cerrar</button>
    </div>

    <!-- Body -->
    <form class="p-5 grid grid-cols-1 md:grid-cols-6 gap-3" onsubmit="return false;">
      <input type="hidden" id="pac_id" value="">

      <!-- N° PAC -->
      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">N° PAC</label>
        <input
          id="pac_nopac"
          class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5"
          placeholder="Ej: 0043"
          autocomplete="off">
      </div>

      <!-- P/NP -->
      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">P/NP</label>
        <select id="pac_pn" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option value="P">P</option>
          <option value="NP">NP</option>
        </select>
      </div>

      <!-- OBAC -->
      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">OBAC</label>
        <select id="pac_obac" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option value="">Seleccionar…</option>
          <option value="EP">EP</option>
          <option value="FAP">FAP</option>
          <option value="MGP">MGP</option>
          <option value="CCFFAA">CCFFAA</option>
          <option value="CONIDA">CONIDA</option>
        </select>
      </div>

      <!-- Fuente -->
      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">Fuente</label>
        <input
          id="pac_fuente"
          class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5"
          placeholder="Ej: RO"
          autocomplete="off">
      </div>

      <!-- Estado -->
      <div class="md:col-span-1">
        <label class="block text-xs text-slate-500 mb-1">Estado</label>
        <select id="pac_estado" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5">
          <option value="PUBLICADO">PUBLICADO</option>
        </select>
      </div>

      <!-- Descripción -->
      <div class="md:col-span-6">
        <div class="flex items-end justify-between gap-2">
          <label class="block text-xs text-slate-500 mb-1">Descripción</label>
          <div class="text-[11px] text-slate-400 mb-1">
            <span id="descCount">0</span>/400
          </div>
        </div>
        <textarea
          id="pac_desc"
          rows="4"
          maxlength="400"
          class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5"
          placeholder="Describe el requerimiento…"></textarea>
      </div>

      <!-- Estimado -->
      <div class="md:col-span-2">
        <label class="block text-xs text-slate-500 mb-1">Estimado (S/.)</label>
        <input
          id="pac_estimado"
          inputmode="decimal"
          class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2.5"
          placeholder="Ej: 90000.00"
          autocomplete="off">
        <div class="mt-1 text-[11px] text-slate-400">
          Vista: <span id="sum_estimado">S/ 0.00</span>
        </div>
      </div>

      <!-- Footer -->
      <div class="md:col-span-6 flex items-center justify-end gap-2 pt-2">
        <button
          type="button"
          class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm hover:bg-slate-50"
          onclick="closeModal('modalForm')">Cancelar</button>

        <button
          type="button"
          class="rounded-2xl bg-slate-900 text-white px-4 py-2.5 text-sm font-medium hover:bg-slate-800"
          onclick="fakeSave()">
          Guardar (maqueta)
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .25rem .55rem;
    border-radius: 999px;
    font-size: 12px;
    border: 1px solid rgba(148, 163, 184, .45);
    background: rgba(255, 255, 255, .85);
    color: rgb(71, 85, 105);
    max-width: 100%;
  }

  .chip--muted span {
    color: rgb(15, 23, 42);
    font-weight: 600;
  }
</style>

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
    const el = $(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
    if (id === 'modalForm') refreshSummary();
  }

  function closeModal(id) {
    const el = $(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
  }

  function fmtMoney(n) {
    const v = Number(String(n ?? '').replace(/,/g, ''));
    if (Number.isNaN(v)) return 'S/ 0.00';
    return 'S/ ' + v.toLocaleString('es-PE', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function refreshSummary() {
    const nopacEl = $('pac_nopac');
    const pnEl = $('pac_pn');
    const obacEl = $('pac_obac');
    const estadoEl = $('pac_estado');
    const estEl = $('pac_estimado');
    const descEl = $('pac_desc');

    $('sum_nopac').textContent = (nopacEl?.value || '-').trim() || '-';
    $('sum_pn').textContent = (pnEl?.value || '-').trim() || '-';
    $('sum_obac').textContent = (obacEl?.value || '-').trim() || '-';
    $('sum_estado').textContent = (estadoEl?.value || '-').trim() || '-';
    $('sum_estimado').textContent = fmtMoney(estEl?.value || '0');

    const d = descEl?.value || '';
    $('descCount').textContent = String(d.length);
  }

  // Live updates
  ['pac_nopac', 'pac_pn', 'pac_obac', 'pac_fuente', 'pac_estado', 'pac_desc', 'pac_estimado'].forEach((id) => {
    $(id)?.addEventListener('input', refreshSummary);
    $(id)?.addEventListener('change', refreshSummary);
  });

  // Nuevo
  $('btnNew')?.addEventListener('click', () => {
    $('modalTitle').textContent = 'Nuevo PAC';
    $('pac_id').value = '';
    $('pac_nopac').value = '';
    $('pac_pn').value = 'NP';
    $('pac_obac').value = '';
    $('pac_fuente').value = '-';
    $('pac_estado').value = 'PUBLICADO';
    $('pac_desc').value = '';
    $('pac_estimado').value = '';
    openModal('modalForm');
  });

  // Editar (mantiene tu firma actual del onclick con 11 params, pero solo usa los necesarios)
  function openEdit(id, nopac, pn, estado, obac, tp, fuente, desc, estimado, pub, eje) {
    $('modalTitle').textContent = 'Editar PAC #' + id;
    $('pac_id').value = id ?? '';
    $('pac_nopac').value = nopac ?? '';
    $('pac_pn').value = pn ?? 'NP';
    $('pac_estado').value = estado ?? 'PUBLICADO';
    $('pac_obac').value = obac ?? '';
    $('pac_fuente').value = fuente ?? '-';
    $('pac_desc').value = desc ?? '';
    $('pac_estimado').value = estimado ?? '';
    openModal('modalForm');
  }
  window.openEdit = openEdit;

  // Delete
  function openDelete(id, nopac) {
    $('delPac').textContent = (nopac ?? '-') + ' (ID ' + (id ?? '-') + ')';
    openModal('modalDelete');
  }
  window.openDelete = openDelete;

  async function fakeSave() {
    const fd = new FormData();
    fd.append('nopac', $('pac_nopac').value);
    fd.append('pn', $('pac_pn').value);
    fd.append('estado', $('pac_estado').value);
    fd.append('descripcion', $('pac_desc').value);
    fd.append('obac', $('pac_obac').value);
    fd.append('fuente', $('pac_fuente').value);
    fd.append('estimado', $('pac_estimado').value);
    fd.append('periodo', new Date().getFullYear());

    try {
      const resp = await fetch('<?= BASE_URL ?>/admin/pac_guardar', {
        method: 'POST',
        body: fd
      });

      const data = await resp.json();

      if (!data.ok) {
        alert(data.msg || 'No se pudo guardar.');
        return;
      }

      alert(data.msg || 'Guardado correctamente.');
      window.location.reload();

    } catch (err) {
      alert('Error al guardar el PAC.');
      console.error(err);
    }
  }
  window.fakeSave = fakeSave;

  function fakeDelete() {
    alert('Eliminado (maqueta). Con BD, aquí harías POST al controlador.');
    closeModal('modalDelete');
  }
  window.fakeDelete = fakeDelete;
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>