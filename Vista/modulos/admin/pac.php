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
  $e = strtoupper(trim((string)$estado));
  if ($e === 'PUBLICADO') return 'green';
  if ($e === 'OBSERVADO') return 'amber';
  if ($e === 'SUBSANADO') return 'blue';
  if ($e === 'SOLICITADO') return 'slate';
  if ($e === 'ESTUDIO DE MERCADO') return 'rose';
  return 'slate';
}
?>

<?php
// ===== KPIs (Programables / No programables) =====
$cntP  = 0;
$cntNP = 0;
$sumP  = 0.0;
$sumNP = 0.0;

foreach ($pacs as $row) {
  $pn  = strtoupper(trim((string)($row['pn'] ?? 'NP')));
  $est = (float)($row['estimado'] ?? 0);

  if ($pn === 'P') {
    $cntP++;
    $sumP += $est;
  } else {
    $cntNP++;
    $sumNP += $est;
  }
}
?>

<div class="pac-page space-y-5">

  <!-- Header -->
  <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
      <div class="text-xs text-slate-500">Administrador</div>
      <h1 class="text-xl font-semibold leading-tight tracking-tight">PAC</h1>
      <div class="text-xs text-slate-500">Mantenimiento</div>
    </div>

    <div class="flex flex-col gap-2 sm:flex-row">
      <div class="relative">
        <input
          placeholder="Buscar por N° PAC, OBAC, descripción…"
          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 pr-9 text-[13px] text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200 sm:w-80" />
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">⌕</span>
      </div>

      <button
        type="button"
        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-[13px] text-slate-700 transition hover:bg-slate-50">
        Filtros
      </button>

      <button
        id="btnNew"
        type="button"
        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-[13px] font-medium text-white transition hover:bg-slate-800">
        + Nuevo PAC
      </button>
    </div>
  </div>

  <!-- KPIs -->
  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
      <div class="text-xs text-slate-500">Total PAC Programables</div>
      <div class="mt-1 text-2xl font-semibold"><?= (int)$cntP ?></div>
      <div class="mt-2 text-xs text-slate-500">P</div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
      <div class="text-xs text-slate-500">Total PAC No Programables</div>
      <div class="mt-1 text-2xl font-semibold"><?= (int)$cntNP ?></div>
      <div class="mt-2 text-xs text-slate-500">NP</div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
      <div class="text-xs text-slate-500">Estimado total Programables</div>
      <div class="mt-1 text-2xl font-semibold">
        S/ <?= number_format($sumP, 2, '.', ',') ?>
      </div>
      <div class="mt-2 text-xs text-slate-500">Suma de estimados (P)</div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-soft">
      <div class="text-xs text-slate-500">Estimado total No Programables</div>
      <div class="mt-1 text-2xl font-semibold">
        S/ <?= number_format($sumNP, 2, '.', ',') ?>
      </div>
      <div class="mt-2 text-xs text-slate-500">Suma de estimados (NP)</div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
      <div class="font-semibold">PAC registrados</div>
    </div>

    <div class="w-full overflow-x-auto">
      <table id="tblPac" class="display w-full thc tbc opacity-0">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th>N° PAC</th>
            <th>P/NP</th>
            <th>Descripción</th>
            <th>OBAC</th>
            <th>Fuente</th>
            <th>Estado</th>
            <th>Estimado</th>
            <th class="text-right">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <?php foreach ($pacs as $r): ?>
            <tr class="hover:bg-slate-50">

              <td class="px-4 py-3 font-semibold text-slate-900">
                <?= h($r['nopac']) ?>
              </td>

              <td class="px-4 py-3">
                <?= pill(h($r['pn'] ?? 'NP'), ($r['pn'] ?? 'NP') === 'P' ? 'blue' : 'slate') ?>
              </td>

              <td class="w-full px-4 py-3">
                <div class="line-clamp-2 text-slate-900" title="<?= h($r['descripcion']) ?>">
                  <?= h($r['descripcion']) ?>
                </div>
              </td>

              <td class="px-4 py-3">
                <?= pill(h($r['obac_nombre'] ?? '-'), 'blue') ?>
              </td>

              <td class="px-4 py-3">
                <?= pill(h($r['fuente_nombre'] ?? '-'), 'amber') ?>
              </td>

              <td class="px-4 py-3">
                <?= pill(h($r['estado_nombre'] ?? '-'), toneEstado($r['estado_nombre'] ?? '')) ?>
              </td>

              <td class="whitespace-nowrap px-4 py-3">
                S/ <?= number_format((float)$r['estimado'], 2) ?>
              </td>

              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1.5">

                  <a
                    href="<?= BASE_URL ?>/admin/pac_detalle?id=<?= (int)$r['id'] ?>"
                    class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-xl border border-slate-300/60 bg-white text-slate-600 transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 active:scale-[.98]"
                    title="Ver detalle"
                    aria-label="Ver detalle PAC <?= h($r['nopac']) ?>">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" aria-hidden="true">
                      <path fill="currentColor"
                        d="M12 5c-5.5 0-9.5 4.5-10.8 6.2a1.3 1.3 0 0 0 0 1.6C2.5 14.5 6.5 19 12 19s9.5-4.5 10.8-6.2a1.3 1.3 0 0 0 0-1.6C21.5 9.5 17.5 5 12 5zm0 12c-4.4 0-7.7-3.4-9-5 1.3-1.6 4.6-5 9-5s7.7 3.4 9 5c-1.3 1.6-4.6 5-9 5zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm0 4.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                    </svg>
                  </a>

                  <div class="relative">
                    <button
                      type="button"
                      class="inline-flex h-[34px] w-[34px] items-center justify-center rounded-xl border border-slate-300/60 bg-white text-slate-600 transition hover:border-slate-400 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200 active:scale-[.98]"
                      data-menu-btn
                      title="Más acciones"
                      aria-label="Más acciones PAC <?= h($r['nopac']) ?>">
                      <svg viewBox="0 0 24 24" class="h-4 w-4" aria-hidden="true">
                        <path fill="currentColor"
                          d="M6 10.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" />
                      </svg>
                    </button>

                    <div
                      class="absolute right-0 top-[42px] z-30 hidden min-w-[170px] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-[0_20px_45px_rgba(15,23,42,.12)]"
                      data-menu>
                      <button
                        type="button"
                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-slate-900 transition hover:bg-slate-100"
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
                        ✏️ Editar
                      </button>

                      <button
                        type="button"
                        class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-rose-600 transition hover:bg-rose-50"
                        onclick="openDelete(<?= (int)$r['id'] ?>, '<?= h($r['nopac']) ?>')">
                        🗑️ Eliminar
                      </button>
                    </div>
                  </div>
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

  <!-- Modal (Nuevo/Editar) -->
  <div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalForm')"></div>

    <div class="relative flex h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-soft">

      <!-- Header -->
      <div class="shrink-0 border-b border-slate-200 bg-white px-5 py-4">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="text-xs uppercase tracking-wide text-slate-400">PAC</div>
            <div id="modalTitle" class="mt-1 text-[32px] font-semibold leading-none text-slate-900">
              Nuevo PAC
            </div>
          </div>

          <button
            type="button"
            class="rounded-full border border-slate-200 bg-white px-5 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
            onclick="closeModal('modalForm')">
            Cerrar
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="flex-1 overflow-y-auto px-5 py-5">
        <form id="pacForm" class="grid grid-cols-1 gap-x-4 gap-y-5 md:grid-cols-6" onsubmit="return false;">
          <input type="hidden" id="pac_id" value="">

          <div class="md:col-span-6">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
              Datos principales
            </div>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">N° PAC</label>
            <input
              id="pac_nopac"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
              placeholder="Ej: 0043"
              autocomplete="off">
          </div>

          <div class="md:col-span-1">
            <label class="mb-1.5 block text-xs text-slate-500">P/NP</label>
            <select id="pac_pn" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="P">P</option>
              <option value="NP">NP</option>
            </select>
          </div>

          <div class="md:col-span-1">
            <label class="mb-1.5 block text-xs text-slate-500">Estado</label>
            <select id="pac_estado" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($estados as $es): ?>
                <option value="<?= (int)$es['id'] ?>"><?= h($es['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Fuente</label>
            <select id="pac_fuente" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($fuentes as $f): ?>
                <option value="<?= (int)$f['id'] ?>"><?= h($f['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-6">
            <div class="flex items-end justify-between gap-2">
              <label class="mb-1.5 block text-xs text-slate-500">Descripción</label>
              <div class="text-[11px] text-slate-400">
                <span id="descCount">0</span>/400
              </div>
            </div>
            <textarea
              id="pac_desc"
              rows="3"
              maxlength="400"
              class="min-h-[110px] w-full resize-none rounded-2xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
              placeholder="Describe el requerimiento..."></textarea>
          </div>

          <div class="md:col-span-6 pt-1">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
              Clasificación
            </div>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">OBAC</label>
            <select id="pac_obac" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($obacs as $o): ?>
                <option value="<?= (int)$o['id'] ?>"><?= h($o['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Selección</label>
            <select id="pac_seleccion" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($selecciones as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= h($s['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Lista</label>
            <select id="pac_lista" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($listas as $l): ?>
                <option value="<?= (int)$l['id'] ?>"><?= h($l['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Modalidad</label>
            <select id="pac_modalidad" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($modalidades as $m): ?>
                <option value="<?= (int)$m['id'] ?>"><?= h($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Tipo mercado</label>
            <select id="pac_tipo_mercado" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($tipos_mercado as $t): ?>
                <option value="<?= (int)$t['id'] ?>"><?= h($t['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Rubro</label>
            <select id="pac_rubro" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($rubros as $rubro): ?>
                <option value="<?= (int)$rubro['id'] ?>"><?= h($rubro['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-6 pt-1">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
              Organización
            </div>
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Ejecución</label>
            <select id="pac_ejecucion" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($entidades as $e): ?>
                <option value="<?= (int)$e['id'] ?>"><?= h($e['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Dependencia</label>
            <select id="pac_dependencia" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($dependencias as $d): ?>
                <option value="<?= (int)$d['id'] ?>"><?= h($d['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-6 pt-1">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
              Programación y montos
            </div>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Mes convocatoria</label>
            <select id="pac_mes_convocatoria" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <option value="ENERO">ENERO</option>
              <option value="FEBRERO">FEBRERO</option>
              <option value="MARZO">MARZO</option>
              <option value="ABRIL">ABRIL</option>
              <option value="MAYO">MAYO</option>
              <option value="JUNIO">JUNIO</option>
              <option value="JULIO">JULIO</option>
              <option value="AGOSTO">AGOSTO</option>
              <option value="SEPTIEMBRE">SEPTIEMBRE</option>
              <option value="OCTUBRE">OCTUBRE</option>
              <option value="NOVIEMBRE">NOVIEMBRE</option>
              <option value="DICIEMBRE">DICIEMBRE</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Periodo</label>
            <select id="pac_periodo" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Seleccionar...</option>
              <?php foreach ($periodos as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= h($p['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Cantidad</label>
            <input
              id="pac_cantidad"
              type="number"
              min="0"
              step="1"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
              placeholder="0">
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Estimado (S/.)</label>
            <input
              id="pac_estimado"
              type="number"
              min="0"
              step="0.01"
              inputmode="decimal"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
              placeholder="Ej: 90000.00"
              autocomplete="off">
            <div class="mt-1 text-[11px] text-slate-400">
              Vista: <span id="sum_estimado">S/ 0.00</span>
            </div>
          </div>

          <div class="md:col-span-3">
            <label class="mb-1.5 block text-xs text-slate-500">Certificado</label>
            <input
              id="pac_certificado"
              type="number"
              min="0"
              step="0.01"
              inputmode="decimal"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
              placeholder="Ej: 45000.00"
              autocomplete="off">
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="shrink-0 border-t border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-end gap-2">
          <button
            type="button"
            class="rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm transition hover:bg-slate-50"
            onclick="closeModal('modalForm')">
            Cancelar
          </button>

          <button
            id="btnSavePac"
            type="button"
            class="rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800"
            onclick="fakeSave()">
            Guardar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal delete -->
  <div id="modalDelete" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalDelete')"></div>

    <div class="relative w-full max-w-md rounded-3xl border border-slate-200 glass shadow-soft">
      <div class="border-b border-slate-200 px-5 py-4">
        <div class="text-xs text-slate-500">Eliminar</div>
        <div class="text-lg font-semibold">Confirmación</div>
      </div>

      <div class="p-5 text-sm text-slate-700">
        ¿Eliminar el PAC <span id="delPac" class="font-semibold"></span>?
      </div>

      <div class="flex justify-end gap-2 p-5 pt-0">
        <button
          type="button"
          class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm transition hover:bg-slate-50"
          onclick="closeModal('modalDelete')">
          Cancelar
        </button>

        <button
          type="button"
          class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm text-rose-700 transition hover:bg-rose-100"
          onclick="fakeDelete()">
          Eliminar
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .pac-page .glass {
    background: rgba(255, 255, 255, .78);
    backdrop-filter: blur(10px);
  }

  .pac-page .shadow-soft {
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
  }

  .pac-page #modalForm .overflow-y-auto {
    scrollbar-gutter: stable;
  }

  .pac-page .dt-ready {
    opacity: 1 !important;
    transition: opacity .15s ease;
  }

  .pac-page table.dataTable {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0;
  }

  .pac-page table.dataTable thead th,
  .pac-page table.dataTable tbody td {
    vertical-align: middle;
    white-space: nowrap;
  }

  .pac-page table.dataTable tbody td:nth-child(3) {
    white-space: normal;
    min-width: 320px;
  }

  .pac-page .dataTables_scrollHead,
  .pac-page .dataTables_scrollBody {
    border: 0 !important;
  }

  .pac-page .dataTables_wrapper .dataTables_filter input,
  .pac-page .dataTables_wrapper .dataTables_length select {
    max-width: 100%;
  }

  .pac-page .dataTables_wrapper .dataTables_filter,
  .pac-page .dataTables_wrapper .dataTables_length {
    width: 100%;
  }

  .pac-page .dataTables_wrapper .dataTables_filter label,
  .pac-page .dataTables_wrapper .dataTables_length label {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    width: 100%;
  }

  @media (max-width: 640px) {
    .pac-page .dataTables_wrapper {
      padding: 10px;
    }

    .pac-page .dataTables_wrapper .dataTables_filter input {
      width: 100% !important;
      margin-left: 0 !important;
    }

    .pac-page .dataTables_wrapper .dataTables_length,
    .pac-page .dataTables_wrapper .dataTables_filter,
    .pac-page .dataTables_wrapper .dataTables_info,
    .pac-page .dataTables_wrapper .dataTables_paginate {
      text-align: left !important;
      float: none !important;
    }

    .pac-page .dataTables_wrapper .dataTables_paginate {
      justify-content: flex-start !important;
    }
  }
</style>

<script>
  const $ = (id) => document.getElementById(id);

  function openModal(id) {
    const el = $(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');

    if (id === 'modalForm') {
      refreshSummary();
    }
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
    $('pac_estado').value = '';
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
    $('modalTitle').textContent = 'Editar PAC';

    $('pac_id').value = id ?? '';
    $('pac_nopac').value = nopac ?? '';
    $('pac_pn').value = pn ?? 'NP';
    $('pac_estado').value = estado ?? '';
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
    $('pac_mes_convocatoria').value = (mesconvoca ?? '').trim();
    $('pac_certificado').value = certificado ?? '';
    $('pac_tipo_mercado').value = tipo_mercado ?? '';
    $('pac_cantidad').value = cantidad ?? '';
    $('pac_rubro').value = rubro ?? '';

    openModal('modalForm');
  }
  window.openEdit = openEdit;

  function openDelete(id, nopac) {
    $('delPac').textContent = nopac ?? '';
    $('modalDelete').dataset.id = String(id ?? '');
    openModal('modalDelete');
  }
  window.openDelete = openDelete;

  async function fakeSave() {
    const btn = $('btnSavePac');
    const textoOriginal = btn ? btn.textContent : 'Guardar';

    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Guardando...';
    }

    const fd = new FormData();
    fd.append('id', $('pac_id').value);
    fd.append('nopac', $('pac_nopac').value);
    fd.append('pn', $('pac_pn').value);
    fd.append('estado', $('pac_estado').value);
    fd.append('descripcion', $('pac_desc').value);
    fd.append('obac', $('pac_obac').value);
    fd.append('seleccion', $('pac_seleccion').value);
    fd.append('fuente', $('pac_fuente').value);
    fd.append('estimado', $('pac_estimado').value);
    fd.append('periodo', $('pac_periodo').value);
    fd.append('lista', $('pac_lista').value);
    fd.append('ejecucion', $('pac_ejecucion').value);
    fd.append('modalidad', $('pac_modalidad').value);
    fd.append('dependencia', $('pac_dependencia').value);
    fd.append('mesconvoca', $('pac_mes_convocatoria').value);
    fd.append('certificado', $('pac_certificado').value);
    fd.append('tipo_mercado', $('pac_tipo_mercado').value);
    fd.append('cantidad', $('pac_cantidad').value);
    fd.append('rubro', $('pac_rubro').value);

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

  function closeAllMenus() {
    document.querySelectorAll('[data-menu]').forEach((m) => {
      m.classList.add('hidden');
    });
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-menu-btn]');
    const menu = e.target.closest('[data-menu]');

    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      const wrap = btn.closest('.relative');
      const m = wrap?.querySelector('[data-menu]');
      const wasOpen = m && !m.classList.contains('hidden');

      closeAllMenus();

      if (m && !wasOpen) {
        m.classList.remove('hidden');
      }
      return;
    }

    if (menu) return;

    closeAllMenus();
  });
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>