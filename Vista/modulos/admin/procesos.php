<?php
// Archivo: Vista/modulos/admin/procesos.php

$titulo = 'Procesos';
$active = 'procesos';

require_once __DIR__ . '/../../../Config/config.php';
require_once __DIR__ . '/../../../Modelo/MdProcesoAdmin.php';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function fmt_money($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}

function obacTokens(string $texto): array
{
  $texto = trim($texto);
  if ($texto === '') {
    return [];
  }

  $parts = array_map('trim', explode('|', $texto));
  $parts = array_values(array_filter(array_unique($parts), fn($v) => $v !== ''));

  return $parts;
}

function tipoProcesoMeta(?string $tipo): array
{
  $tipo = strtoupper(trim((string)$tipo));

  return match ($tipo) {
    'CORPORATIVO' => [
      'label' => 'Corporativo',
      'class' => 'border-violet-200 bg-violet-50 text-violet-700',
      'dot'   => 'bg-violet-500',
    ],
    default => [
      'label' => 'Individual',
      'class' => 'border-sky-200 bg-sky-50 text-sky-700',
      'dot'   => 'bg-sky-500',
    ],
  };
}

function estadoProcesoClass(?string $estado): string
{
  $estado = mb_strtoupper(trim((string)$estado), 'UTF-8');

  return match ($estado) {
    'CONVOCADO'   => 'bg-amber-50 text-amber-700 border-amber-200',
    'ADJUDICADO'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'DESIERTO'    => 'bg-rose-50 text-rose-700 border-rose-200',
    'OBSERVADO'   => 'bg-orange-50 text-orange-700 border-orange-200',
    'PUBLICADO'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    default       => 'bg-slate-100 text-slate-700 border-slate-200',
  };
}

$filtros = [
  'periodo'      => isset($_GET['periodo']) ? (int)$_GET['periodo'] : null,
  'q'            => $_GET['q'] ?? '',
  'estado_id'    => $_GET['estado_id'] ?? '',
  'tipo_proceso' => $_GET['tipo_proceso'] ?? '',
];

$rows = MdProcesoAdmin::listar($filtros) ?? [];

$totalProcesos = count($rows);
$sumEstimado   = array_reduce($rows, fn($a, $r) => $a + (float)($r['estimado'] ?? 0), 0);
$anio          = (int)($filtros['periodo'] ?? ($rows[0]['periodo'] ?? date('Y')));

$totalCorporativos = 0;
$totalIndividuales = 0;

foreach ($rows as $r) {
  $tp = strtoupper(trim((string)($r['tipo_proceso'] ?? '')));
  if ($tp === 'CORPORATIVO') {
    $totalCorporativos++;
  } else {
    $totalIndividuales++;
  }
}
?>

<style>
  .glass-card {
    background:
      linear-gradient(180deg, rgba(255, 255, 255, .96) 0%, rgba(248, 250, 252, .96) 100%);
    box-shadow:
      0 10px 30px rgba(15, 23, 42, 0.06),
      0 1px 0 rgba(255, 255, 255, 0.7) inset;
  }

  .soft-table thead th {
    letter-spacing: .04em;
  }

  .soft-row:hover {
    background: linear-gradient(90deg, rgba(248, 250, 252, 1) 0%, rgba(255, 255, 255, 1) 100%);
  }

  .metric-card {
    position: relative;
    overflow: hidden;
  }

  .metric-card::after {
    content: "";
    position: absolute;
    inset: auto -30px -30px auto;
    width: 90px;
    height: 90px;
    border-radius: 9999px;
    background: rgba(148, 163, 184, .08);
  }

  .table-sticky-head thead th {
    position: sticky;
    top: 0;
    z-index: 5;
  }
</style>

