<?php
// Vista/modulos/admin/pac.php
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
    'violet' => 'bg-violet-50 text-violet-700 border-violet-200',
  ];
  $c = $map[$tone] ?? $map['slate'];
  return '<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium ' . $c . '">' . $txt . '</span>';
}

function toneEstado($estado)
{
  $e = strtoupper(trim((string)$estado));

  if ($e === 'PUBLICADO') return 'green';
  if ($e === 'SOLICITADO') return 'slate';
  if ($e === 'REITERADO') return 'blue';
  if ($e === 'RECEPCIONADO') return 'blue';
  if ($e === 'OBSERVADO') return 'amber';
  if ($e === 'SUBSANADO') return 'blue';
  if ($e === 'APROBADO') return 'green';
  if ($e === 'ESTUDIO DE MERCADO') return 'rose';

  return 'slate';
}

function esModalidadExcluida(array $row, int $modalidadExcluidaId = 4): bool
{
  return (int)($row['modalidad'] ?? 0) === $modalidadExcluidaId;
}

// ===== KPIs =====
// Modalidad excluida NO debe considerarse en el total PAC
$modalidadExcluidaId = isset($modalidadExcluidaId) ? (int)$modalidadExcluidaId : 4;

$cntP = 0;
$cntNP = 0;
$sumP = 0.0;
$sumNP = 0.0;
$cntExcluida = 0;
$sumExcluida = 0.0;

foreach ($pacs as $row) {
  $pn  = strtoupper(trim((string)($row['pn'] ?? 'NP')));
  $est = (float)($row['estimado'] ?? 0);

  if (esModalidadExcluida($row, $modalidadExcluidaId)) {
    $cntExcluida++;
    $sumExcluida += $est;
    continue;
  }

  if ($pn === 'P') {
    $cntP++;
    $sumP += $est;
  } else {
    $cntNP++;
    $sumNP += $est;
  }
}

$totalCnt = $cntP + $cntNP;
$totalSum = $sumP + $sumNP;
?>

