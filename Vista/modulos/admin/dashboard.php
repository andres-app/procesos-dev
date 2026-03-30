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

foreach ($topDependencias as $item) {
  $maxMontoDep = max($maxMontoDep, (float)($item['monto'] ?? 0));
}
foreach ($topObac as $item) {
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

foreach ($porEstado as $item) {
  $chartEstadoLabels[] = (string)($item['nombre'] ?? '-');
  $chartEstadoTotals[] = (int)($item['total'] ?? 0);
}

$chartObacLabels = [];
$chartObacMontos = [];
foreach ($porObac as $item) {
  $chartObacLabels[] = (string)($item['nombre'] ?? '-');
  $chartObacMontos[] = (float)($item['monto'] ?? 0);
}

$chartMercadoLabels = [];
$chartMercadoTotals = [];
foreach ($porMercado as $item) {
  $nombre = trim((string)($item['nombre'] ?? ''));
  if ($nombre === '') {
    continue;
  }

  $chartMercadoLabels[] = $nombre;
  $chartMercadoTotals[] = (int)($item['total'] ?? 0);
}

$chartModalidadLabels = [];
$chartModalidadTotals = [];
foreach ($porModalidad as $item) {
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
foreach ($tendenciaMes as $item) {
  $chartMesLabels[]  = (string)($item['nombre'] ?? '-');
  $chartMesMontos[]  = (float)($item['monto'] ?? 0);
  $chartMesTotales[] = (int)($item['total'] ?? 0);
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

  <div class="flex flex-wrap items-center gap-2">
    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
      Vista rápida:
    </span>

    <a
      href="<?= htmlspecialchars(buildDashboardFilterUrl(['ejecucion' => 4]), ENT_QUOTES, 'UTF-8') ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $ejecucionActual === '4'
                                                                                                          ? 'border-rose-300 bg-rose-600 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-rose-200 hover:text-rose-700' ?>">
      ACFFAA
    </a>

    <a
      href="<?= htmlspecialchars(buildDashboardFilterUrl(['ejecucion' => 0]), ENT_QUOTES, 'UTF-8') ?>"
      class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition <?= $ejecucionActual === '0'
                                                                                                          ? 'border-slate-300 bg-slate-900 text-white'
                                                                                                          : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:text-slate-900' ?>">
      Todos
    </a>
  </div>

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

  <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)] xl:col-span-2">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Cobertura</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Estimado vs certificado</h2>
        </div>
        <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
          <?= $coberturaPct ?>%
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-400">Estimado</div>
          <div class="mt-2 text-2xl font-semibold text-slate-900">
            <?= fmt_money_dashboard($comparativo['total_estimado'] ?? 0) ?>
          </div>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
          <div class="text-xs uppercase tracking-wide text-emerald-600">Certificado</div>
          <div class="mt-2 text-2xl font-semibold text-emerald-800">
            <?= fmt_money_dashboard($comparativo['total_certificado'] ?? 0) ?>
          </div>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
          <div class="text-xs uppercase tracking-wide text-amber-600">Brecha</div>
          <div class="mt-2 text-2xl font-semibold text-amber-800">
            <?= fmt_money_dashboard($comparativo['brecha'] ?? 0) ?>
          </div>
        </div>
      </div>

      <div class="mt-5">
        <div class="mb-2 flex items-center justify-between gap-3">
          <div class="text-sm font-medium text-slate-700">Nivel de cobertura financiera</div>
          <div class="text-sm font-semibold text-slate-900"><?= $coberturaPct ?>%</div>
        </div>
        <div class="h-4 overflow-hidden rounded-full bg-slate-200">
          <div class="h-full rounded-full bg-emerald-600 transition-all" style="width: <?= min($coberturaPct, 100) ?>%;"></div>
        </div>
      </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Estado financiero</div>
      <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">PAC certificados</h2>

      <div class="mt-5 space-y-4">
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
          <div class="text-xs uppercase tracking-wide text-emerald-600">Con certificado</div>
          <div class="mt-2 text-3xl font-semibold text-emerald-800">
            <?= (int)($comparativo['pac_con_certificado'] ?? 0) ?>
          </div>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
          <div class="text-xs uppercase tracking-wide text-amber-600">Sin certificado</div>
          <div class="mt-2 text-3xl font-semibold text-amber-800">
            <?= (int)($comparativo['pac_sin_certificado'] ?? 0) ?>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Participación ejecutiva</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">ACFFAA</h2>
          <p class="mt-1 text-sm text-slate-500">Peso relativo dentro del universo filtrado del dashboard.</p>
        </div>
        <div class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
          <?= number_format($partAcffaaPctPac, 1) ?>% PAC
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-400">PAC registrados</div>
          <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
            <?= $partAcffaaPac ?>
          </div>
          <div class="mt-2 text-sm text-slate-500">
            Participación en cantidad: <span class="font-semibold text-slate-700"><?= number_format($partAcffaaPctPac, 1) ?>%</span>
          </div>
        </div>

        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
          <div class="text-xs uppercase tracking-wide text-blue-600">Monto estimado</div>
          <div class="mt-2 text-2xl font-semibold tracking-tight text-blue-900">
            <?= fmt_money_dashboard($partAcffaaMonto) ?>
          </div>
          <div class="mt-2 text-sm text-blue-700/80">
            Participación en monto: <span class="font-semibold"><?= number_format($partAcffaaPctMonto, 1) ?>%</span>
          </div>
        </div>
      </div>

      <div class="mt-5 space-y-3">
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
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)]">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Participación ejecutiva</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Resto del sector</h2>
          <p class="mt-1 text-sm text-slate-500">Comparativo del conjunto no ACFFAA dentro del mismo filtro.</p>
        </div>
        <div class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
          <?= number_format($partRestoPctPac, 1) ?>% PAC
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <div class="text-xs uppercase tracking-wide text-slate-400">PAC registrados</div>
          <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
            <?= $partRestoPac ?>
          </div>
          <div class="mt-2 text-sm text-slate-500">
            Participación en cantidad: <span class="font-semibold text-slate-700"><?= number_format($partRestoPctPac, 1) ?>%</span>
          </div>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
          <div class="text-xs uppercase tracking-wide text-amber-600">Monto estimado</div>
          <div class="mt-2 text-2xl font-semibold tracking-tight text-amber-900">
            <?= fmt_money_dashboard($partRestoMonto) ?>
          </div>
          <div class="mt-2 text-sm text-amber-700/80">
            Participación en monto: <span class="font-semibold"><?= number_format($partRestoPctMonto, 1) ?>%</span>
          </div>
        </div>
      </div>

      <div class="mt-5 space-y-3">
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
    </section>
  </div>

  <div class="grid grid-cols-1 gap-4 xl:grid-cols-2 2xl:grid-cols-4">

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
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,.06)] 2xl:col-span-2">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Control</div>
          <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">Top OBAC</h2>
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
            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-3">
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
    </section>

  </div>

