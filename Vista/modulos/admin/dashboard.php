<?php
// Vista/modulos/admin/dashboard.php
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

function badge_criticidad_dashboard(array $row): array
{
  $estado = strtoupper(trim((string)($row['estado_nombre'] ?? '')));
  $cert   = (float)($row['certificado'] ?? 0);
  $dep    = trim((string)($row['dependencia_nombre'] ?? ''));

  if (strpos($estado, 'OBSERV') !== false) {
    return ['Observado', 'bg-rose-50 text-rose-700 border-rose-200'];
  }

  if ($cert <= 0) {
    return ['Sin certificado', 'bg-amber-50 text-amber-700 border-amber-200'];
  }

  if ($dep === '') {
    return ['Sin dependencia', 'bg-slate-50 text-slate-700 border-slate-200'];
  }

  return ['Revisar', 'bg-blue-50 text-blue-700 border-blue-200'];
}

function buildDashboardFilterUrl(array $changes = []): string
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

function fmt_resumen_pac_dashboard(int $cantidad, float $monto): string
{
  return $cantidad . '/(' . number_format($monto, 2, '.', ',') . ')';
}

$totalPacGeneral   = (int)($kpis['total_pac'] ?? 0);
$totalMontoGeneral = (float)($kpis['total_estimado'] ?? 0);
$ejecucionActual   = isset($_GET['ejecucion']) ? (string)$_GET['ejecucion'] : '4';
$coberturaPct      = (float)($comparativo['cobertura_pct'] ?? 0);

$partAcffaaPac       = (int)($participacion['acffaa_pac'] ?? 0);
$partAcffaaMonto     = (float)($participacion['acffaa_monto'] ?? 0);
$partAcffaaPctPac    = (float)($participacion['acffaa_pct_pac'] ?? 0);
$partAcffaaPctMonto  = (float)($participacion['acffaa_pct_monto'] ?? 0);

$partRestoPac        = (int)($participacion['resto_pac'] ?? 0);
$partRestoMonto      = (float)($participacion['resto_monto'] ?? 0);
$partRestoPctPac     = (float)($participacion['resto_pct_pac'] ?? 0);
$partRestoPctMonto   = (float)($participacion['resto_pct_monto'] ?? 0);

$maxMontoDep  = 0;
$maxMontoObac = 0;

foreach (($topDependencias ?? []) as $item) {
  $maxMontoDep = max($maxMontoDep, (float)($item['monto'] ?? 0));
}
foreach (($topObac ?? []) as $item) {
  $maxMontoObac = max($maxMontoObac, (float)($item['monto'] ?? 0));
}

/* ===== datasets para charts ===== */
$chartParticipacionLabels  = [];
$chartParticipacionMontos  = [];
$chartParticipacionTotales = [];

foreach (($participacionPie ?? []) as $item) {
  $nombre = strtoupper(trim((string)($item['nombre'] ?? 'SIN DATO')));
  if ($nombre === '') {
    $nombre = 'SIN DATO';
  }

  $chartParticipacionLabels[]  = $nombre;
  $chartParticipacionMontos[]  = (float)($item['monto'] ?? 0);
  $chartParticipacionTotales[] = (int)($item['total'] ?? 0);
}

$chartEstadoLabels = [];
$chartEstadoTotals = [];
foreach (($porEstado ?? []) as $item) {
  $chartEstadoLabels[] = (string)($item['nombre'] ?? '-');
  $chartEstadoTotals[] = (int)($item['total'] ?? 0);
}

$chartObacLabels = [];
$chartObacMontos = [];
foreach (($porObac ?? []) as $item) {
  $chartObacLabels[] = (string)($item['nombre'] ?? '-');
  $chartObacMontos[] = (float)($item['monto'] ?? 0);
}

$chartMercadoLabels = [];
$chartMercadoTotals = [];
foreach (($porMercado ?? []) as $item) {
  $nombre = trim((string)($item['nombre'] ?? ''));
  if ($nombre === '') {
    continue;
  }

  $chartMercadoLabels[] = $nombre;
  $chartMercadoTotals[] = (int)($item['total'] ?? 0);
}

$chartModalidadLabels = [];
$chartModalidadTotals = [];
foreach (($porModalidad ?? []) as $item) {
  $nombre = trim((string)($item['nombre'] ?? ''));
  if ($nombre === '') {
    continue;
  }

  $chartModalidadLabels[] = $nombre;
  $chartModalidadTotals[] = (int)($item['total'] ?? 0);
}

$chartMesLabels  = [];
$chartMesMontos  = [];
$chartMesTotales = [];
foreach (($tendenciaMes ?? []) as $item) {
  $chartMesLabels[]  = (string)($item['nombre'] ?? '-');
  $chartMesMontos[]  = (float)($item['monto'] ?? 0);
  $chartMesTotales[] = (int)($item['total'] ?? 0);
}
?>

