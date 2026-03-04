<?php
// Archivo: Vista/modulos/admin/procesos.php  (LISTA ADMIN - CORREGIDO)

$titulo = 'Procesos';
$active = 'procesos';

require_once __DIR__ . '/../../../Config/config.php';
require_once __DIR__ . '/../../../Modelo/MdProceso.php';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function fmt_money($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}

$filtros = [
  'periodo' => isset($_GET['periodo']) ? (int)$_GET['periodo'] : null,
];

$rows = MdProceso::listar($filtros) ?? [];

$totalProcesos = count($rows);
$sumEstimado   = array_reduce($rows, fn($a, $r) => $a + (float)($r['estimado'] ?? 0), 0);

// AF: usa el filtro si existe, si no usa el periodo del primer registro, si no el año actual
$anio = (int)($filtros['periodo'] ?? ($rows[0]['periodo'] ?? date('Y')));
?>

<div class="space-y-6">

  <!-- HEADER -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <div class="text-xs text-slate-500 font-medium">Mantenimiento</div>
      <h1 class="text-xl font-semibold text-slate-900">Procesos</h1>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="<?= BASE_URL ?>/admin/procesos/nuevo"
        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        ＋ Nuevo
      </a>

      <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50">
        ⤓ Exportar
      </button>

      <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50">
        ⤒ Importar
      </button>
    </div>
  </div>

  <!-- KPIs -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5">
      <div class="text-xs text-slate-500 font-medium">Total procesos</div>
      <div class="mt-1 text-2xl font-bold text-slate-900"><?= (int)$totalProcesos ?></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
      <div class="text-xs text-slate-500 font-medium">Estimado total</div>
      <div class="mt-1 text-2xl font-bold text-slate-900"><?= fmt_money($sumEstimado) ?></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
      <div class="text-xs text-slate-500 font-medium">Año</div>
      <div class="mt-1 text-2xl font-semibold text-slate-900">AF-<?= (int)$anio ?></div>
    </div>
  </div>

  <!-- FILTROS (solo UI por ahora) -->
  <div class="rounded-2xl border border-slate-200 bg-white p-4">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">

      <div class="lg:col-span-5">
        <input type="text"
          placeholder="Buscar por proceso, expediente, OBAC o descripción"
          class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-900 focus:bg-white outline-none">
      </div>

      <div class="lg:col-span-2">
        <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium">
          <option>Estado (Todos)</option>
        </select>
      </div>

      <div class="lg:col-span-2">
        <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium">
          <option>OBAC (Todos)</option>
        </select>
      </div>

      <div class="lg:col-span-2">
        <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium">
          <option>AF (Todos)</option>
        </select>
      </div>

      <div class="lg:col-span-1">
        <button class="h-11 w-full rounded-xl border border-slate-200 bg-white text-sm font-semibold hover:bg-slate-50">
          Limpiar
        </button>
      </div>

    </div>
  </div>

  <!-- TABLA -->
  <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">

    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
      <div class="text-sm font-semibold text-slate-900">
        Lista
        <span class="ml-2 bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full font-medium">
          <?= (int)$totalProcesos ?>
        </span>
      </div>
    </div>

    <div class="overflow-auto">
      <table class="min-w-full text-left">
        <thead class="bg-slate-50">
          <tr class="text-xs font-semibold text-slate-600">
            <th class="px-4 py-3"></th>
            <th class="px-4 py-3">Proceso</th>
            <th class="px-4 py-3">Expediente</th>
            <th class="px-4 py-3">OBAC</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3 text-right">Estimado</th>
            <th class="px-4 py-3 text-right">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <?php foreach ($rows as $r): ?>
            <tr class="hover:bg-slate-50 transition">
              <td class="px-4 py-3">
                <input type="checkbox" class="h-4 w-4">
              </td>

              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900"><?= h($r['proceso'] ?? '') ?></div>
                <div class="text-xs text-slate-500 font-normal line-clamp-1">
                  <?= h($r['descripcion'] ?? '') ?>
                </div>
              </td>

              <td class="px-4 py-3 text-sm font-medium text-slate-700">
                <?= h($r['expediente'] ?? '') ?>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700">
                  <?= h($r['obac'] ?? '') ?>
                </span>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                  <?= h($r['estado'] ?? '') ?>
                </span>
              </td>

              <td class="px-4 py-3 text-right text-sm font-semibold text-slate-900">
                <?= fmt_money($r['estimado'] ?? 0) ?>
              </td>

              <td class="px-4 py-3 text-right">
                <div class="relative inline-block text-left">
                  <button type="button"
                    class="h-9 w-9 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition"
                    data-menu-btn>
                    <span class="text-lg leading-none text-slate-600">⋯</span>
                  </button>

                  <div class="hidden absolute right-0 mt-2 w-40 rounded-xl bg-white shadow-xl border border-slate-200 z-50"
                    data-menu>

                    <!-- ✅ CORREGIDO: Ver debe ir a DETALLE ADMIN -->
                    <a href="<?= BASE_URL ?>/admin/actividades?id=<?= (int)($r['id'] ?? 0) ?>"
                      class="block px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                      👁 Ver
                    </a>

                    <a href="<?= BASE_URL ?>/admin/procesos/editar?id=<?= (int)($r['id'] ?? 0) ?>"
                      class="block px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                      ✏ Editar
                    </a>

                    <button type="button"
                      class="w-full text-left px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50"
                      data-del="<?= (int)($r['id'] ?? 0) ?>"
                      data-name="<?= h($r['proceso'] ?? '') ?>">
                      🗑 Eliminar
                    </button>

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

<script>
  const closeAllMenus = () => {
    document.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-menu-btn]');
    const insideMenu = e.target.closest('[data-menu]');

    if (btn) {
      e.preventDefault();
      e.stopPropagation();
      const wrap = btn.closest('.relative') || btn.parentElement;
      const m = wrap?.querySelector('[data-menu]');
      const wasOpen = m && !m.classList.contains('hidden');
      closeAllMenus();
      if (m && !wasOpen) m.classList.remove('hidden');
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