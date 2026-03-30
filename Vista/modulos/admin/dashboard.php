<?php
$titulo = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/../../layout/admin_layout.php';

function fmt_money_dashboard($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}
?>

<div class="space-y-6">

  <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
    <div>
      <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
        Panel gerencial
      </div>
      <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">
        Dashboard PAC
      </h1>
      <p class="mt-1 text-sm text-slate-500">
        Resumen ejecutivo de programación anual de contrataciones.
      </p>
    </div>

    <form method="GET" class="grid grid-cols-1 gap-2 sm:grid-cols-4">
      <select
        name="periodo"
        class="h-11 rounded-2xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
        <option value="">Periodo</option>
        <?php foreach ($periodos as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= ((string)($_GET['periodo'] ?? '') === (string)$p['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select
        name="obac"
        class="h-11 rounded-2xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
        <option value="">OBAC</option>
        <?php foreach ($obacs as $o): ?>
          <option value="<?= (int)$o['id'] ?>" <?= ((string)($_GET['obac'] ?? '') === (string)$o['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($o['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select
        name="estado"
        class="h-11 rounded-2xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
        <option value="">Estado</option>
        <?php foreach ($estados as $e): ?>
          <option value="<?= (int)$e['id'] ?>" <?= ((string)($_GET['estado'] ?? '') === (string)$e['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <div class="flex gap-2">
        <button
          type="submit"
          class="flex-1 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
          Filtrar
        </button>

        <a
          href="<?= BASE_URL ?>/admin/dashboard"
          class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50">
          Limpiar
        </a>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="text-xs font-medium uppercase tracking-wide text-slate-400">Total PAC</div>
      <div class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">
        <?= (int)($kpis['total_pac'] ?? 0) ?>
      </div>
      <div class="mt-2 text-sm text-slate-500">Registros acumulados</div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="text-xs font-medium uppercase tracking-wide text-slate-400">Monto estimado</div>
      <div class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">
        <?= fmt_money_dashboard($kpis['total_estimado'] ?? 0) ?>
      </div>
      <div class="mt-2 text-sm text-slate-500">Suma total estimada</div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="text-xs font-medium uppercase tracking-wide text-slate-400">Monto certificado</div>
      <div class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">
        <?= fmt_money_dashboard($kpis['total_certificado'] ?? 0) ?>
      </div>
      <div class="mt-2 text-sm text-slate-500">Total certificado registrado</div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="text-xs font-medium uppercase tracking-wide text-slate-400">PAC con inversión</div>
      <div class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">
        <?= (int)($kpis['total_con_inversion'] ?? 0) ?>
      </div>
      <div class="mt-2 text-sm text-slate-500">Registros con campo inversiones</div>
    </div>

  </div>

  <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-5 py-6">
    <div class="text-sm font-semibold text-slate-700">Siguiente bloque</div>
    <div class="mt-1 text-sm text-slate-500">
      Después de validar estos KPIs, en el siguiente paso armamos la segunda fila:
      distribución por estado, OBAC, mercado y modalidad.
    </div>
  </div>
</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>