<div class="space-y-6 dashboard-shell">

  <!-- HERO / CABECERA -->
  <section class="hero-premium">
    <div class="hero-premium__glow hero-premium__glow--one"></div>
    <div class="hero-premium__glow hero-premium__glow--two"></div>

    <div class="relative z-[1] flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
      <div class="max-w-3xl">
        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
          Dashboard Principal
        </h1>

        <div class="mt-5 flex flex-wrap items-center gap-2">

          <!-- Chips -->
          <a href="<?= htmlspecialchars(buildDashboardFilterUrl(['ejecucion' => 4]), ENT_QUOTES, 'UTF-8') ?>"
            class="inline-flex items-center rounded-full border px-3.5 py-2 text-xs font-semibold transition <?= $ejecucionActual === '4' ? 'border-rose-300 bg-rose-600 text-white shadow-[0_8px_20px_rgba(225,29,72,.22)]' : 'border-slate-200 bg-white/85 text-slate-700 hover:border-rose-200 hover:text-rose-700' ?>">
            ACFFAA
          </a>

          <a href="<?= htmlspecialchars(buildDashboardFilterUrl(['ejecucion' => 0]), ENT_QUOTES, 'UTF-8') ?>"
            class="inline-flex items-center rounded-full border px-3.5 py-2 text-xs font-semibold transition <?= $ejecucionActual === '0' ? 'border-slate-300 bg-slate-900 text-white shadow-[0_8px_20px_rgba(15,23,42,.20)]' : 'border-slate-200 bg-white/85 text-slate-700 hover:border-slate-300 hover:text-slate-900' ?>">
            Todos
          </a>

          <!-- SELECT AÑO (nuevo) -->
          <form method="GET">
            <input type="hidden" name="ejecucion" value="<?= htmlspecialchars($ejecucionActual) ?>">

            <select name="periodo"
              onchange="this.form.submit()"
              class="ml-2 h-10 rounded-full border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-200">

              <?php foreach (($periodos ?? []) as $p): ?>
                <option value="<?= (int)$p['id'] ?>"
                  <?= ((string)($filtros['periodo'] ?? '') === (string)$p['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </form>

        </div>
      </div>
    </div>
  </section>
  <!-- KPI -->
  <section class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-4">
    <article class="metric-card metric-card--dark">
      <div class="metric-card__label">Total PAC</div>
      <div class="metric-card__value"><?= (int)($kpis['total_pac'] ?? 0) ?></div>
      <div class="metric-card__sub">Registros acumulados</div>
    </article>

    <article class="metric-card">
      <div class="metric-card__label">Monto estimado</div>
      <div class="metric-card__value"><?= fmt_money_dashboard($kpis['total_estimado'] ?? 0) ?></div>
      <div class="metric-card__sub">Suma total estimada</div>
    </article>

    <article class="metric-card">
      <div class="metric-card__label">Monto certificado</div>
      <div class="metric-card__value"><?= fmt_money_dashboard($kpis['total_certificado'] ?? 0) ?></div>
      <div class="metric-card__sub">Total certificado registrado</div>
    </article>

    <article class="metric-card">
      <div class="metric-card__label">PAC con inversión</div>
      <div class="metric-card__value"><?= (int)($kpis['total_con_inversion'] ?? 0) ?></div>
      <div class="metric-card__sub">Registros con inversiones</div>
    </article>
  </section>

  <!-- Participación -->
  <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
    <section class="premium-chart-card">
      <div class="premium-chart-head">
        <div>
          <div class="premium-kicker">Visual ejecutivo</div>
          <h2 class="premium-title">Participación sectorial</h2>
          <p class="premium-subtitle">Monto estimado entre ACFFAA y OBAC.</p>
        </div>
        <div class="premium-chip premium-chip-blue">Participación</div>
      </div>
      <div class="premium-chart-wrap premium-chart-wrap-donut">
        <canvas id="chartParticipacionPie"></canvas>
      </div>
    </section>

    <section class="premium-chart-card">
      <div class="premium-chart-head">
        <div>
          <div class="premium-kicker">Visual ejecutivo</div>
          <h2 class="premium-title">Tipo de mercado</h2>
          <p class="premium-subtitle">Participación nacional y extranjero.</p>
        </div>
        <div class="premium-chip premium-chip-emerald">Mercado</div>
      </div>
      <div class="premium-chart-wrap premium-chart-wrap-donut">
        <canvas id="chartMercado"></canvas>
      </div>
    </section>

    <section class="premium-chart-card">
      <div class="premium-chart-head">
        <div>
          <div class="premium-kicker">Visual ejecutivo</div>
          <h2 class="premium-title">Modalidad</h2>
          <p class="premium-subtitle">Peso relativo por modalidad de compra.</p>
        </div>
        <div class="premium-chip premium-chip-violet">Modalidad</div>
      </div>
      <div class="premium-chart-wrap premium-chart-wrap-donut">
        <canvas id="chartModalidad"></canvas>
      </div>
    </section>
  </section>

  <!-- TABLAS RESUMEN -->
  <section class="summary-grid">
    <?php
    $tables = [
      [
        'title' => 'Listas generales de compras',
        'subtitle' => 'Vista consolidada dinámica por tipo de compra.',
        'label' => 'Listas',
        'data' => $resumenListas ?? [],
        'field' => 'lista',
        'icon' => 'LC'
      ],
      [
        'title' => 'Tipo de compra por mercado',
        'subtitle' => 'Distribución entre individuales y corporativos por mercado.',
        'label' => 'Mercado',
        'data' => $resumenMercadoDetalle ?? [],
        'field' => 'mercado',
        'icon' => 'TM'
      ]
    ];
    ?>

    <?php foreach ($tables as $table): ?>
      <article class="executive-table-card">
        <div class="executive-table-head">
          <div class="head-icon"><?= $table['icon'] ?></div>

          <div>
            <div class="section-kicker">Resumen ejecutivo</div>
            <h2 class="section-title"><?= htmlspecialchars($table['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="section-subtitle"><?= htmlspecialchars($table['subtitle'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </div>

        <div class="executive-table-wrap">
          <table class="executive-table">
            <thead>
              <tr>
                <th><?= htmlspecialchars($table['label'], ENT_QUOTES, 'UTF-8') ?></th>
                <th>Individuales</th>
                <th>Corporativos</th>
                <th>Total</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($table['data'] as $row): ?>
                <?php $isTotal = !empty($row['is_total']); ?>

                <tr class="<?= $isTotal ? 'is-total' : '' ?>">
                  <td>
                    <span class="row-badge">
                      <?= htmlspecialchars((string)($row[$table['field']] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>

                  <td>
                    <div class="metric-number"><?= (int)($row['individuales_cantidad'] ?? 0) ?></div>
                    <div class="metric-money"><?= fmt_money_dashboard((float)($row['individuales_monto'] ?? 0)) ?></div>
                  </td>

                  <td>
                    <div class="metric-number"><?= (int)($row['corporativos_cantidad'] ?? 0) ?></div>
                    <div class="metric-money"><?= fmt_money_dashboard((float)($row['corporativos_monto'] ?? 0)) ?></div>
                  </td>

                  <td>
                    <div class="metric-number metric-number-total"><?= (int)($row['total_cantidad'] ?? 0) ?></div>
                    <div class="metric-money metric-money-total"><?= fmt_money_dashboard((float)($row['total_monto'] ?? 0)) ?></div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <!-- PARTICIPACION -->
  <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
    <article class="section-card">
      <div class="section-head">
        <div>
          <div class="section-kicker">Participación ejecutiva</div>
          <h2 class="section-title">ACFFAA</h2>
          <p class="section-subtitle">Peso relativo dentro del universo filtrado del dashboard.</p>
        </div>
        <div class="stat-pill stat-pill--blue"><?= number_format($partAcffaaPctPac, 1) ?>% PAC</div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="soft-mini-card">
          <div class="soft-mini-card__label">PAC registrados</div>
          <div class="soft-mini-card__value"><?= $partAcffaaPac ?></div>
          <div class="soft-mini-card__foot">
            Participación en cantidad:
            <span class="font-semibold text-slate-700"><?= number_format($partAcffaaPctPac, 1) ?>%</span>
          </div>
        </div>

        <div class="soft-mini-card soft-mini-card--blue">
          <div class="soft-mini-card__label">Monto estimado</div>
          <div class="soft-mini-card__value"><?= fmt_money_dashboard($partAcffaaMonto) ?></div>
          <div class="soft-mini-card__foot">
            Participación en monto:
            <span class="font-semibold"><?= number_format($partAcffaaPctMonto, 1) ?>%</span>
          </div>
        </div>
      </div>

      <div class="mt-5 space-y-4">
        <div>
          <div class="mb-2 flex items-center justify-between gap-3">
            <div class="text-sm font-medium text-slate-700">Participación por PAC</div>
            <div class="text-sm font-semibold text-slate-900"><?= number_format($partAcffaaPctPac, 1) ?>%</div>
          </div>
          <div class="h-3 overflow-hidden rounded-full bg-slate-200">
            <div class="h-full rounded-full bg-blue-600" style="width: <?= min($partAcffaaPctPac, 100) ?>%;"></div>
          </div>
        </div>

        <div>
          <div class="mb-2 flex items-center justify-between gap-3">
            <div class="text-sm font-medium text-slate-700">Participación por monto</div>
            <div class="text-sm font-semibold text-slate-900"><?= number_format($partAcffaaPctMonto, 1) ?>%</div>
          </div>
          <div class="h-3 overflow-hidden rounded-full bg-slate-200">
            <div class="h-full rounded-full bg-cyan-500" style="width: <?= min($partAcffaaPctMonto, 100) ?>%;"></div>
          </div>
        </div>
      </div>
    </article>

    <article class="section-card">
      <div class="section-head">
        <div>
          <div class="section-kicker">Participación ejecutiva</div>
          <h2 class="section-title">Resto del sector</h2>
          <p class="section-subtitle">Comparativo del conjunto no ACFFAA dentro del mismo filtro.</p>
        </div>
        <div class="stat-pill stat-pill--amber"><?= number_format($partRestoPctPac, 1) ?>% PAC</div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="soft-mini-card">
          <div class="soft-mini-card__label">PAC registrados</div>
          <div class="soft-mini-card__value"><?= $partRestoPac ?></div>
          <div class="soft-mini-card__foot">
            Participación en cantidad:
            <span class="font-semibold text-slate-700"><?= number_format($partRestoPctPac, 1) ?>%</span>
          </div>
        </div>

        <div class="soft-mini-card soft-mini-card--amber">
          <div class="soft-mini-card__label">Monto estimado</div>
          <div class="soft-mini-card__value"><?= fmt_money_dashboard($partRestoMonto) ?></div>
          <div class="soft-mini-card__foot">
            Participación en monto:
            <span class="font-semibold"><?= number_format($partRestoPctMonto, 1) ?>%</span>
          </div>
        </div>
      </div>

      <div class="mt-5 space-y-4">
        <div>
          <div class="mb-2 flex items-center justify-between gap-3">
            <div class="text-sm font-medium text-slate-700">Participación por PAC</div>
            <div class="text-sm font-semibold text-slate-900"><?= number_format($partRestoPctPac, 1) ?>%</div>
          </div>
          <div class="h-3 overflow-hidden rounded-full bg-slate-200">
            <div class="h-full rounded-full bg-amber-500" style="width: <?= min($partRestoPctPac, 100) ?>%;"></div>
          </div>
        </div>

        <div>
          <div class="mb-2 flex items-center justify-between gap-3">
            <div class="text-sm font-medium text-slate-700">Participación por monto</div>
            <div class="text-sm font-semibold text-slate-900"><?= number_format($partRestoPctMonto, 1) ?>%</div>
          </div>
          <div class="h-3 overflow-hidden rounded-full bg-slate-200">
            <div class="h-full rounded-full bg-orange-500" style="width: <?= min($partRestoPctMonto, 100) ?>%;"></div>
          </div>
        </div>
      </div>
    </article>
  </section>

  <!-- CONTROL -->
  <section class="grid grid-cols-1 gap-4 xl:grid-cols-2 2xl:grid-cols-4">
    <article class="section-card">
      <div class="section-head">
        <div>
          <div class="section-kicker">Ranking</div>
          <h2 class="section-title">Top dependencias</h2>
        </div>
        <div class="stat-pill stat-pill--amber">Top 5</div>
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
            <div class="rank-card">
              <div class="flex items-center gap-3">
                <div class="rank-card__index"><?= $i + 1 ?></div>
                <div class="min-w-0 flex-1">
                  <div class="truncate text-sm font-semibold text-slate-800"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="mt-1 text-xs text-slate-500"><?= $total ?> PAC · <?= fmt_money_dashboard($monto) ?></div>
                </div>
              </div>
              <div class="rank-card__bar">
                <div class="rank-card__bar-fill rank-card__bar-fill--amber" style="width: <?= min($width, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state-card">Sin datos para mostrar.</div>
        <?php endif; ?>
      </div>
    </article>

    <article class="section-card">
      <div class="section-head">
        <div>
          <div class="section-kicker">Control</div>
          <h2 class="section-title">Alertas gerenciales</h2>
        </div>
        <div class="stat-pill stat-pill--rose">Atención</div>
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
          <div class="empty-state-card">Sin alertas.</div>
        <?php endif; ?>
      </div>
    </article>

    <article class="section-card 2xl:col-span-2">
      <div class="section-head">
        <div>
          <div class="section-kicker">Control</div>
          <h2 class="section-title">Top OBAC</h2>
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2">
        <?php if (!empty($topObac)): ?>
          <?php foreach ($topObac as $i => $item): ?>
            <?php
            $nombre = (string)($item['nombre'] ?? '-');
            $monto  = (float)($item['monto'] ?? 0);
            $width  = $maxMontoObac > 0 ? round(($monto / $maxMontoObac) * 100, 1) : 0;
            ?>
            <div class="rank-card">
              <div class="mb-1 flex items-center justify-between gap-3">
                <div class="truncate text-sm font-medium text-slate-700"><?= ($i + 1) . '. ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-xs text-slate-500"><?= fmt_money_dashboard($monto) ?></div>
              </div>
              <div class="rank-card__bar">
                <div class="rank-card__bar-fill rank-card__bar-fill--blue" style="width: <?= min($width, 100) ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state-card">Sin datos.</div>
        <?php endif; ?>
      </div>
    </article>
  </section>
</div>

<style>
  .dashboard-shell {
    --dash-border: rgba(148, 163, 184, .18);
    --dash-shadow: 0 16px 40px rgba(15, 23, 42, .06);
  }

  .hero-premium {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, .9);
    border-radius: 32px;
    padding: 28px;
    background:
      radial-gradient(circle at top left, rgba(255, 255, 255, .98), rgba(248, 250, 252, .95) 42%, rgba(241, 245, 249, .98) 100%);
    box-shadow:
      0 18px 48px rgba(15, 23, 42, .06),
      inset 0 1px 0 rgba(255, 255, 255, .85);
  }

  .hero-premium__glow {
    position: absolute;
    border-radius: 999px;
    filter: blur(38px);
    pointer-events: none;
    opacity: .55;
  }

  .hero-premium__glow--one {
    top: -60px;
    right: -30px;
    width: 180px;
    height: 180px;
    background: rgba(59, 130, 246, .16);
  }

  .hero-premium__glow--two {
    bottom: -70px;
    left: 30%;
    width: 200px;
    height: 200px;
    background: rgba(16, 185, 129, .12);
  }

  .hero-mini-kpi-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    width: min(100%, 420px);
  }

  .hero-mini-kpi-card {
    border: 1px solid rgba(226, 232, 240, .95);
    background: rgba(255, 255, 255, .78);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 18px 16px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .9);
  }

  .hero-mini-kpi-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgb(148 163 184);
  }

  .hero-mini-kpi-value {
    margin-top: 10px;
    font-size: 28px;
    line-height: 1.05;
    font-weight: 700;
    letter-spacing: -.03em;
    color: rgb(15 23 42);
  }

  .hero-mini-kpi-value--money {
    font-size: 22px;
  }

  .hero-mini-kpi-sub {
    margin-top: 6px;
    font-size: 12px;
    color: rgb(100 116 139);
  }

  .section-card {
    border-radius: 28px;
    border: 1px solid rgba(148, 163, 184, .18);
    background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .96));
    box-shadow: 0 16px 40px rgba(15, 23, 42, .06);
    padding: 22px;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
  }

  .section-head--border {
    margin: -22px -22px 18px;
    padding: 22px 22px 18px;
    border-bottom: 1px solid rgba(226, 232, 240, .95);
    background: linear-gradient(180deg, rgba(255, 255, 255, .9), rgba(248, 250, 252, .92));
  }

  .section-kicker {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgb(148 163 184);
  }

  .section-title {
    margin-top: 6px;
    font-size: 22px;
    line-height: 1.08;
    font-weight: 700;
    letter-spacing: -.02em;
    color: rgb(15 23 42);
  }

  .section-subtitle {
    margin-top: 7px;
    font-size: 13px;
    line-height: 1.5;
    color: rgb(100 116 139);
  }

  .metric-card {
    position: relative;
    overflow: hidden;
    border-radius: 28px;
    border: 1px solid rgba(148, 163, 184, .18);
    background:
      radial-gradient(circle at top right, rgba(255, 255, 255, .96), rgba(248, 250, 252, .94) 52%, rgba(241, 245, 249, .98));
    box-shadow: 0 16px 40px rgba(15, 23, 42, .06);
    padding: 24px 22px;
  }

  .metric-card--dark {
    background:
      linear-gradient(135deg, rgba(15, 23, 42, .98), rgba(30, 41, 59, .96));
    border-color: rgba(30, 41, 59, .9);
    box-shadow: 0 18px 38px rgba(15, 23, 42, .18);
  }

  .metric-card__label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgb(148 163 184);
  }

  .metric-card__value {
    margin-top: 12px;
    font-size: 32px;
    line-height: 1.02;
    font-weight: 700;
    letter-spacing: -.035em;
    color: rgb(15 23 42);
    word-break: break-word;
  }

  .metric-card--dark .metric-card__value,
  .metric-card--dark .metric-card__sub {
    color: white;
  }

  .metric-card--dark .metric-card__label {
    color: rgba(226, 232, 240, .72);
  }

  .metric-card__sub {
    margin-top: 8px;
    font-size: 13px;
    color: rgb(100 116 139);
  }

  .soft-mini-card {
    border-radius: 22px;
    border: 1px solid rgba(226, 232, 240, .95);
    background: rgba(248, 250, 252, .88);
    padding: 16px;
  }

  .soft-mini-card--emerald {
    border-color: rgba(167, 243, 208, .95);
    background: rgba(236, 253, 245, .95);
  }

  .soft-mini-card--amber {
    border-color: rgba(253, 230, 138, .95);
    background: rgba(255, 251, 235, .95);
  }

  .soft-mini-card--blue {
    border-color: rgba(191, 219, 254, .95);
    background: rgba(239, 246, 255, .95);
  }

  .soft-mini-card__label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgb(148 163 184);
  }

  .soft-mini-card__value {
    margin-top: 10px;
    font-size: 28px;
    line-height: 1.06;
    font-weight: 700;
    letter-spacing: -.03em;
    color: rgb(15 23 42);
  }

  .soft-mini-card__foot {
    margin-top: 10px;
    font-size: 13px;
    color: rgb(100 116 139);
  }

  .stat-pill {
    flex-shrink: 0;
    border-radius: 999px;
    padding: 8px 12px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    border: 1px solid transparent;
  }

  .stat-pill--emerald {
    background: rgba(16, 185, 129, .10);
    color: rgb(5 150 105);
    border-color: rgba(16, 185, 129, .16);
  }

  .stat-pill--blue {
    background: rgba(37, 99, 235, .08);
    color: rgb(29 78 216);
    border-color: rgba(37, 99, 235, .12);
  }

  .stat-pill--amber {
    background: rgba(245, 158, 11, .08);
    color: rgb(180 83 9);
    border-color: rgba(245, 158, 11, .14);
  }

  .stat-pill--rose {
    background: rgba(244, 63, 94, .08);
    color: rgb(190 24 93);
    border-color: rgba(244, 63, 94, .12);
  }

  .rank-card {
    border-radius: 20px;
    border: 1px solid rgba(241, 245, 249, .95);
    background: rgba(248, 250, 252, .84);
    padding: 14px;
  }

  .rank-card__index {
    display: flex;
    width: 34px;
    height: 34px;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: rgba(251, 191, 36, .18);
    color: rgb(180 83 9);
    font-size: 12px;
    font-weight: 800;
  }

  .rank-card__bar {
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
    background: rgba(226, 232, 240, .95);
    margin-top: 12px;
  }

  .rank-card__bar-fill {
    height: 100%;
    border-radius: 999px;
  }

  .rank-card__bar-fill--amber {
    background: rgb(245 158 11);
  }

  .rank-card__bar-fill--blue {
    background: rgb(37 99 235);
  }

  .empty-state-card {
    border-radius: 20px;
    border: 1px dashed rgba(203, 213, 225, .95);
    background: rgba(248, 250, 252, .8);
    padding: 18px;
    font-size: 14px;
    color: rgb(100 116 139);
  }

  .premium-chart-card {
    position: relative;
    overflow: hidden;
    border-radius: 28px;
    border: 1px solid rgba(148, 163, 184, .18);
    background:
      radial-gradient(circle at top right, rgba(255, 255, 255, .95), rgba(248, 250, 252, .92) 45%, rgba(241, 245, 249, .96) 100%);
    box-shadow:
      0 16px 40px rgba(15, 23, 42, .06),
      inset 0 1px 0 rgba(255, 255, 255, .75);
    padding: 22px 22px 18px;
  }

  .premium-chart-card::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(180deg, rgba(255, 255, 255, .34), rgba(255, 255, 255, 0));
  }

  .premium-chart-head {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }

  .premium-kicker {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgb(148 163 184);
  }

  .premium-title {
    margin-top: 6px;
    font-size: 22px;
    line-height: 1.05;
    font-weight: 700;
    letter-spacing: -.02em;
    color: rgb(15 23 42);
  }

  .premium-subtitle {
    margin-top: 7px;
    font-size: 13px;
    line-height: 1.45;
    color: rgb(100 116 139);
  }

  .premium-chip {
    flex-shrink: 0;
    border-radius: 999px;
    padding: 8px 12px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    border: 1px solid transparent;
  }

  .premium-chip-slate {
    background: rgba(15, 23, 42, .06);
    color: rgb(15 23 42);
    border-color: rgba(15, 23, 42, .08);
  }

  .premium-chip-blue {
    background: rgba(37, 99, 235, .08);
    color: rgb(29 78 216);
    border-color: rgba(37, 99, 235, .12);
  }

  .premium-chip-emerald {
    background: rgba(5, 150, 105, .08);
    color: rgb(5 150 105);
    border-color: rgba(5, 150, 105, .12);
  }

  .premium-chip-violet {
    background: rgba(124, 58, 237, .08);
    color: rgb(109 40 217);
    border-color: rgba(124, 58, 237, .12);
  }

  .premium-chart-wrap {
    position: relative;
    z-index: 1;
    border-radius: 24px;
    border: 1px solid rgba(226, 232, 240, .9);
    background: linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(248, 250, 252, .96));
  }


  .premium-chart-wrap-bar {
    height: 360px;
    padding: 18px 14px 12px;
  }

  .premium-chart-wrap-pie {
    height: 400px;
    padding: 20px 16px 14px;
    border-radius: 24px;
    border: 1px solid rgba(226, 232, 240, .95);
    background:
      radial-gradient(circle at top, rgba(255, 255, 255, .98), rgba(248, 250, 252, .96) 58%, rgba(241, 245, 249, .98));
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, .8),
      0 10px 24px rgba(15, 23, 42, .04);
  }

  .table-row-title {
    font-size: 14px;
    font-weight: 700;
    color: rgb(15 23 42);
    line-height: 1.35;
  }

  .table-row-number {
    font-size: 18px;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -.02em;
    color: rgb(15 23 42);
  }

  .table-row-number--total {
    font-size: 20px;
  }

  .table-row-money {
    margin-top: 4px;
    font-size: 12px;
    line-height: 1.35;
    color: rgb(71 85 105);
    word-break: break-word;
  }

  .table-row-money--total {
    color: rgb(15 23 42);
    font-weight: 600;
  }


  @media (max-width: 768px) {


    .table-row-title {
      font-size: 13px;
    }

    .table-row-number {
      font-size: 16px;
    }

    .table-row-number--total {
      font-size: 18px;
    }

    .table-row-money {
      font-size: 11px;
    }
  }

  @media (max-width: 1024px) {
    .hero-mini-kpi-grid {
      width: 100%;
    }
  }

  @media (max-width: 768px) {

    .hero-premium,
    .section-card,
    .metric-card,
    .premium-chart-card {
      border-radius: 24px;
      padding: 18px 16px;
    }

    .section-head--border {
      margin: -18px -16px 16px;
      padding: 18px 16px 16px;
    }

    .section-title,
    .premium-title {
      font-size: 18px;
    }

    .section-subtitle,
    .premium-subtitle {
      font-size: 12px;
    }

    .metric-card__value {
      font-size: 26px;
    }

    .soft-mini-card__value,
    .hero-mini-kpi-value {
      font-size: 24px;
    }

    .hero-mini-kpi-value--money {
      font-size: 18px;
    }

    .premium-chart-wrap-donut,
    .premium-chart-wrap-bar {
      height: 390px;
      padding: 18px 16px 12px;
    }

    .premium-chart-wrap-pie {
      height: 340px;
      padding: 14px 10px 10px;
    }

    .premium-chart-head,
    .section-head {
      flex-direction: column;
      align-items: flex-start;
    }
  }

  .summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-top: 18px;
  }

  .executive-table-card {
    overflow: hidden;
    border-radius: 22px;
    background:
      linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .96));
    border: 1px solid rgba(148, 163, 184, .25);
    box-shadow:
      0 18px 40px rgba(15, 23, 42, .07),
      inset 0 1px 0 rgba(255, 255, 255, .9);
  }

  .executive-table-head {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 20px 16px;
    border-bottom: 1px solid rgba(148, 163, 184, .20);
    background:
      radial-gradient(circle at top left, rgba(122, 12, 25, .10), transparent 34%),
      linear-gradient(180deg, #ffffff, #f8fafc);
  }

  .head-icon {
    width: 42px;
    height: 42px;
    border-radius: 15px;
    display: grid;
    place-items: center;
    color: #7A0C19;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: .08em;
    background: linear-gradient(180deg, #fff7f8, #f8e8eb);
    border: 1px solid rgba(122, 12, 25, .16);
    box-shadow: 0 10px 24px rgba(122, 12, 25, .10);
    flex: 0 0 auto;
  }

  .section-kicker {
    color: #64748b;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 5px;
  }

  .section-title {
    color: #0f172a;
    font-size: 19px;
    font-weight: 800;
    line-height: 1.1;
    margin: 0;
  }

  .section-subtitle {
    color: #64748b;
    font-size: 12px;
    margin-top: 7px;
  }

  .executive-table-wrap {
    padding: 14px 18px 18px;
    overflow-x: auto;
  }

  .executive-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
    font-size: 12px;
  }

  .executive-table thead th {
    padding: 11px 10px;
    color: #64748b;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    text-align: center;
    background: #f8fafc;
    border-top: 1px solid rgba(148, 163, 184, .22);
    border-bottom: 1px solid rgba(148, 163, 184, .22);
  }

  .executive-table thead th:first-child {
    text-align: left;
    border-radius: 14px 0 0 14px;
    border-left: 1px solid rgba(148, 163, 184, .22);
  }

  .executive-table thead th:last-child {
    border-radius: 0 14px 14px 0;
    border-right: 1px solid rgba(148, 163, 184, .22);
  }

  .executive-table tbody tr {
    background: #ffffff;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .045);
  }

  .executive-table tbody td {
    padding: 13px 10px;
    text-align: center;
    vertical-align: middle;
    border-top: 1px solid rgba(226, 232, 240, .9);
    border-bottom: 1px solid rgba(226, 232, 240, .9);
  }

  .executive-table tbody td:first-child {
    text-align: left;
    border-left: 1px solid rgba(226, 232, 240, .9);
    border-radius: 15px 0 0 15px;
  }

  .executive-table tbody td:last-child {
    border-right: 1px solid rgba(226, 232, 240, .9);
    border-radius: 0 15px 15px 0;
    background: linear-gradient(180deg, #fff, #fafafa);
  }

  .row-badge {
    display: inline-flex;
    align-items: center;
    min-width: 76px;
    padding: 7px 10px;
    border-radius: 999px;
    color: #0f172a;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, .24);
    font-weight: 800;
  }

  .metric-number {
    color: #020617;
    font-size: 18px;
    font-weight: 850;
    line-height: 1;
  }

  .metric-money {
    margin-top: 7px;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
  }

  .metric-number-total {
    color: #7A0C19;
    font-size: 19px;
  }

  .metric-money-total {
    color: #111827;
    font-weight: 800;
  }

  .executive-table tbody tr.is-total {
    box-shadow: 0 12px 26px rgba(122, 12, 25, .10);
  }

  .executive-table tbody tr.is-total td {
    background: linear-gradient(180deg, #fff8f9, #f7e8eb);
    border-top: 1px solid rgba(122, 12, 25, .18);
    border-bottom: 1px solid rgba(122, 12, 25, .18);
  }

  .executive-table tbody tr.is-total td:first-child {
    border-left: 1px solid rgba(122, 12, 25, .18);
  }

  .executive-table tbody tr.is-total td:last-child {
    border-right: 1px solid rgba(122, 12, 25, .18);
  }

  .executive-table tbody tr.is-total .row-badge {
    color: #7A0C19;
    background: #ffffff;
    border-color: rgba(122, 12, 25, .22);
  }

  @media (max-width: 1180px) {
    .summary-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
  Chart.register(ChartDataLabels);

  const centerTextPlugin = {
    id: 'centerTextPlugin',
    afterDraw(chart, args, pluginOptions) {
      if (chart.config.type !== 'doughnut') return;

      const meta = chart.getDatasetMeta(0);
      if (!meta || !meta.data || !meta.data.length) return;

      const {
        ctx
      } = chart;
      const x = meta.data[0].x;
      const y = meta.data[0].y;

      const line1 = pluginOptions?.line1 || '';
      const line2 = pluginOptions?.line2 || '';

      ctx.save();
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';

      ctx.fillStyle = '#0f172a';
      ctx.font = '700 30px Inter, system-ui, sans-serif';
      ctx.fillText(line1, x, y - 8);

      ctx.fillStyle = '#64748b';
      ctx.font = '600 12px Inter, system-ui, sans-serif';
      ctx.fillText(line2, x, y + 18);
      ctx.restore();
    }
  };

  Chart.register(centerTextPlugin);

  const moneyFormatter = (value) => {
    return 'S/ ' + Number(value || 0).toLocaleString('es-PE', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  };

  const shortMoneyFormatter = (value) => {
    const n = Number(value || 0);

    if (Math.abs(n) >= 1000000) {
      return 'S/ ' + (n / 1000000).toFixed(1) + 'M';
    }

    if (Math.abs(n) >= 1000) {
      return 'S/ ' + (n / 1000).toFixed(0) + 'K';
    }

    return 'S/ ' + n.toLocaleString('es-PE', {
      maximumFractionDigits: 0
    });
  };

  const percentFormatter = (value, total) => {
    const n = Number(value || 0);
    const t = Number(total || 0);
    if (t <= 0) return '0%';
    return ((n / t) * 100).toFixed(1) + '%';
  };

  const sumArray = (arr) => arr.reduce((acc, n) => acc + Number(n || 0), 0);

  const obacLabels = <?= json_encode($chartObacLabels, JSON_UNESCAPED_UNICODE) ?>;
  const obacMontos = <?= json_encode($chartObacMontos, JSON_UNESCAPED_UNICODE) ?>;

  const mercadoLabels = <?= json_encode($chartMercadoLabels, JSON_UNESCAPED_UNICODE) ?>;
  const mercadoTotals = <?= json_encode($chartMercadoTotals, JSON_UNESCAPED_UNICODE) ?>;

  const modalidadLabels = <?= json_encode($chartModalidadLabels, JSON_UNESCAPED_UNICODE) ?>;
  const modalidadTotals = <?= json_encode($chartModalidadTotals, JSON_UNESCAPED_UNICODE) ?>;

  const mesLabels = <?= json_encode($chartMesLabels, JSON_UNESCAPED_UNICODE) ?>;
  const mesMontos = <?= json_encode($chartMesMontos, JSON_UNESCAPED_UNICODE) ?>;
  const mesTotales = <?= json_encode($chartMesTotales, JSON_UNESCAPED_UNICODE) ?>;

  const participacionLabels = <?= json_encode($chartParticipacionLabels, JSON_UNESCAPED_UNICODE) ?>;
  const participacionMontos = <?= json_encode($chartParticipacionMontos, JSON_UNESCAPED_UNICODE) ?>;
  const participacionTotales = <?= json_encode($chartParticipacionTotales, JSON_UNESCAPED_UNICODE) ?>;

  const totalMercado = sumArray(mercadoTotals);
  const totalModalidad = sumArray(modalidadTotals);
  const totalParticipacionMonto = sumArray(participacionMontos);

  const maxMercadoIndex = mercadoTotals.length ? mercadoTotals.indexOf(Math.max(...mercadoTotals)) : -1;
  const maxModalidadIndex = modalidadTotals.length ? modalidadTotals.indexOf(Math.max(...modalidadTotals)) : -1;

  const premiumLegend = {
    position: 'bottom',
    labels: {
      usePointStyle: true,
      pointStyle: 'circle',
      boxWidth: 8,
      boxHeight: 8,
      padding: 18,
      color: '#475569',
      font: {
        size: 11,
        weight: '600'
      }
    }
  };

  const premiumTooltip = {
    backgroundColor: 'rgba(15,23,42,.96)',
    titleColor: '#fff',
    bodyColor: '#e2e8f0',
    padding: 14,
    cornerRadius: 14,
    displayColors: true
  };

  if (document.getElementById('chartMercado') && mercadoLabels.length) {
    new Chart(document.getElementById('chartMercado'), {
      type: 'doughnut',
      data: {
        labels: mercadoLabels,
        datasets: [{
          data: mercadoTotals,
          backgroundColor: [
            '#10B981',
            '#3B82F6',
            '#F59E0B',
            '#8B5CF6',
            '#06B6D4',
            '#F472B6'
          ],
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 8,
          spacing: 2
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 24,
            right: 34,
            bottom: 10,
            left: 34
          }
        },
        cutout: '66%',
        plugins: {
          legend: premiumLegend,
          tooltip: {
            ...premiumTooltip,
            callbacks: {
              label: (ctx) => `${ctx.label}: ${ctx.raw} PAC (${percentFormatter(ctx.raw, totalMercado)})`
            }
          },
          datalabels: {
            color: '#065f46',
            font: {
              size: 11,
              weight: '700'
            },
            formatter: (value) => {
              const pct = parseFloat(percentFormatter(value, totalMercado));
              return pct >= 7 ? pct.toFixed(1) + '%' : '';
            },
            anchor: 'end',
            align: 'end',
            offset: 10,
            clamp: true
          },
          centerTextPlugin: {
            line1: totalMercado > 0 ? percentFormatter(mercadoTotals[maxMercadoIndex] || 0, totalMercado) : '0%',
            line2: maxMercadoIndex >= 0 ? mercadoLabels[maxMercadoIndex] : 'SIN DATOS'
          }
        }
      }
    });
  }

  if (document.getElementById('chartModalidad') && modalidadLabels.length) {
    new Chart(document.getElementById('chartModalidad'), {
      type: 'doughnut',
      data: {
        labels: modalidadLabels,
        datasets: [{
          data: modalidadTotals,
          backgroundColor: [
            '#6366F1',
            '#22C55E',
            '#F97316',
            '#0EA5E9',
            '#A855F7',
            '#E11D48'
          ],
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 8,
          spacing: 2
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 24,
            right: 38,
            bottom: 10,
            left: 38
          }
        },
        cutout: '66%',
        plugins: {
          legend: premiumLegend,
          tooltip: {
            ...premiumTooltip,
            callbacks: {
              label: (ctx) => `${ctx.label}: ${ctx.raw} PAC (${percentFormatter(ctx.raw, totalModalidad)})`
            }
          },
          datalabels: {
            color: '#4c1d95',
            font: {
              size: 11,
              weight: '700'
            },
            formatter: (value) => {
              const pct = parseFloat(percentFormatter(value, totalModalidad));
              return pct >= 7 ? pct.toFixed(1) + '%' : '';
            },
            anchor: 'end',
            align: 'end',
            offset: 10,
            clamp: true
          },
          centerTextPlugin: {
            line1: totalModalidad > 0 ? percentFormatter(modalidadTotals[maxModalidadIndex] || 0, totalModalidad) : '0%',
            line2: maxModalidadIndex >= 0 ? modalidadLabels[maxModalidadIndex] : 'SIN DATOS'
          }
        }
      }
    });
  }

  if (document.getElementById('chartMeses') && mesLabels.length) {
    new Chart(document.getElementById('chartMeses'), {
      type: 'bar',
      data: {
        labels: mesLabels,
        datasets: [{
          label: 'Monto estimado',
          data: mesMontos,
          backgroundColor: '#0b1736',
          borderRadius: 12,
          borderSkipped: false,
          maxBarThickness: 44
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 28,
            right: 10,
            left: 10,
            bottom: 0
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            ...premiumTooltip,
            callbacks: {
              label: (ctx) => moneyFormatter(ctx.raw),
              afterLabel: (ctx) => `${mesTotales[ctx.dataIndex] || 0} PAC`
            }
          },
          datalabels: {
            color: '#0f172a',
            anchor: 'end',
            align: 'top',
            offset: 6,
            clamp: true,
            formatter: (value) => moneyFormatter(value),
            font: {
              size: 10,
              weight: '700'
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#64748b',
              font: {
                size: 11,
                weight: '600'
              }
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: '#64748b',
              callback: (value) => moneyFormatter(value)
            },
            grid: {
              color: 'rgba(148,163,184,.14)'
            }
          }
        }
      }
    });
  }

  if (document.getElementById('chartParticipacionPie') && participacionLabels.length) {
    const doughnutCanvas = document.getElementById('chartParticipacionPie');

    const participacionColors = [
      '#3B82F6',
      '#8B5CF6',
      '#10B981',
      '#F59E0B',
      '#06B6D4',
      '#F472B6',
      '#A78BFA',
      '#60A5FA'
    ];

    const maxParticipacionIndex = participacionMontos.length ?
      participacionMontos.indexOf(Math.max(...participacionMontos)) :
      -1;

    new Chart(doughnutCanvas, {
      type: 'doughnut',
      data: {
        labels: participacionLabels,
        datasets: [{
          data: participacionMontos,
          backgroundColor: participacionColors,
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 8,
          spacing: 2
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 24,
            right: 36,
            bottom: 10,
            left: 36
          }
        },
        cutout: '66%',
        animation: {
          animateRotate: true,
          duration: 900
        },
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              boxWidth: 8,
              boxHeight: 8,
              padding: 18,
              color: '#475569',
              font: {
                size: 11,
                weight: '600'
              },
              generateLabels(chart) {
                const data = chart.data;
                return data.labels.map((label, i) => {
                  const value = Number(data.datasets[0].data[i] || 0);
                  const pct = totalParticipacionMonto > 0 ?
                    ((value / totalParticipacionMonto) * 100).toFixed(1) :
                    '0.0';

                  return {
                    text: `${label} · ${pct}%`,
                    fillStyle: data.datasets[0].backgroundColor[i],
                    strokeStyle: '#fff',
                    lineWidth: 2,
                    hidden: false,
                    index: i
                  };
                });
              }
            }
          },
          tooltip: {
            ...premiumTooltip,
            callbacks: {
              title: (items) => items[0]?.label || '',
              label: (ctx) => `Monto: ${moneyFormatter(ctx.raw)}`,
              afterLabel: (ctx) => {
                const totalPac = participacionTotales[ctx.dataIndex] || 0;
                const pctMonto = percentFormatter(ctx.raw, totalParticipacionMonto);
                return `PAC: ${totalPac} · Participación: ${pctMonto}`;
              }
            }
          },
          datalabels: {
            color: '#0f172a',
            font: {
              size: 11,
              weight: '700'
            },
            formatter: (value) => {
              const pct = parseFloat(percentFormatter(value, totalParticipacionMonto));
              return pct >= 7 ? pct.toFixed(1) + '%' : '';
            },
            anchor: 'end',
            align: 'end',
            offset: 10,
            clamp: true
          },
          centerTextPlugin: {
            line1: totalParticipacionMonto > 0 ?
              percentFormatter(participacionMontos[maxParticipacionIndex] || 0, totalParticipacionMonto) : '0%',
            line2: maxParticipacionIndex >= 0 ?
              participacionLabels[maxParticipacionIndex] : 'SIN DATOS'
          }
        }
      }
    });
  }

  if (document.getElementById('chartObac') && obacLabels.length) {
    new Chart(document.getElementById('chartObac'), {
      type: 'bar',
      data: {
        labels: obacLabels,
        datasets: [{
          label: 'Monto estimado',
          data: obacMontos,
          backgroundColor: '#2563eb',
          borderRadius: 12,
          borderSkipped: false,
          maxBarThickness: 26
        }]
      },
      options: {
        indexAxis: 'y',
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 8,
            right: 78,
            left: 0,
            bottom: 0
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            ...premiumTooltip,
            callbacks: {
              label: (ctx) => moneyFormatter(ctx.raw)
            }
          },
          datalabels: {
            color: '#1e3a8a',
            anchor: 'end',
            align: 'right',
            offset: 8,
            clamp: true,
            formatter: (value) => moneyFormatter(value),
            font: {
              size: 10,
              weight: '700'
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: {
              color: '#64748b',
              callback: (value) => moneyFormatter(value)
            },
            grid: {
              color: 'rgba(148,163,184,.14)'
            }
          },
          y: {
            ticks: {
              color: '#64748b',
              font: {
                size: 11,
                weight: '600'
              }
            },
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
</script>