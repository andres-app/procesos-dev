<?php
$titulo = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/../../layout/admin_layout.php';

function fmt_money_dashboard($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}

function pct_dashboard($parte, $total)
{
  if ((float)$total <= 0) {
    return 0;
  }

  return round(((float)$parte / (float)$total) * 100, 1);
}

function tono_alerta_dashboard($tono)
{
  $map = [
    'amber'   => 'border-amber-200 bg-amber-50 text-amber-800',
    'rose'    => 'border-rose-200 bg-rose-50 text-rose-800',
    'slate'   => 'border-slate-200 bg-slate-50 text-slate-800',
    'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
  ];

  return $map[$tono] ?? $map['slate'];
}

$totalPacGeneral   = (int)($kpis['total_pac'] ?? 0);
$totalMontoGeneral = (float)($kpis['total_estimado'] ?? 0);
$maxMontoMes       = 0;
$maxMontoDep       = 0;
$maxMontoObac      = 0;

foreach ($tendenciaMes as $item) {
  $maxMontoMes = max($maxMontoMes, (float)($item['monto'] ?? 0));
}
foreach ($topDependencias as $item) {
  $maxMontoDep = max($maxMontoDep, (float)($item['monto'] ?? 0));
}
foreach ($topObac as $item) {
  $maxMontoObac = max($maxMontoObac, (float)($item['monto'] ?? 0));
}
?>