<div class="pac-page space-y-5">

  <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
      <div class="text-xs text-slate-500">Administrador</div>
      <h1 class="text-xl font-semibold leading-tight tracking-tight">PAC</h1>
      <div class="text-xs text-slate-500">Mantenimiento</div>
    </div>

    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
      <button
        id="btnFilters"
        type="button"
        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-[13px] text-slate-700 transition hover:bg-slate-50">
        Filtros
      </button>

      <button
        id="btnImport"
        type="button"
        class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-[13px] font-medium text-blue-700 transition hover:bg-blue-100">
        Importación masiva
      </button>

      <button
        id="btnNew"
        type="button"
        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-[13px] font-medium text-white transition hover:bg-slate-800">
        + Nuevo PAC
      </button>
    </div>
  </div>

  <?php
  $hayFiltrosActivos =
    !empty($filtros['q']) ||
    !empty($filtros['pn']) ||
    !empty($filtros['estado']) ||
    !empty($filtros['periodo']) ||
    !empty($filtros['obac']) ||
    (!empty($filtros['ejecucion']) && (string)$filtros['ejecucion'] !== '0') ||
    !empty($filtros['inversiones']) ||
    !empty($filtros['vraem']) ||
    !empty($filtros['modalidad_excluida']);
  ?>

  <?php if ($hayFiltrosActivos): ?>
    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <span class="font-semibold">Filtros activos</span>
          <span class="text-blue-700">
            <?= !empty($filtros['q']) ? ' | Búsqueda: ' . h($filtros['q']) : '' ?>
            <?= !empty($filtros['pn']) ? ' | P/NP: ' . h($filtros['pn']) : '' ?>
            <?= !empty($filtros['estado']) ? ' | Estado aplicado' : '' ?>
            <?= !empty($filtros['periodo']) ? ' | Periodo aplicado' : '' ?>
            <?= !empty($filtros['obac']) ? ' | OBAC aplicado' : '' ?>
            <?= (!empty($filtros['ejecucion']) && (string)$filtros['ejecucion'] !== '0') ? ' | ACFFAA aplicado' : '' ?>
            <?= !empty($filtros['vraem']) ? ' | VRAEM aplicado' : '' ?>
            <?= !empty($filtros['inversiones']) ? ' | Inversiones aplicado' : '' ?>
            <?= !empty($filtros['modalidad_excluida']) ? ' | Modalidad excluida' : '' ?>
          </span>
        </div>

        <a
          href="<?= BASE_URL ?>/admin/pac"
          class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-white px-3 py-2 text-[13px] text-blue-700 transition hover:bg-blue-100">
          Limpiar
        </a>
      </div>
    </div>
  <?php endif; ?>

  <?php
  $totalCntSafe = max(1, (int)$totalCnt);
  $totalSumSafe = max(0.01, (float)$totalSum);

  $porcP   = $totalCnt > 0 ? ($cntP / $totalCnt) * 100 : 0;
  $porcNP  = $totalCnt > 0 ? ($cntNP / $totalCnt) * 100 : 0;

  $porcMontoP  = $totalSum > 0 ? ($sumP / $totalSum) * 100 : 0;
  $porcMontoNP = $totalSum > 0 ? ($sumNP / $totalSum) * 100 : 0;

  $grupoDominanteCantidad = $cntP >= $cntNP ? 'Programables' : 'No Programables';
  $grupoDominanteMonto    = $sumP >= $sumNP ? 'Programables' : 'No Programables';
  ?>

  <!-- KPIs -->
  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">

    <!-- TOTAL GENERAL (DESTACADO) -->
    <div class="rounded-3xl border border-green-200 bg-green-50 p-5 shadow-soft">
      <div class="text-xs font-semibold uppercase tracking-wide text-green-600">Total PAC</div>
      <div class="mt-2 text-3xl font-bold text-green-900"><?= (int)$totalCnt ?></div>
      <div class="mt-3 text-sm font-medium text-green-700">
        S/ <?= number_format($totalSum, 2, '.', ',') ?>
      </div>
    </div>

    <!-- PROGRAMABLES -->
    <div class="rounded-3xl border border-blue-200 bg-blue-50 p-5 shadow-soft">
      <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">Programables</div>
      <div class="mt-2 text-3xl font-bold text-blue-900"><?= (int)$cntP ?></div>
      <div class="mt-2 text-sm text-blue-700">
        <?= $totalCnt > 0 ? number_format(($cntP / $totalCnt) * 100, 1) . '%' : '0%' ?>
      </div>
      <div class="mt-3 text-xs text-blue-700">
        S/ <?= number_format($sumP, 2, '.', ',') ?>
      </div>
    </div>

    <!-- NO PROGRAMABLES -->
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-soft">
      <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">No Programables</div>
      <div class="mt-2 text-3xl font-bold text-slate-900"><?= (int)$cntNP ?></div>
      <div class="mt-2 text-sm text-slate-600">
        <?= $totalCnt > 0 ? number_format(($cntNP / $totalCnt) * 100, 1) . '%' : '0%' ?>
      </div>
      <div class="mt-3 text-xs text-slate-600">
        S/ <?= number_format($sumNP, 2, '.', ',') ?>
      </div>
    </div>

  </div>
  <?php
  function buildFilterUrl(array $changes = []): string
  {
    $query = $_GET;

    foreach ($changes as $k => $v) {
      if ($v === null || $v === '') {
        unset($query[$k]);
      } else {
        $query[$k] = $v;
      }
    }

    return '?' . http_build_query($query);
  }

  $vraemActivo             = !empty($filtros['vraem']);
  $inversionesActivo       = !empty($filtros['inversiones']);
  $modalidadExcluidaActiva = !empty($filtros['modalidad_excluida']);
  $ejecucionActual         = (string)($filtros['ejecucion'] ?? '');

  $acffaaActivo = $ejecucionActual === '4' && !$vraemActivo && !$inversionesActivo && !$modalidadExcluidaActiva;
  $todosActivo  = !$acffaaActivo && !$vraemActivo && !$inversionesActivo && !$modalidadExcluidaActiva && ($ejecucionActual === '0' || $ejecucionActual === '');

  $urlAcffaa = h(buildFilterUrl([
    'ejecucion'           => 4,
    'vraem'               => null,
    'inversiones'         => null,
    'modalidad_excluida'  => null,
  ]));

  $urlVraem = h(buildFilterUrl([
    'ejecucion'           => null,
    'vraem'               => 1,
    'inversiones'         => null,
    'modalidad_excluida'  => null,
  ]));

  $urlInversiones = h(buildFilterUrl([
    'ejecucion'           => null,
    'vraem'               => null,
    'inversiones'         => 1,
    'modalidad_excluida'  => null,
  ]));

  $urlModalidadExcluida = h(buildFilterUrl([
    'ejecucion'           => null,
    'vraem'               => null,
    'inversiones'         => null,
    'modalidad_excluida'  => 1,
  ]));

  $urlTodos = h(buildFilterUrl([
    'ejecucion'           => 0,
    'vraem'               => null,
    'inversiones'         => null,
    'modalidad_excluida'  => null,
  ]));
  ?>

  <div class="mb-3 flex flex-wrap items-center gap-2">
    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
      Filtros rápidos:
    </span>

    <a
      href="<?= $urlAcffaa ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $acffaaActivo
                                                                                                          ? 'border-rose-300 bg-rose-600 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-rose-200 hover:text-rose-700' ?>">
      ACFFAA
    </a>

    <a
      href="<?= $urlVraem ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $vraemActivo
                                                                                                          ? 'border-amber-300 bg-amber-500 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-amber-200 hover:text-amber-700' ?>">
      VRAEM
    </a>

    <a
      href="<?= $urlInversiones ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $inversionesActivo
                                                                                                          ? 'border-emerald-300 bg-emerald-600 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-emerald-200 hover:text-emerald-700' ?>">
      INVERSIONES
    </a>

    <a
      href="<?= $urlModalidadExcluida ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $modalidadExcluidaActiva
                                                                                                          ? 'border-violet-300 bg-violet-600 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-violet-200 hover:text-violet-700' ?>">
      EXCLUIDOS
    </a>

    <a
      href="<?= $urlTodos ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $todosActivo
                                                                                                          ? 'border-slate-300 bg-slate-900 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:text-slate-900' ?>">
      TODOS
    </a>
  </div>

  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
      <div class="font-semibold">PAC registrados</div>
    </div>

    <div class="datatable-shell">
      <div class="w-full overflow-x-auto">
        <table id="tblPac" class="display w-full opacity-0">
          <thead>
            <tr>
              <th>N° PAC</th>
              <th>P/NP</th>
              <th>Descripción</th>
              <th>OBAC</th>
              <th>Fuente</th>
              <th>Modalidad</th>
              <th>Estado</th>
              <th>Estimado</th>
              <th class="text-right">Acciones</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($pacs as $r): ?>
              <?php $rowEsExcluida = esModalidadExcluida($r, $modalidadExcluidaId ?? 4); ?>
              <tr>
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
                  <?= pill(
                    h($r['modalidad_nombre'] ?? '-'),
                    $rowEsExcluida ? 'violet' : 'slate'
                  ) ?>
                </td>

                <td class="px-4 py-3">
                  <span title="<?= h($r['estado_nombre'] ?? '-') ?>">
                    <?= pill(
                      h($r['estado_codigo'] ?? $r['estado_nombre_pac'] ?? '-'),
                      toneEstado($r['estado_codigo'] ?? $r['estado_nombre_pac'] ?? '')
                    ) ?>
                  </span>
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
                  '<?= h($r['rubro'] ?? '') ?>',
                  '<?= h($r['inversiones'] ?? '') ?>'
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
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal filtros -->
  <div id="modalFilters" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalFilters')"></div>

    <div class="relative flex w-full max-w-2xl flex-col overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-soft">
      <div class="border-b border-slate-200 px-5 py-4">
        <div class="text-xs uppercase tracking-wide text-slate-400">PAC</div>
        <div class="mt-1 text-2xl font-semibold text-slate-900">Filtros</div>
        <div class="mt-1 text-sm text-slate-500">Aplica filtros para la tabla de PAC.</div>
      </div>

      <form method="GET" action="<?= BASE_URL ?>/admin/pac" class="space-y-5 px-5 py-5">
        <?php if (!empty($filtros['ejecucion'])): ?>
          <input type="hidden" name="ejecucion" value="<?= h($filtros['ejecucion']) ?>">
        <?php endif; ?>

        <?php if (!empty($filtros['vraem'])): ?>
          <input type="hidden" name="vraem" value="<?= h($filtros['vraem']) ?>">
        <?php endif; ?>

        <?php if (!empty($filtros['inversiones'])): ?>
          <input type="hidden" name="inversiones" value="<?= h($filtros['inversiones']) ?>">
        <?php endif; ?>

        <?php if (!empty($filtros['modalidad_excluida'])): ?>
          <input type="hidden" name="modalidad_excluida" value="<?= h($filtros['modalidad_excluida']) ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs text-slate-500">Buscar</label>
            <input
              type="text"
              name="q"
              value="<?= h($filtros['q'] ?? '') ?>"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200"
              placeholder="N° PAC, descripción, OBAC, modalidad o estado">
          </div>

          <div>
            <label class="mb-1.5 block text-xs text-slate-500">P/NP</label>
            <select
              name="pn"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Todos</option>
              <option value="P" <?= (($filtros['pn'] ?? '') === 'P') ? 'selected' : '' ?>>P</option>
              <option value="NP" <?= (($filtros['pn'] ?? '') === 'NP') ? 'selected' : '' ?>>NP</option>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-xs text-slate-500">Estado</label>
            <select
              name="estado"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Todos</option>
              <?php foreach ($estados as $es): ?>
                <option
                  value="<?= (int)$es['id'] ?>"
                  <?= ((string)($filtros['estado'] ?? '') === (string)$es['id']) ? 'selected' : '' ?>>
                  <?= h($es['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-xs text-slate-500">Periodo</label>
            <select
              name="periodo"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Todos</option>
              <?php foreach ($periodos as $p): ?>
                <option
                  value="<?= (int)$p['id'] ?>"
                  <?= ((string)($filtros['periodo'] ?? '') === (string)$p['id']) ? 'selected' : '' ?>>
                  <?= h($p['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="mb-1.5 block text-xs text-slate-500">OBAC</label>
            <select
              name="obac"
              class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
              <option value="">Todos</option>
              <?php foreach ($obacs as $o): ?>
                <option
                  value="<?= (int)$o['id'] ?>"
                  <?= ((string)($filtros['obac'] ?? '') === (string)$o['id']) ? 'selected' : '' ?>>
                  <?= h($o['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
          <a
            href="<?= BASE_URL ?>/admin/pac"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
            Limpiar filtros
          </a>

          <button
            type="button"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50"
            onclick="closeModal('modalFilters')">
            Cancelar
          </button>

          <button
            type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
            Aplicar filtros
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- El resto de modales y JS se mantiene igual -->
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
    transition: opacity .18s ease;
  }

  .pac-page .datatable-shell {
    border-top: 1px solid rgb(226 232 240);
    background: linear-gradient(to bottom, rgba(248, 250, 252, .95), rgba(255, 255, 255, 1) 56px);
  }

  .pac-page .dataTables_wrapper {
    padding: 14px 16px 16px;
  }

  .pac-page .dataTables_wrapper .top,
  .pac-page .dataTables_wrapper .bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .pac-page .dataTables_wrapper .top {
    margin-bottom: 14px;
  }

  .pac-page .dataTables_wrapper .bottom {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid rgb(241 245 249);
  }

  .pac-page .dataTables_wrapper .dataTables_length,
  .pac-page .dataTables_wrapper .dataTables_filter,
  .pac-page .dataTables_wrapper .dataTables_info,
  .pac-page .dataTables_wrapper .dataTables_paginate {
    float: none !important;
    margin: 0 !important;
  }

  .pac-page .dataTables_wrapper .dataTables_length label,
  .pac-page .dataTables_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 12px;
    font-weight: 600;
    color: rgb(100 116 139);
  }

  .pac-page .dataTables_wrapper .dataTables_length select {
    appearance: none;
    min-width: 86px;
    height: 38px;
    border-radius: 14px;
    border: 1px solid rgb(226 232 240);
    background: #fff;
    padding: 0 34px 0 12px;
    font-size: 13px;
    color: rgb(15 23 42);
    outline: none;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .03);
  }

  .pac-page .dataTables_wrapper .dataTables_filter input {
    width: 280px !important;
    max-width: 100%;
    height: 40px;
    border-radius: 14px;
    border: 1px solid rgb(226 232 240);
    background: #fff;
    margin-left: 0 !important;
    padding: 0 14px;
    font-size: 13px;
    color: rgb(15 23 42);
    outline: none;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .03);
    transition: all .18s ease;
  }

  .pac-page .dataTables_wrapper .dataTables_filter input:focus,
  .pac-page .dataTables_wrapper .dataTables_length select:focus {
    border-color: rgb(148 163 184);
    box-shadow: 0 0 0 4px rgba(148, 163, 184, .14);
  }

  .pac-page table.dataTable {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0;
    margin: 0 !important;
  }

  .pac-page table.dataTable.no-footer {
    border-bottom: 0 !important;
  }

  .pac-page table.dataTable thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: rgb(248 250 252) !important;
    color: rgb(71 85 105) !important;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    border-bottom: 1px solid rgb(226 232 240) !important;
    padding: 14px 16px !important;
    white-space: nowrap;
  }

  .pac-page table.dataTable tbody td {
    vertical-align: middle;
    padding: 14px 16px !important;
    border-bottom: 1px solid rgb(241 245 249) !important;
    font-size: 13px;
    color: rgb(15 23 42);
    white-space: nowrap;
    background: #fff;
  }

  .pac-page table.dataTable tbody tr:hover td {
    background: rgb(248 250 252);
  }

  .pac-page table.dataTable tbody tr:last-child td {
    border-bottom: 0 !important;
  }

  .pac-page table.dataTable tbody td:nth-child(1) {
    font-weight: 700;
    color: rgb(15 23 42);
  }

  .pac-page table.dataTable tbody td:nth-child(3) {
    white-space: normal;
    min-width: 360px;
    max-width: 560px;
  }

  .pac-page table.dataTable tbody td:nth-child(8) {
    font-variant-numeric: tabular-nums;
    font-weight: 700;
    color: rgb(15 23 42);
  }

  .pac-page .dataTables_scrollHead,
  .pac-page .dataTables_scrollBody {
    border: 0 !important;
  }

  .pac-page .dataTables_empty {
    padding: 36px 16px !important;
    text-align: center !important;
    color: rgb(100 116 139) !important;
    font-size: 13px;
  }

  .pac-page .dataTables_processing {
    border: 0 !important;
    border-radius: 18px !important;
    background: rgba(255, 255, 255, .92) !important;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08) !important;
    color: rgb(15 23 42) !important;
  }

  .pac-page .dataTables_wrapper .dataTables_info {
    font-size: 12px;
    color: rgb(100 116 139);
    padding-top: 0 !important;
  }

  .pac-page .dataTables_wrapper .dataTables_paginate {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-top: 0 !important;
  }

  .pac-page .dataTables_wrapper .paginate_button {
    min-width: 38px;
    height: 38px;
    border-radius: 12px !important;
    border: 1px solid transparent !important;
    background: #fff !important;
    color: rgb(51 65 85) !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    margin: 0 !important;
    padding: 0 12px !important;
    font-size: 13px;
    font-weight: 600;
    transition: all .15s ease;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .03);
  }

  .pac-page .dataTables_wrapper .paginate_button:hover {
    border-color: rgb(226 232 240) !important;
    background: rgb(248 250 252) !important;
    color: rgb(15 23 42) !important;
  }

  .pac-page .dataTables_wrapper .paginate_button.current,
  .pac-page .dataTables_wrapper .paginate_button.current:hover {
    border-color: rgb(15 23 42) !important;
    background: rgb(15 23 42) !important;
    color: #fff !important;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .16);
  }

  .pac-page .dataTables_wrapper .paginate_button.disabled,
  .pac-page .dataTables_wrapper .paginate_button.disabled:hover {
    opacity: .45;
    cursor: not-allowed !important;
    background: #fff !important;
    border-color: transparent !important;
    color: rgb(148 163 184) !important;
  }

  .pac-page .dt-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .pac-page .dt-buttons-wrap {
    display: flex;
    align-items: center;
  }

  .pac-page .dt-button,
  .pac-page button.dt-button,
  .pac-page div.dt-button,
  .pac-page a.dt-button {
    min-height: 42px;
    border-radius: 14px !important;
    border: 1px solid rgba(203, 213, 225, .9) !important;
    background:
      linear-gradient(180deg, rgba(255, 255, 255, .96) 0%, rgba(248, 250, 252, .96) 100%) !important;
    color: rgb(15 23 42) !important;
    padding: 0 14px !important;
    margin: 0 !important;
    box-shadow:
      0 1px 2px rgba(15, 23, 42, .04),
      0 8px 24px rgba(15, 23, 42, .06);
    transition: all .18s ease !important;
  }

  .pac-page .dt-button:hover,
  .pac-page button.dt-button:hover,
  .pac-page div.dt-button:hover,
  .pac-page a.dt-button:hover {
    border-color: rgba(148, 163, 184, .95) !important;
    background:
      linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(241, 245, 249, 1) 100%) !important;
    color: rgb(15 23 42) !important;
    box-shadow:
      0 1px 2px rgba(15, 23, 42, .05),
      0 12px 28px rgba(15, 23, 42, .10);
    transform: translateY(-1px);
  }

  .pac-page .dt-button:active,
  .pac-page button.dt-button:active,
  .pac-page div.dt-button:active,
  .pac-page a.dt-button:active {
    transform: translateY(0);
    box-shadow:
      0 1px 2px rgba(15, 23, 42, .04),
      0 6px 16px rgba(15, 23, 42, .08);
  }

  .pac-page .dt-button:focus,
  .pac-page button.dt-button:focus,
  .pac-page div.dt-button:focus,
  .pac-page a.dt-button:focus {
    outline: none !important;
    box-shadow:
      0 0 0 4px rgba(148, 163, 184, .14),
      0 8px 24px rgba(15, 23, 42, .08) !important;
  }

  .pac-page .dt-btn-inner {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: -.01em;
  }

  .pac-page .dt-btn-icon {
    width: 16px;
    height: 16px;
    flex: 0 0 16px;
    opacity: .88;
  }

  @media (max-width: 1024px) {
    .pac-page table.dataTable tbody td:nth-child(3) {
      min-width: 300px;
      max-width: 420px;
    }
  }

  @media (max-width: 640px) {
    .pac-page .dataTables_wrapper {
      padding: 12px;
    }

    .pac-page .dataTables_wrapper .top,
    .pac-page .dataTables_wrapper .bottom {
      flex-direction: column;
      align-items: stretch;
    }

    .pac-page .dataTables_wrapper .dataTables_filter input,
    .pac-page .dataTables_wrapper .dataTables_length select {
      width: 100% !important;
    }

    .pac-page .dataTables_wrapper .dataTables_paginate {
      justify-content: flex-start !important;
      flex-wrap: wrap;
    }

    .pac-page table.dataTable thead th,
    .pac-page table.dataTable tbody td {
      padding: 12px 12px !important;
    }

    .pac-page table.dataTable tbody td:nth-child(3) {
      min-width: 260px;
      max-width: 320px;
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
    'pac_rubro',
    'pac_inversiones'
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
    $('pac_inversiones').value = '';
    openModal('modalForm');
  });

  $('btnImport')?.addEventListener('click', () => {
    $('csv_file').value = '';
    $('csvFileName').textContent = 'Ningún archivo seleccionado';
    $('importResult').className = 'hidden rounded-2xl border px-4 py-3 text-sm';
    $('importResult').innerHTML = '';
    openModal('modalImport');
  });

  $('btnFilters')?.addEventListener('click', () => {
    openModal('modalFilters');
  });

  $('csv_file')?.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    $('csvFileName').textContent = file ? file.name : 'Ningún archivo seleccionado';
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
    rubro,
    inversiones
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
    $('pac_inversiones').value = inversiones ?? '';

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
    fd.append('inversiones', $('pac_inversiones').value);

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
      showToast(data.msg || 'PAC guardado correctamente.', 'success', 'Correcto');

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

  async function importCsvPac() {
    const fileInput = $('csv_file');
    const resultBox = $('importResult');
    const btn = $('btnSendImport');
    const file = fileInput?.files?.[0];

    if (!file) {
      showToast('Selecciona un archivo CSV.', 'error', 'Error');
      return;
    }

    const original = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Importando...';

    resultBox.className = 'hidden rounded-2xl border px-4 py-3 text-sm';
    resultBox.innerHTML = '';

    const fd = new FormData();
    fd.append('csv_file', file);

    try {
      const resp = await fetch('<?= BASE_URL ?>/admin/pac_importar_csv', {
        method: 'POST',
        body: fd
      });

      const rawText = await resp.text();
      let data = null;

      try {
        data = JSON.parse(rawText);
      } catch (jsonError) {
        console.error('Respuesta no válida del servidor:', rawText);

        resultBox.className = 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
        resultBox.innerHTML = `
          <div class="font-semibold">Error del servidor</div>
          <div class="mt-1">La respuesta no es un JSON válido.</div>

          <div class="mt-2 rounded-xl bg-white/70 p-3 font-mono text-[12px] text-slate-700 max-h-40 overflow-auto">
            ${String(rawText || 'Respuesta vacía del servidor.').replace(/</g, '&lt;').replace(/>/g, '&gt;')}
          </div>

          <div class="mt-4 flex gap-2">
            <button
              type="button"
              class="rounded-xl bg-slate-900 px-4 py-2 text-white text-sm"
              onclick="importCsvPac()">
              Reintentar
            </button>

            <button
              type="button"
              class="rounded-xl border border-slate-300 px-4 py-2 text-sm"
              onclick="closeModal('modalImport')">
              Cerrar
            </button>
          </div>
        `;

        showToast('El servidor devolvió una respuesta inválida.', 'error', 'Error');
        btn.disabled = false;
        btn.textContent = original;
        return;
      }

      if (!data.ok) {
        resultBox.className = 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
        resultBox.innerHTML = `
          <div class="font-semibold">No se pudo importar</div>
          <div class="mt-1">${data.msg || 'Ocurrió un error en la importación.'}</div>

          ${Array.isArray(data.errores) && data.errores.length
            ? `<ul class="mt-2 list-disc pl-5 max-h-40 overflow-auto">${data.errores.map(x => `<li>${x}</li>`).join('')}</ul>`
            : ''
          }

          <div class="mt-4 flex gap-2">
            <button
              type="button"
              class="rounded-xl bg-slate-900 px-4 py-2 text-white text-sm"
              onclick="importCsvPac()">
              Reintentar
            </button>

            <button
              type="button"
              class="rounded-xl border border-slate-300 px-4 py-2 text-sm"
              onclick="closeModal('modalImport')">
              Cerrar
            </button>
          </div>
        `;

        showToast(data.msg || 'No se pudo importar el CSV.', 'error', 'Error');
        btn.disabled = false;
        btn.textContent = original;
        return;
      }

      resultBox.className = 'rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700';
      resultBox.innerHTML = `
        <div class="font-semibold">Importación completada</div>
        <div class="mt-1">${data.msg || 'Proceso finalizado correctamente.'}</div>

        <div class="mt-2">
          <strong>Insertados:</strong> ${data.insertados ?? 0}
          ${typeof data.omitidos !== 'undefined' ? ` | <strong>Omitidos:</strong> ${data.omitidos}` : ''}
        </div>

        ${Array.isArray(data.errores) && data.errores.length
          ? `<ul class="mt-2 list-disc pl-5 max-h-40 overflow-auto">${data.errores.map(x => `<li>${x}</li>`).join('')}</ul>`
          : ''
        }

        <div class="mt-4 flex gap-2">
          <button
            type="button"
            class="rounded-xl bg-slate-900 px-4 py-2 text-white text-sm"
            onclick="location.reload()">
            Actualizar tabla
          </button>

          <button
            type="button"
            class="rounded-xl border border-slate-300 px-4 py-2 text-sm"
            onclick="closeModal('modalImport')">
            Cerrar
          </button>
        </div>
      `;

      showToast(data.msg || 'CSV importado correctamente.', 'success', 'Correcto');
      btn.disabled = false;
      btn.textContent = original;

    } catch (err) {
      console.error(err);
      resultBox.className = 'rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
      resultBox.innerHTML = `
        <div class="font-semibold">Error</div>
        <div class="mt-1">Ocurrió un error al procesar la importación.</div>

        <div class="mt-4 flex gap-2">
          <button
            type="button"
            class="rounded-xl bg-slate-900 px-4 py-2 text-white text-sm"
            onclick="importCsvPac()">
            Reintentar
          </button>

          <button
            type="button"
            class="rounded-xl border border-slate-300 px-4 py-2 text-sm"
            onclick="closeModal('modalImport')">
            Cerrar
          </button>
        </div>
      `;
      showToast('Error al importar el CSV.', 'error', 'Error');
      btn.disabled = false;
      btn.textContent = original;
    }
  }

  window.importCsvPac = importCsvPac;

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

  function initPacDataTable() {
    if (typeof jQuery === 'undefined' || !jQuery.fn.DataTable) return;

    const $table = jQuery('#tblPac');
    if (!$table.length) return;

    if (jQuery.fn.DataTable.isDataTable('#tblPac')) {
      $table.DataTable().destroy();
    }

    $table.DataTable({
      autoWidth: false,
      responsive: false,
      pageLength: 10,
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, 'Todos']
      ],
      order: [
        [0, 'asc']
      ],
      language: {
        lengthMenu: 'Mostrar _MENU_',
        search: 'Buscar:',
        searchPlaceholder: 'N° PAC, descripción, OBAC...',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        infoEmpty: 'Mostrando 0 a 0 de 0 registros',
        infoFiltered: '(filtrado de _MAX_ registros)',
        zeroRecords: 'No se encontraron coincidencias',
        emptyTable: 'No hay registros disponibles',
        paginate: {
          first: '«',
          last: '»',
          next: '›',
          previous: '‹'
        },
        buttons: {
          excel: 'Excel',
          pdf: 'PDF'
        }
      },
      columnDefs: [{
        targets: 8,
        orderable: false,
        searchable: false,
        className: 'text-right'
      }],
      dom: "<'top flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between'\
      <'flex flex-col sm:flex-row sm:items-center gap-2'\
        <'dataTables_length'l>\
        <'dt-buttons-wrap'B>\
      >\
      <'dataTables_filter'f>\
    >" +
        "rt" +
        "<'bottom'<'dataTables_info'i><'dataTables_paginate'p>>",
      buttons: [{
          extend: 'excelHtml5',
          text: `
      <span class="dt-btn-inner">
        <svg viewBox="0 0 24 24" class="dt-btn-icon" aria-hidden="true">
          <path fill="currentColor" d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8zm0 1.5L18.5 8H14zM9.7 16.8l1.55-2.3-1.45-2.3h1.52l.77 1.35.8-1.35h1.47l-1.47 2.28 1.57 2.32h-1.54l-.88-1.4-.89 1.4z"/>
        </svg>
        <span>Excel</span>
      </span>
    `,
          title: 'PAC_registrados',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7]
          }
        },
        {
          extend: 'pdfHtml5',
          text: `
      <span class="dt-btn-inner">
        <svg viewBox="0 0 24 24" class="dt-btn-icon" aria-hidden="true">
          <path fill="currentColor" d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8zm0 1.5L18.5 8H14zM8.8 16.8v-4.6h2.08c1.02 0 1.72.66 1.72 1.6 0 .96-.7 1.62-1.72 1.62h-.82v1.4zm1.26-2.46h.63c.4 0 .67-.24.67-.56 0-.34-.27-.56-.67-.56h-.63zm3.36 2.46v-4.6h1.88c1.38 0 2.28.9 2.28 2.3s-.9 2.3-2.28 2.3zm1.26-1.08h.46c.68 0 1.1-.45 1.1-1.22s-.42-1.22-1.1-1.22h-.46zm3.52 1.08v-4.6h3.08v1.05h-1.82v.78h1.64v1.02h-1.64v1.75z"/>
        </svg>
        <span>PDF</span>
      </span>
    `,
          title: 'PAC_registrados',
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7]
          }
        }
      ],
      initComplete: function() {
        const wrapper = $table.closest('.dataTables_wrapper');

        wrapper.find('.dataTables_filter input')
          .attr('placeholder', 'Buscar por N° PAC, descripción, OBAC o modalidad...')
          .attr('autocomplete', 'off');

        $table.addClass('dt-ready').removeClass('opacity-0');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    initPacDataTable();
  });
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>