<?php
$titulo = 'Indicadores | Seguimiento';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

// ===== MAQUETA (MOCK) =====
$mockPac = [
  'total' => 22,
  'totalSoles' => 58765432.10,
  'pub' => 14,
  'sol' => 8,
  'byEstado' => [
    'PUBLICADO' => 14,
    'SOLICITADO' => 8,
  ],
  'bySel' => [
    'RES' => 10,
    'LP'  => 4,
    'SIE' => 3,
    'CPS' => 3,
    'CPA' => 1,
    'CP'  => 1,
  ],
  'byObac' => [
    'FAP' => 9,
    'EP' => 6,
    'MGP' => 6,
    'CONIDA' => 1,
  ],
  'sumByObac' => [
    'FAP' => 32000000,
    'EP' => 18000000,
    'MGP' => 8000000,
    'CONIDA' => 820000,
  ],
];

$mockPro = [
  'total' => 13,
  'totalSoles' => 151234567.89,
  'adj' => 8,
  'con' => 2,
  'byEstado' => [
    'ADJUDICADO' => 8,
    'CONVOCADO' => 2,
    'DESIERTO' => 3,
  ],
  'byTipo' => [
    'LP' => 5,
    'RES' => 3,
    'CPC' => 2,
    'CPS' => 3,
  ],
  'byObac' => [
    'MGP' => 5,
    'FAP' => 4,
    'EP' => 3,
    'CORP' => 1,
  ],
  'sumByObac' => [
    'CORP' => 61367168.26,
    'MGP'  => 65000000,
    'EP'   => 18213000,
    'FAP'  => 7500000,
  ],
];

function fmt_money($n) {
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}
function pct($value, $total) {
  if ($total <= 0) return 0;
  return round(($value / $total) * 100, 1);
}
function topN($assoc, $n = 6) {
  arsort($assoc);
  return array_slice($assoc, 0, $n, true);
}
?>