</div>

<style>
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

  .premium-chart-wrap-donut {
    height: 380px;
    padding: 18px 14px 10px;
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

  @media (max-width: 768px) {
    .premium-chart-card {
      padding: 18px 16px 14px;
      border-radius: 24px;
    }

    .premium-title {
      font-size: 18px;
    }

    .premium-subtitle {
      font-size: 12px;
    }

    .premium-chart-wrap-donut,
    .premium-chart-wrap-bar {
      height: 320px;
      padding: 14px 10px 10px;
    }

    .premium-chart-wrap-pie {
      height: 340px;
      padding: 14px 10px 10px;
    }

    .premium-chart-head {
      flex-direction: column;
      align-items: flex-start;
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

      const { ctx } = chart;
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

  const chartColors = {
    slate: ['#0b1736', '#1e3a8a', '#334155', '#64748b', '#94a3b8', '#cbd5e1'],
    blue: ['#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
    emerald: ['#047857', '#059669', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0'],
    violet: ['#5b21b6', '#6d28d9', '#7c3aed', '#8b5cf6', '#a78bfa', '#c4b5fd'],
    mixed: ['#0b1736', '#1d4ed8', '#059669', '#7c3aed', '#f59e0b', '#ef4444', '#0891b2', '#ec4899']
  };

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

  const estadoLabels = <?= json_encode($chartEstadoLabels, JSON_UNESCAPED_UNICODE) ?>;
  const estadoTotals = <?= json_encode($chartEstadoTotals, JSON_UNESCAPED_UNICODE) ?>;

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

  const totalEstado = sumArray(estadoTotals);
  const totalMercado = sumArray(mercadoTotals);
  const totalModalidad = sumArray(modalidadTotals);
  const totalParticipacionMonto = sumArray(participacionMontos);

  const maxEstadoIndex = estadoTotals.length ? estadoTotals.indexOf(Math.max(...estadoTotals)) : -1;
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
          backgroundColor: chartColors.emerald,
          borderColor: '#ffffff',
          borderWidth: 2,
          hoverOffset: 6
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: { top: 24, right: 34, bottom: 10, left: 34 }
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
          backgroundColor: chartColors.violet,
          borderColor: '#ffffff',
          borderWidth: 2,
          hoverOffset: 6
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: { top: 24, right: 38, bottom: 10, left: 38 }
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
          padding: { top: 28, right: 10, left: 10, bottom: 0 }
        },
        plugins: {
          legend: { display: false },
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
            grid: { display: false },
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
    const pieCanvas = document.getElementById('chartParticipacionPie');
    const pieContext = pieCanvas.getContext('2d');

    const pieGradients = [
      (() => {
        const g = pieContext.createLinearGradient(0, 0, 0, 320);
        g.addColorStop(0, '#60a5fa');
        g.addColorStop(1, '#2563eb');
        return g;
      })(),
      (() => {
        const g = pieContext.createLinearGradient(0, 0, 0, 320);
        g.addColorStop(0, '#fb923c');
        g.addColorStop(1, '#f97316');
        return g;
      })()
    ];

    new Chart(pieCanvas, {
      type: 'pie',
      data: {
        labels: participacionLabels,
        datasets: [{
          data: participacionMontos,
          backgroundColor: pieGradients,
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverBorderWidth: 4,
          hoverOffset: 10,
          radius: '86%'
        }]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: { top: 16, right: 20, bottom: 12, left: 20 }
        },
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
              boxWidth: 10,
              boxHeight: 10,
              padding: 18,
              color: '#334155',
              font: {
                size: 12,
                weight: '700'
              },
              generateLabels(chart) {
                const data = chart.data;
                return data.labels.map((label, i) => {
                  const value = Number(data.datasets[0].data[i] || 0);
                  const pct = totalParticipacionMonto > 0
                    ? ((value / totalParticipacionMonto) * 100).toFixed(1)
                    : '0.0';

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
            padding: 16,
            cornerRadius: 16,
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
            textAlign: 'center',
            textStrokeColor: 'rgba(255,255,255,.92)',
            textStrokeWidth: 3,
            formatter: (value, ctx) => {
              const pct = totalParticipacionMonto > 0
                ? ((Number(value || 0) / totalParticipacionMonto) * 100)
                : 0;

              const pac = participacionTotales[ctx.dataIndex] || 0;

              return [
                pct.toFixed(1) + '%',
                pac + ' PAC',
                shortMoneyFormatter(value)
              ];
            },
            font: (ctx) => {
              const value = Number(ctx.dataset.data[ctx.dataIndex] || 0);
              const pct = totalParticipacionMonto > 0 ? (value / totalParticipacionMonto) * 100 : 0;

              return {
                size: pct < 15 ? 10 : 13,
                weight: '800'
              };
            },
            display: (ctx) => {
              const value = Number(ctx.dataset.data[ctx.dataIndex] || 0);
              const pct = totalParticipacionMonto > 0 ? (value / totalParticipacionMonto) * 100 : 0;
              return pct > 6;
            },
            anchor: 'center',
            align: 'center',
            offset: 0,
            clamp: true
          }
        }
      },
      plugins: [{
        id: 'softShadowPie',
        beforeDatasetDraw(chart) {
          const { ctx } = chart;
          ctx.save();
          ctx.shadowColor = 'rgba(0,0,0,0.12)';
          ctx.shadowBlur = 18;
          ctx.shadowOffsetY = 6;
        },
        afterDatasetDraw(chart) {
          chart.ctx.restore();
        }
      }]
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
          padding: { top: 8, right: 78, left: 0, bottom: 0 }
        },
        plugins: {
          legend: { display: false },
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
            grid: { display: false }
          }
        }
      }
    });
  }
</script>