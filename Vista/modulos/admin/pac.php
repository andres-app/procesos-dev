<?php
// Vista/modulos/admin/pac.php
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
                    '<?= h($r['obac'] ?? '') ?>',
                    '<?= h($r['seleccion'] ?? '') ?>',
                    '<?= h($r['fuente'] ?? '') ?>',
                    '<?= h($r['descripcion']) ?>',
                    '<?= h($r['estimado']) ?>',
                    '<?= h($r['periodo'] ?? '') ?>',
                    '<?= h($r['lista'] ?? '') ?>',
                    '<?= h($r['ejecucion'] ?? '') ?>',
                    '<?= h($r['modalidad'] ?? '') ?>',
                    '<?= h($r['dependencia'] ?? '') ?>',
                    '<?= h($r['mesconvoca'] ?? '') ?>',
                    '<?= h($r['certificado'] ?? '') ?>',
                    '<?= h($r['tipo_mercado'] ?? '') ?>',
                    '<?= h($r['cantidad'] ?? '') ?>',
                    '<?= h($r['rubro'] ?? '') ?>'
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
<!-- Modal (Nuevo/Editar) -->
<div id="modalForm" class="fixed inset-0 hidden items-center justify-center p-4 z-50">
  <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalForm')"></div>

  <div class="relative w-full max-w-5xl h-[90vh] rounded-[28px] border border-slate-200 bg-white shadow-soft overflow-hidden flex flex-col">

    <!-- Header -->
    <div class="shrink-0 px-5 py-4 border-b border-slate-200 bg-white">
      <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
          <div class="text-xs text-slate-400 uppercase tracking-wide">PAC</div>
          <div id="modalTitle" class="text-[32px] leading-none font-semibold text-slate-900 mt-1">
            Nuevo PAC
          </div>
        </div>

        <button
          type="button"
          class="rounded-full border border-slate-200 bg-white px-5 py-2 text-sm text-slate-700 hover:bg-slate-50"
          onclick="closeModal('modalForm')">
          Cerrar
        </button>
      </div>
    </div>

    <!-- Body -->
    <div class="flex-1 overflow-y-auto px-5 py-5">
      <form id="pacForm" class="grid grid-cols-1 md:grid-cols-6 gap-x-4 gap-y-5" onsubmit="return false;">
        <input type="hidden" id="pac_id" value="">

        <!-- DATOS PRINCIPALES -->
        <div class="md:col-span-6">
          <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
            Datos principales
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">N° PAC</label>
          <input
            id="pac_nopac"
            class="field"
            placeholder="Ej: 0043"
            autocomplete="off">
        </div>

        <div class="md:col-span-1">
          <label class="block text-xs text-slate-500 mb-1.5">P/NP</label>
          <select id="pac_pn" class="field">
            <option value="P">P</option>
            <option value="NP">NP</option>
          </select>
        </div>

        <div class="md:col-span-1">
          <label class="block text-xs text-slate-500 mb-1.5">Estado</label>
          <select id="pac_estado" class="field">
            <option value="PUBLICADO">PUBLICADO</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">Fuente</label>
          <select id="pac_fuente" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($fuentes as $f): ?>
              <option value="<?= (int)$f['id'] ?>"><?= h($f['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-6">
          <div class="flex items-end justify-between gap-2">
            <label class="block text-xs text-slate-500 mb-1.5">Descripción</label>
            <div class="text-[11px] text-slate-400">
              <span id="descCount">0</span>/400
            </div>
          </div>
          <textarea
            id="pac_desc"
            rows="3"
            maxlength="400"
            class="field min-h-[110px] resize-none"
            placeholder="Describe el requerimiento..."></textarea>
        </div>

        <!-- CLASIFICACIÓN -->
        <div class="md:col-span-6 pt-1">
          <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
            Clasificación
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">OBAC</label>
          <select id="pac_obac" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($obacs as $o): ?>
              <option value="<?= (int)$o['id'] ?>"><?= h($o['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">Selección</label>
          <select id="pac_seleccion" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($selecciones as $s): ?>
              <option value="<?= (int)$s['id'] ?>"><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">Lista</label>
          <select id="pac_lista" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($listas as $l): ?>
              <option value="<?= (int)$l['id'] ?>"><?= h($l['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Modalidad</label>
          <select id="pac_modalidad" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($modalidades as $m): ?>
              <option value="<?= (int)$m['id'] ?>"><?= h($m['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Tipo mercado</label>
          <select id="pac_tipo_mercado" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($tipos_mercado as $t): ?>
              <option value="<?= (int)$t['id'] ?>"><?= h($t['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Rubro</label>
          <select id="pac_rubro" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($rubros as $rubro): ?>
              <option value="<?= (int)$rubro['id'] ?>"><?= h($rubro['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- ORGANIZACIÓN -->
        <div class="md:col-span-6 pt-1">
          <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
            Organización
          </div>
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Ejecución</label>
          <select id="pac_ejecucion" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($entidades as $e): ?>
              <option value="<?= (int)$e['id'] ?>"><?= h($e['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Dependencia</label>
          <select id="pac_dependencia" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($dependencias as $d): ?>
              <option value="<?= (int)$d['id'] ?>"><?= h($d['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- PROGRAMACIÓN Y MONTOS -->
        <div class="md:col-span-6 pt-1">
          <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
            Programación y montos
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">Mes convocatoria</label>
          <select id="pac_mes_convocatoria" class="field">
            <option value="">Seleccionar...</option>
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">Periodo</label>
          <select id="pac_periodo" class="field">
            <option value="">Seleccionar...</option>
            <?php foreach ($periodos as $p): ?>
              <option value="<?= (int)$p['id'] ?>"><?= h($p['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-500 mb-1.5">Cantidad</label>
          <input
            id="pac_cantidad"
            type="number"
            min="0"
            step="1"
            class="field"
            placeholder="0">
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Estimado (S/.)</label>
          <input
            id="pac_estimado"
            type="number"
            min="0"
            step="0.01"
            inputmode="decimal"
            class="field"
            placeholder="Ej: 90000.00"
            autocomplete="off">
          <div class="mt-1 text-[11px] text-slate-400">
            Vista: <span id="sum_estimado">S/ 0.00</span>
          </div>
        </div>

        <div class="md:col-span-3">
          <label class="block text-xs text-slate-500 mb-1.5">Certificado</label>
          <input
            id="pac_certificado"
            type="number"
            min="0"
            step="0.01"
            inputmode="decimal"
            class="field"
            placeholder="Ej: 45000.00"
            autocomplete="off">
        </div>
      </form>
    </div>

    <!-- Footer -->
    <div class="shrink-0 px-5 py-4 border-t border-slate-200 bg-white">
      <div class="flex items-center justify-end gap-2">
        <button
          type="button"
          class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm hover:bg-slate-50"
          onclick="closeModal('modalForm')">
          Cancelar
        </button>

        <button
          id="btnSavePac"
          type="button"
          class="rounded-2xl bg-slate-900 text-white px-5 py-2.5 text-sm font-medium hover:bg-slate-800"
          onclick="fakeSave()">
          Guardar
        </button>
      </div>
    </div>
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

  .field {
    width: 100%;
    height: 44px;
    border-radius: 18px;
    border: 1px solid rgb(226, 232, 240);
    background: #fff;
    padding: 0 14px;
    font-size: 14px;
    color: rgb(15, 23, 42);
    outline: none;
    transition: .15s ease;
  }

  .field:focus {
    border-color: rgb(148, 163, 184);
    box-shadow: 0 0 0 3px rgba(148, 163, 184, .18);
  }

  textarea.field {
    height: auto;
    padding-top: 12px;
    padding-bottom: 12px;
  }

  #modalForm .overflow-y-auto {
    scrollbar-gutter: stable;
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
    const estEl = $('pac_estimado');
    const descEl = $('pac_desc');

    const sumEstimadoEl = $('sum_estimado');
    if (sumEstimadoEl) {
      sumEstimadoEl.textContent = fmtMoney(estEl?.value || '0');
    }

    const descCountEl = $('descCount');
    if (descCountEl) {
      const d = descEl?.value || '';
      descCountEl.textContent = String(d.length);
    }
  }

  [
    'pac_nopac',
    'pac_pn',
    'pac_obac',
    'pac_seleccion',
    'pac_fuente',
    'pac_estado',
    'pac_desc',
    'pac_estimado',
    'pac_periodo',
    'pac_lista',
    'pac_ejecucion',
    'pac_modalidad',
    'pac_dependencia',
    'pac_mes_convocatoria',
    'pac_certificado',
    'pac_tipo_mercado',
    'pac_cantidad',
    'pac_rubro'
  ].forEach((id) => {
    $(id)?.addEventListener('input', refreshSummary);
    $(id)?.addEventListener('change', refreshSummary);
  });

  $('btnNew')?.addEventListener('click', () => {
    $('modalTitle').textContent = 'Nuevo PAC';
    $('pac_id').value = '';
    $('pac_nopac').value = '';
    $('pac_pn').value = 'NP';
    $('pac_estado').value = 'PUBLICADO';
    $('pac_fuente').value = '';
    $('pac_desc').value = '';
    $('pac_obac').value = '';
    $('pac_seleccion').value = '';
    $('pac_lista').value = '';
    $('pac_modalidad').value = '';
    $('pac_tipo_mercado').value = '';
    $('pac_rubro').value = '';
    $('pac_ejecucion').value = '';
    $('pac_dependencia').value = '';
    $('pac_mes_convocatoria').value = '';
    $('pac_periodo').value = '';
    $('pac_cantidad').value = '';
    $('pac_estimado').value = '';
    $('pac_certificado').value = '';
    openModal('modalForm');
  });

  function openEdit(
    id,
    nopac,
    pn,
    estado,
    obac,
    seleccion,
    fuente,
    desc,
    estimado,
    periodo,
    lista,
    ejecucion,
    modalidad,
    dependencia,
    mesconvoca,
    certificado,
    tipo_mercado,
    cantidad,
    rubro
  ) {
    $('pac_id').value = id ?? '';
    $('pac_nopac').value = nopac ?? '';
    $('pac_pn').value = pn ?? 'NP';
    $('pac_estado').value = estado ?? 'PUBLICADO';
    $('pac_obac').value = obac ?? '';
    $('pac_seleccion').value = seleccion ?? '';
    $('pac_fuente').value = fuente ?? '';
    $('pac_desc').value = desc ?? '';
    $('pac_estimado').value = estimado ?? '';
    $('pac_periodo').value = periodo ?? '';
    $('pac_lista').value = lista ?? '';
    $('pac_ejecucion').value = ejecucion ?? '';
    $('pac_modalidad').value = modalidad ?? '';
    $('pac_dependencia').value = dependencia ?? '';
    $('pac_mes_convocatoria').value = mesconvoca ?? '';
    $('pac_certificado').value = certificado ?? '';
    $('pac_tipo_mercado').value = tipo_mercado ?? '';
    $('pac_cantidad').value = cantidad ?? '';
    $('pac_rubro').value = rubro ?? '';
    openModal('modalForm');
  }
  window.openEdit = openEdit;

  function openDelete(id, nopac) {
    $('delPac').textContent = (nopac ?? '-') + ' (ID ' + (id ?? '-') + ')';
    openModal('modalDelete');
  }
  window.openDelete = openDelete;

  async function fakeSave() {
    const btn = document.getElementById('btnSavePac');
    const textoOriginal = btn ? btn.textContent : 'Guardar';

    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Guardando...';
    }

    const fd = new FormData();
    fd.append('id', document.getElementById('pac_id').value);
    fd.append('nopac', document.getElementById('pac_nopac').value.trim());
    fd.append('pn', document.getElementById('pac_pn').value);
    fd.append('estado', document.getElementById('pac_estado').value);
    fd.append('descripcion', document.getElementById('pac_desc').value.trim());
    fd.append('obac', document.getElementById('pac_obac').value);
    fd.append('seleccion', document.getElementById('pac_seleccion').value);
    fd.append('fuente', document.getElementById('pac_fuente').value);
    fd.append('estimado', document.getElementById('pac_estimado').value);
    fd.append('periodo', document.getElementById('pac_periodo').value);
    fd.append('lista', document.getElementById('pac_lista').value);
    fd.append('ejecucion', document.getElementById('pac_ejecucion').value);
    fd.append('modalidad', document.getElementById('pac_modalidad').value);
    fd.append('dependencia', document.getElementById('pac_dependencia').value);
    fd.append('mesconvoca', document.getElementById('pac_mes_convocatoria').value);
    fd.append('certificado', document.getElementById('pac_certificado').value);
    fd.append('tipo_mercado', document.getElementById('pac_tipo_mercado').value);
    fd.append('cantidad', document.getElementById('pac_cantidad').value);
    fd.append('rubro', document.getElementById('pac_rubro').value);

    try {
      const resp = await fetch('<?= BASE_URL ?>/admin/pac_guardar', {
        method: 'POST',
        body: fd
      });

      const data = await resp.json();

      if (!data.ok) {
        showToast(data.msg || 'No se pudo guardar.', 'error', 'Error');
        if (btn) {
          btn.disabled = false;
          btn.textContent = textoOriginal;
        }
        return;
      }

      closeModal('modalForm');

      showToast(
        data.msg || 'PAC guardado correctamente.',
        'success',
        'Correcto'
      );

      setTimeout(() => {
        window.location.reload();
      }, 700);

    } catch (err) {
      showToast('Error al guardar el PAC.', 'error', 'Error');
      console.error(err);

      if (btn) {
        btn.disabled = false;
        btn.textContent = textoOriginal;
      }
    }
  }
  window.fakeSave = fakeSave;

  function fakeDelete() {
    showToast('Eliminado correctamente.', 'success', 'Correcto');
    closeModal('modalDelete');
  }
  window.fakeDelete = fakeDelete;
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>