<main class="page page-shell flex-1 px-5 pt-4 main-ind">

  <section class="mb-5 filtros-sticky">
    <div class="bg-white/90 text-slate-900 rounded-2xl p-4 shadow-lg filtros-wrap">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm text-slate-500">Resumen ejecutivo</p>
          <h2 class="text-2xl font-semibold mt-1">Indicadores</h2>
        </div>
        <div class="year-pill">
          <span class="dot"></span>
          <span>AF-2026</span>
        </div>
      </div>

      <div class="mt-4 tabs">
        <button class="tab tab-active" type="button" data-tab="pac">PAC</button>
        <button class="tab" type="button" data-tab="procesos">Procesos</button>
      </div>
    </div>
  </section>

  <!-- PAC -->
  <section id="tab-pac" class="tab-panel">
    <section class="kpis">
      <div class="kpi">
        <p class="kpi-label">Total PAC</p>
        <p class="kpi-value"><?= (int)$mockPac['total'] ?></p>
        <p class="kpi-sub">Registros</p>
      </div>
      <div class="kpi">
        <p class="kpi-label">Monto total</p>
        <p class="kpi-value"><?= fmt_money($mockPac['totalSoles']) ?></p>
        <p class="kpi-sub">Estimado</p>
      </div>
      <div class="kpi">
        <p class="kpi-label">Publicado</p>
        <p class="kpi-value"><?= (int)$mockPac['pub'] ?></p>
        <p class="kpi-sub"><?= pct($mockPac['pub'], $mockPac['total']) ?>%</p>
      </div>
      <div class="kpi">
        <p class="kpi-label">Solicitado</p>
        <p class="kpi-value"><?= (int)$mockPac['sol'] ?></p>
        <p class="kpi-sub"><?= pct($mockPac['sol'], $mockPac['total']) ?>%</p>
      </div>
    </section>

    <section class="grid2 mt-4">
      <div class="card">
        <div class="card-head">
          <h3>Por estado</h3>
          <span class="muted"><?= (int)$mockPac['total'] ?> total</span>
        </div>
        <div class="bars">
          <?php foreach (topN($mockPac['byEstado'], 6) as $k => $v): $p = pct($v, $mockPac['total']); ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-title"><?= htmlspecialchars($k) ?></span>
                <span class="bar-num"><?= (int)$v ?> (<?= $p ?>%)</span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width: <?= $p ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-head">
          <h3>Por selección</h3>
          <span class="muted">Top</span>
        </div>
        <div class="bars">
          <?php foreach (topN($mockPac['bySel'], 6) as $k => $v): $p = pct($v, $mockPac['total']); ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-title"><?= htmlspecialchars($k) ?></span>
                <span class="bar-num"><?= (int)$v ?> (<?= $p ?>%)</span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width: <?= $p ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="grid2 mt-4">
      <div class="card">
        <div class="card-head">
          <h3>PAC por OBAC</h3>
          <span class="muted">Cantidad</span>
        </div>
        <div class="bars">
          <?php foreach (topN($mockPac['byObac'], 6) as $k => $v): $p = pct($v, $mockPac['total']); ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-title"><?= htmlspecialchars($k) ?></span>
                <span class="bar-num"><?= (int)$v ?> (<?= $p ?>%)</span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width: <?= $p ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-head">
          <h3>Monto por OBAC</h3>
          <span class="muted">Estimado</span>
        </div>
        <div class="table">
          <?php foreach (topN($mockPac['sumByObac'], 6) as $k => $s): ?>
            <div class="tr">
              <div class="td strong"><?= htmlspecialchars($k) ?></div>
              <div class="td right"><?= fmt_money($s) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </section>

  <!-- PROCESOS -->
  <section id="tab-procesos" class="tab-panel hidden">
    <section class="kpis">
      <div class="kpi">
        <p class="kpi-label">Total procesos</p>
        <p class="kpi-value"><?= (int)$mockPro['total'] ?></p>
        <p class="kpi-sub">Registros</p>
      </div>
      <div class="kpi">
        <p class="kpi-label">Monto total</p>
        <p class="kpi-value"><?= fmt_money($mockPro['totalSoles']) ?></p>
        <p class="kpi-sub">Estimado</p>
      </div>
      <div class="kpi">
        <p class="kpi-label">Adjudicado</p>
        <p class="kpi-value"><?= (int)$mockPro['adj'] ?></p>
        <p class="kpi-sub"><?= pct($mockPro['adj'], $mockPro['total']) ?>%</p>
      </div>
      <div class="kpi">
        <p class="kpi-label">Convocado</p>
        <p class="kpi-value"><?= (int)$mockPro['con'] ?></p>
        <p class="kpi-sub"><?= pct($mockPro['con'], $mockPro['total']) ?>%</p>
      </div>
    </section>

    <section class="grid2 mt-4">
      <div class="card">
        <div class="card-head">
          <h3>Por estado</h3>
          <span class="muted"><?= (int)$mockPro['total'] ?> total</span>
        </div>
        <div class="bars">
          <?php foreach (topN($mockPro['byEstado'], 6) as $k => $v): $p = pct($v, $mockPro['total']); ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-title"><?= htmlspecialchars($k) ?></span>
                <span class="bar-num"><?= (int)$v ?> (<?= $p ?>%)</span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width: <?= $p ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-head">
          <h3>Por tipo</h3>
          <span class="muted">Prefijo</span>
        </div>
        <div class="bars">
          <?php foreach (topN($mockPro['byTipo'], 6) as $k => $v): $p = pct($v, $mockPro['total']); ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-title"><?= htmlspecialchars($k) ?></span>
                <span class="bar-num"><?= (int)$v ?> (<?= $p ?>%)</span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width: <?= $p ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="grid2 mt-4">
      <div class="card">
        <div class="card-head">
          <h3>Procesos por OBAC</h3>
          <span class="muted">Cantidad</span>
        </div>
        <div class="bars">
          <?php foreach (topN($mockPro['byObac'], 6) as $k => $v): $p = pct($v, $mockPro['total']); ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-title"><?= htmlspecialchars($k) ?></span>
                <span class="bar-num"><?= (int)$v ?> (<?= $p ?>%)</span>
              </div>
              <div class="bar-track"><div class="bar-fill" style="width: <?= $p ?>%"></div></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-head">
          <h3>Monto por OBAC</h3>
          <span class="muted">Estimado</span>
        </div>
        <div class="table">
          <?php foreach (topN($mockPro['sumByObac'], 6) as $k => $s): ?>
            <div class="tr">
              <div class="td strong"><?= htmlspecialchars($k) ?></div>
              <div class="td right"><?= fmt_money($s) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .page-shell{width:100%}
  @media (min-width:1024px){.page-shell{max-width:1120px;margin:0 auto;padding-left:24px;padding-right:24px}}
  .main-ind{overflow:hidden}
  .filtros-sticky{position:sticky;top:0;z-index:80;padding-top:6px}
  .filtros-sticky>div{background:rgba(255,255,255,.96)!important;border:1px solid rgba(148,163,184,.25);box-shadow:0 18px 40px rgba(0,0,0,.18);backdrop-filter:blur(12px)}
  .year-pill{display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:9999px;background:rgba(107,28,38,.10);color:#6B1C26;border:1px solid rgba(107,28,38,.15);font-weight:800;font-size:.85rem;white-space:nowrap}
  .year-pill .dot{width:8px;height:8px;border-radius:9999px;background:#C9A227;box-shadow:0 0 0 3px rgba(201,162,39,.20)}
  .tabs{display:flex;gap:10px}
  .tab{height:44px;padding:0 14px;border-radius:9999px;border:1px solid rgba(148,163,184,.35);background:#f8fafc;font-weight:900;color:#0f172a}
  .tab-active{background:rgba(107,28,38,.12);color:#6B1C26;border-color:rgba(107,28,38,.22)}
  .kpis{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  @media (min-width:1024px){.kpis{grid-template-columns:1fr 1fr 1fr 1fr}}
  .kpi{background:#fff;border-radius:18px;padding:14px;box-shadow:0 10px 25px rgba(0,0,0,.10);border:1px solid rgba(148,163,184,.18)}
  .kpi-label{font-weight:900;font-size:.82rem;color:#475569}
  .kpi-value{font-weight:950;font-size:1.15rem;color:#0f172a;margin-top:6px}
  .kpi-sub{font-weight:800;font-size:.78rem;color:#64748b;margin-top:4px}
  .grid2{display:grid;grid-template-columns:1fr;gap:12px}
  @media (min-width:1024px){.grid2{grid-template-columns:1fr 1fr}}
  .card{background:#fff;border-radius:20px;padding:14px;box-shadow:0 10px 25px rgba(0,0,0,.10);border:1px solid rgba(148,163,184,.18)}
  .card-head{display:flex;align-items:baseline;justify-content:space-between;gap:10px;margin-bottom:10px}
  .card h3{font-weight:950;color:#0f172a;font-size:1rem}
  .muted{color:#64748b;font-weight:800;font-size:.78rem}
  .bars{display:flex;flex-direction:column;gap:10px}
  .bar-row{display:flex;flex-direction:column;gap:6px}
  .bar-meta{display:flex;justify-content:space-between;gap:10px;align-items:center}
  .bar-title{font-weight:900;color:#0f172a;font-size:.85rem}
  .bar-num{font-weight:900;color:#475569;font-size:.82rem;white-space:nowrap}
  .bar-track{width:100%;height:10px;border-radius:999px;background:rgba(148,163,184,.18);overflow:hidden}
  .bar-fill{height:100%;border-radius:999px;background:rgba(107,28,38,.75)}
  .table{display:flex;flex-direction:column;gap:8px}
  .tr{display:flex;justify-content:space-between;gap:12px;padding:10px 12px;border-radius:14px;background:rgba(148,163,184,.10)}
  .td{font-weight:900;color:#0f172a;font-size:.85rem}
  .td.right{text-align:right}
  .td.strong{color:#6B1C26}
  .hidden{display:none!important}
</style>

<script>
  const tabs = document.querySelectorAll('[data-tab]');
  const panelPac = document.getElementById('tab-pac');
  const panelPro = document.getElementById('tab-procesos');

  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      tabs.forEach(x => x.classList.remove('tab-active'));
      btn.classList.add('tab-active');

      const t = btn.getAttribute('data-tab');
      if (t === 'pac') {
        panelPac.classList.remove('hidden');
        panelPro.classList.add('hidden');
      } else {
        panelPro.classList.remove('hidden');
        panelPac.classList.add('hidden');
      }
    });
  });
</script>