<div class="space-y-6">

  <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
    <div class="space-y-1">
      <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
        Mantenimiento
      </div>
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Procesos</h1>
        <p class="mt-1 text-sm text-slate-500">
          Gestión consolidada de procesos individuales y corporativos
        </p>
      </div>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="<?= BASE_URL ?>/admin/procesos_nuevo"
        class="inline-flex h-11 items-center gap-2 rounded-2xl bg-slate-900 px-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-[1px] hover:bg-slate-800">
        <span class="text-base leading-none">＋</span>
        Nuevo
      </a>

      <button class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        <span>⤓</span>
        Exportar
      </button>

      <button class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        <span>⤒</span>
        Importar
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="metric-card glass-card rounded-3xl border border-slate-200 p-5">
      <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Total procesos</div>
      <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900"><?= (int)$totalProcesos ?></div>
      <div class="mt-2 text-xs text-slate-500">Registros visibles en la lista</div>
    </div>

    <div class="metric-card glass-card rounded-3xl border border-slate-200 p-5">
      <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Estimado total</div>
      <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900"><?= fmt_money($sumEstimado) ?></div>
      <div class="mt-2 text-xs text-slate-500">Monto acumulado de los procesos</div>
    </div>

    <div class="metric-card glass-card rounded-3xl border border-slate-200 p-5">
      <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Individuales</div>
      <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900"><?= (int)$totalIndividuales ?></div>
      <div class="mt-2 text-xs text-slate-500">Procesos con una sola OBAC</div>
    </div>

    <div class="metric-card glass-card rounded-3xl border border-slate-200 p-5">
      <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Corporativos</div>
      <div class="mt-2 text-3xl font-bold tracking-tight text-slate-900"><?= (int)$totalCorporativos ?></div>
      <div class="mt-2 text-xs text-slate-500">Procesos con varias OBAC involucradas</div>
    </div>
  </div>

  <div class="glass-card rounded-3xl border border-slate-200 p-4 sm:p-5">
    <div class="mb-4 flex flex-col gap-1">
      <h2 class="text-sm font-semibold text-slate-900">Filtros y búsqueda</h2>
      <p class="text-xs text-slate-500">Refina la visualización de la lista de procesos</p>
    </div>

    <form method="GET" class="grid grid-cols-1 gap-3 lg:grid-cols-12">
      <div class="lg:col-span-5">
        <label class="mb-1 block text-xs font-medium text-slate-500">Buscar</label>
        <input
          type="text"
          name="q"
          value="<?= h($filtros['q']) ?>"
          placeholder="Código, expediente, OBAC o descripción"
          class="h-11 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-slate-300 focus:bg-white">
      </div>

      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-medium text-slate-500">Estado</label>
        <select name="estado_id" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 outline-none transition focus:border-slate-300">
          <option value="">Todos</option>
        </select>
      </div>

      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-medium text-slate-500">Tipo</label>
        <select name="tipo_proceso" class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 outline-none transition focus:border-slate-300">
          <option value="">Todos</option>
          <option value="INDIVIDUAL" <?= strtoupper((string)$filtros['tipo_proceso']) === 'INDIVIDUAL' ? 'selected' : '' ?>>Individual</option>
          <option value="CORPORATIVO" <?= strtoupper((string)$filtros['tipo_proceso']) === 'CORPORATIVO' ? 'selected' : '' ?>>Corporativo</option>
        </select>
      </div>

      <div class="lg:col-span-2">
        <label class="mb-1 block text-xs font-medium text-slate-500">Año fiscal</label>
        <input
          type="number"
          name="periodo"
          value="<?= h((string)($filtros['periodo'] ?? '')) ?>"
          placeholder="2026"
          class="h-11 w-full rounded-2xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 outline-none transition focus:border-slate-300">
      </div>

      <div class="lg:col-span-1 flex items-end">
        <a href="<?= BASE_URL ?>/admin/procesos"
          class="inline-flex h-11 w-full items-center justify-center rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
          Limpiar
        </a>
      </div>
    </form>
  </div>

  <div class="glass-card overflow-hidden rounded-3xl border border-slate-200">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
      <div>
        <div class="flex items-center gap-2">
          <h2 class="text-sm font-semibold text-slate-900">Lista de procesos</h2>
          <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
            <?= (int)$totalProcesos ?>
          </span>
        </div>
        <p class="mt-1 text-xs text-slate-500">
          Vista compacta con información principal, tipo de proceso y acciones rápidas
        </p>
      </div>

      <div class="flex flex-wrap gap-2 text-xs">
        <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 font-semibold text-sky-700">
          <span class="h-2 w-2 rounded-full bg-sky-500"></span>
          Individual
        </span>
        <span class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 font-semibold text-violet-700">
          <span class="h-2 w-2 rounded-full bg-violet-500"></span>
          Corporativo
        </span>
      </div>
    </div>

    <div class="overflow-auto">
      <table class="soft-table table-sticky-head min-w-full text-left">
        <thead class="bg-slate-50/95 backdrop-blur">
          <tr class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
            <th class="px-4 py-4 sm:px-5">
              <input type="checkbox" class="h-4 w-4 rounded border-slate-300">
            </th>
            <th class="px-4 py-4 sm:px-5">Proceso</th>
            <th class="px-4 py-4">Tipo</th>
            <th class="px-4 py-4">Expediente</th>
            <th class="px-4 py-4">OBAC</th>
            <th class="px-4 py-4">Estado</th>
            <th class="px-4 py-4 text-right">Estimado</th>
            <th class="px-4 py-4 text-right sm:px-5">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): ?>
              <?php
              $tipoMeta   = tipoProcesoMeta($r['tipo_proceso'] ?? '');
              $obacs      = obacTokens((string)($r['obacs_involucrados'] ?? ''));
              $obacSimple = trim((string)($r['obac_nombre'] ?? ''));
              $estadoNom  = (string)($r['estado_nombre'] ?? '');
              ?>
              <tr class="soft-row transition">
                <td class="px-4 py-4 align-top sm:px-5">
                  <input type="checkbox" class="h-4 w-4 rounded border-slate-300">
                </td>

                <td class="px-4 py-4 align-top min-w-[280px]">
                  <div class="space-y-1">
                    <div class="font-semibold text-slate-900">
                      <?= h($r['codigo_proceso'] ?? '') ?>
                    </div>

                    <div class="line-clamp-2 text-sm leading-5 text-slate-500">
                      <?= h($r['descripcion'] ?? '') ?>
                    </div>
                  </div>
                </td>

                <td class="px-4 py-4 align-top">
                  <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold <?= $tipoMeta['class'] ?>">
                    <span class="h-2 w-2 rounded-full <?= $tipoMeta['dot'] ?>"></span>
                    <?= h($tipoMeta['label']) ?>
                  </span>
                </td>

                <td class="px-4 py-4 align-top">
                  <div class="text-sm font-medium text-slate-700">
                    <?= h($r['expediente'] ?? '') ?: '<span class="text-slate-400">—</span>' ?>
                  </div>
                </td>

                <td class="px-4 py-4 align-top min-w-[200px]">
                  <?php if (!empty($obacs)): ?>
                    <div class="flex flex-wrap gap-1.5">
                      <?php foreach ($obacs as $ob): ?>
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                          <?= h($ob) ?>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  <?php elseif ($obacSimple !== ''): ?>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                      <?= h($obacSimple) ?>
                    </span>
                  <?php else: ?>
                    <span class="text-xs text-slate-400">—</span>
                  <?php endif; ?>
                </td>

                <td class="px-4 py-4 align-top">
                  <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold <?= estadoProcesoClass($estadoNom) ?>">
                    <?= h($estadoNom !== '' ? $estadoNom : 'Sin estado') ?>
                  </span>
                </td>

                <td class="px-4 py-4 align-top text-right">
                  <div class="text-right text-sm font-semibold text-slate-900 whitespace-nowrap">
                    <?= 'S/ ' . number_format((float)$r['estimado'], 2, '.', ',') ?>
                  </div>
                </td>

                <td class="px-4 py-4 align-top text-right sm:px-5">
                  <div class="relative inline-block text-left">
                    <button type="button"
                      class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50"
                      data-menu-btn>
                      <span class="text-lg leading-none">⋯</span>
                    </button>

                    <div class="hidden absolute right-0 z-50 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
                      data-menu>

                      <a href="<?= BASE_URL ?>/admin/procesos_detalle?id=<?= (int)($r['id'] ?? 0) ?>"
                        class="block px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        👁 Ver detalle
                      </a>

                      <a href="<?= BASE_URL ?>/admin/procesos/editar?id=<?= (int)($r['id'] ?? 0) ?>"
                        class="block px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        ✏ Editar
                      </a>

                      <button type="button"
                        class="w-full px-4 py-3 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50"
                        data-del="<?= (int)($r['id'] ?? 0) ?>"
                        data-name="<?= h($r['codigo_proceso'] ?? '') ?>">
                        🗑 Eliminar
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="px-6 py-14 text-center">
                <div class="mx-auto max-w-md">
                  <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                    📁
                  </div>
                  <h3 class="text-base font-semibold text-slate-900">No hay procesos para mostrar</h3>
                  <p class="mt-1 text-sm text-slate-500">
                    Ajusta los filtros o registra un nuevo proceso para comenzar.
                  </p>
                  <div class="mt-5">
                    <a href="<?= BASE_URL ?>/admin/procesos_nuevo"
                      class="inline-flex h-10 items-center rounded-2xl bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                      Crear proceso
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
  const closeAllMenus = () => {
    document.querySelectorAll('[data-menu]').forEach(menu => menu.classList.add('hidden'));
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-menu-btn]');
    const insideMenu = e.target.closest('[data-menu]');

    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      const wrap = btn.closest('.relative') || btn.parentElement;
      const menu = wrap ? wrap.querySelector('[data-menu]') : null;
      const wasOpen = menu && !menu.classList.contains('hidden');

      closeAllMenus();

      if (menu && !wasOpen) {
        menu.classList.remove('hidden');
      }
      return;
    }

    if (insideMenu) return;
    closeAllMenus();
  });

  document.addEventListener('click', (e) => {
    const del = e.target.closest('[data-del]');
    if (!del) return;

    e.preventDefault();
    e.stopPropagation();

    const id = del.getAttribute('data-del');
    const name = del.getAttribute('data-name') || 'este registro';

    if (!confirm(`¿Eliminar ${name}? Esta acción no se puede deshacer.`)) return;
    window.location.href = `<?= BASE_URL ?>/admin/procesos/eliminar?id=${id}`;
  });
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>