<div class="space-y-6">

  <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
    <div>
      <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
        Panel gerencial
      </div>
      <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">
        Dashboard PAC
      </h1>
      <p class="mt-1 text-sm text-slate-500">
        Lectura ejecutiva de programación anual de contrataciones.
      </p>
    </div>

    <form method="GET" class="grid grid-cols-1 gap-2 sm:grid-cols-4">
      <select
        name="periodo"
        class="h-11 rounded-2xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
        <option value="">Periodo</option>
        <?php foreach ($periodos as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= ((string)($_GET['periodo'] ?? '') === (string)$p['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select
        name="obac"
        class="h-11 rounded-2xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
        <option value="">OBAC</option>
        <?php foreach ($obacs as $o): ?>
          <option value="<?= (int)$o['id'] ?>" <?= ((string)($_GET['obac'] ?? '') === (string)$o['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($o['nombre'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select
        name="estado"
        class="h-11 rounded-2xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200">
        <option value="">Estado</option>
        <?php foreach ($estados as $e): ?>
          <option value="<?= (int)$e['id'] ?>" <?= ((string)($_GET['estado'] ?? '') === (string)$e['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['nombre'], ENT_QUOTES, 'UTF-8') ?>
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

  <!-- KPIs -->
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-4">
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
      <div class="mt-2 text-sm text-slate-500">Registros con inversiones</div>
    </div>
  </div>

  <!-- Segunda fila -->
  <div class="grid grid-cols-1 gap-4 xl:grid-cols-2 2xl:grid-cols-4">

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Distribución</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Por estado</h2>
        </div>
        <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
          <?= count($porEstado) ?> grupos
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php if (!empty($porEstado)): ?>
          <?php foreach ($porEstado as $item): ?>
            <?php
              $nombre = (string)($item['nombre'] ?? '-');
              $total  = (int)($item['total'] ?? 0);
              $monto  = (float)($item['monto'] ?? 0);
              $pct    = pct_dashboard($total, $totalPacGeneral);
            ?>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
              <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?= $total ?> PAC</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-semibold text-slate-900"><?= $pct ?>%</div>
                  <div class="mt-1 text-xs text-slate-500"><?= fmt_money_dashboard($monto) ?></div>
                </div>
              </div>
              <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full bg-slate-900" style="width: <?= min($pct, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin datos para mostrar.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Participación</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Por OBAC</h2>
        </div>
        <div class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
          Monto estimado
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php if (!empty($porObac)): ?>
          <?php foreach ($porObac as $item): ?>
            <?php
              $nombre = (string)($item['nombre'] ?? '-');
              $total  = (int)($item['total'] ?? 0);
              $monto  = (float)($item['monto'] ?? 0);
              $pct    = pct_dashboard($monto, $totalMontoGeneral);
            ?>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
              <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?= $total ?> PAC</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-semibold text-slate-900"><?= $pct ?>%</div>
                  <div class="mt-1 text-xs text-slate-500"><?= fmt_money_dashboard($monto) ?></div>
                </div>
              </div>
              <div class="mt-3 h-2 overflow-hidden rounded-full bg-blue-100">
                <div class="h-full rounded-full bg-blue-600" style="width: <?= min($pct, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin datos para mostrar.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Clasificación</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Tipo de mercado</h2>
        </div>
        <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
          Nacional / Extranjero
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php if (!empty($porMercado)): ?>
          <?php foreach ($porMercado as $item): ?>
            <?php
              $nombre = (string)($item['nombre'] ?? '-');
              $total  = (int)($item['total'] ?? 0);
              $monto  = (float)($item['monto'] ?? 0);
              $pct    = pct_dashboard($monto, $totalMontoGeneral);
            ?>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
              <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?= $total ?> PAC</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-semibold text-slate-900"><?= $pct ?>%</div>
                  <div class="mt-1 text-xs text-slate-500"><?= fmt_money_dashboard($monto) ?></div>
                </div>
              </div>
              <div class="mt-3 h-2 overflow-hidden rounded-full bg-emerald-100">
                <div class="h-full rounded-full bg-emerald-600" style="width: <?= min($pct, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin datos para mostrar.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Modalidad</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Individual vs corporativo</h2>
        </div>
        <div class="rounded-full bg-violet-50 px-3 py-1 text-xs font-medium text-violet-700">
          Distribución
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php if (!empty($porModalidad)): ?>
          <?php foreach ($porModalidad as $item): ?>
            <?php
              $nombre = (string)($item['nombre'] ?? '-');
              $total  = (int)($item['total'] ?? 0);
              $monto  = (float)($item['monto'] ?? 0);
              $pct    = pct_dashboard($monto, $totalMontoGeneral);
            ?>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
              <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <div class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?= $total ?> PAC</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-semibold text-slate-900"><?= $pct ?>%</div>
                  <div class="mt-1 text-xs text-slate-500"><?= fmt_money_dashboard($monto) ?></div>
                </div>
              </div>
              <div class="mt-3 h-2 overflow-hidden rounded-full bg-violet-100">
                <div class="h-full rounded-full bg-violet-600" style="width: <?= min($pct, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin datos para mostrar.
          </div>
        <?php endif; ?>
      </div>
    </section>

  </div>

  <!-- Tercera fila -->
  <div class="grid grid-cols-1 gap-4 xl:grid-cols-2 2xl:grid-cols-4">

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)] 2xl:col-span-2">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tendencia</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Programación mensual</h2>
        </div>
        <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
          Mes convocatoria
        </div>
      </div>

      <div class="mt-5 space-y-4">
        <?php if (!empty($tendenciaMes)): ?>
          <?php foreach ($tendenciaMes as $item): ?>
            <?php
              $nombre = (string)($item['nombre'] ?? '-');
              $total  = (int)($item['total'] ?? 0);
              $monto  = (float)($item['monto'] ?? 0);
              $width  = $maxMontoMes > 0 ? round(($monto / $maxMontoMes) * 100, 1) : 0;
            ?>
            <div>
              <div class="mb-1.5 flex items-center justify-between gap-3">
                <div class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-xs text-slate-500"><?= $total ?> PAC · <?= fmt_money_dashboard($monto) ?></div>
              </div>
              <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                <div class="h-full rounded-full bg-slate-900" style="width: <?= min($width, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin datos para mostrar.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Ranking</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Top dependencias</h2>
        </div>
        <div class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
          Top 5
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php if (!empty($topDependencias)): ?>
          <?php foreach ($topDependencias as $i => $item): ?>
            <?php
              $nombre = (string)($item['nombre'] ?? '-');
              $total  = (int)($item['total'] ?? 0);
              $monto  = (float)($item['monto'] ?? 0);
              $width  = $maxMontoDep > 0 ? round(($monto / $maxMontoDep) * 100, 1) : 0;
            ?>
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
              <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">
                  <?= $i + 1 ?>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?= $total ?> PAC · <?= fmt_money_dashboard($monto) ?></div>
                </div>
              </div>
              <div class="mt-3 h-2 overflow-hidden rounded-full bg-amber-100">
                <div class="h-full rounded-full bg-amber-500" style="width: <?= min($width, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin datos para mostrar.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Control</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Alertas gerenciales</h2>
        </div>
        <div class="rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700">
          Atención
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php if (!empty($alertas)): ?>
          <?php foreach ($alertas as $alerta): ?>
            <div class="rounded-2xl border px-4 py-3 <?= tono_alerta_dashboard($alerta['tono'] ?? 'slate') ?>">
              <div class="flex items-center justify-between gap-3">
                <div class="text-sm font-semibold">
                  <?= htmlspecialchars((string)($alerta['titulo'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="text-xl font-bold">
                  <?= (int)($alerta['valor'] ?? 0) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
            Sin alertas.
          </div>
        <?php endif; ?>
      </div>

      <div class="mt-5 border-t border-slate-100 pt-4">
        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Top OBAC</div>
        <div class="mt-3 space-y-3">
          <?php if (!empty($topObac)): ?>
            <?php foreach ($topObac as $i => $item): ?>
              <?php
                $nombre = (string)($item['nombre'] ?? '-');
                $monto  = (float)($item['monto'] ?? 0);
                $width  = $maxMontoObac > 0 ? round(($monto / $maxMontoObac) * 100, 1) : 0;
              ?>
              <div>
                <div class="mb-1 flex items-center justify-between gap-3">
                  <div class="truncate text-sm font-medium text-slate-700"><?= ($i + 1) . '. ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="text-xs text-slate-500"><?= fmt_money_dashboard($monto) ?></div>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-blue-100">
                  <div class="h-full rounded-full bg-blue-600" style="width: <?= min($width, 100) ?>%;"></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-sm text-slate-500">Sin datos.</div>
          <?php endif; ?>
        </div>
      </div>
    </section>

  </div>

</div>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>