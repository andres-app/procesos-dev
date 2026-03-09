<?php
// Vista/modulos/presupuesto.php (PWA / Gerencial)
// Maqueta (sin BD) con: header sticky + KPIs calculados + summary + búsqueda + filtros (bottom sheet) + cards + kebab

$titulo  = 'Presupuesto | Presupuesto';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function fmt_money($n){ return 'S/ ' . number_format((float)$n, 2, '.', ','); }
function clamp0($n){ return max(0, (float)$n); }

/* =========================
   MAQUETA (PRESUPUESTO)
   Campos: codigo, obac, categoria, fuente, descripcion, aprobado, ajustes, compromiso, devengado, pagado, estado
   ========================= */
$lineas = [
  [
    'id'=>101, 'codigo'=>'2.3.2.1.1', 'obac'=>'EP',  'categoria'=>'Bienes', 'fuente'=>'RO',
    'descripcion'=>'Adquisición de papelería y útiles de oficina (catálogos electrónicos)',
    'aprobado'=> 750000, 'ajustes'=>  50000, 'compromiso'=> 420000, 'devengado'=> 280000, 'pagado'=> 240000,
    'estado'=>'VIGENTE'
  ],
  [
    'id'=>102, 'codigo'=>'2.3.2.2.3', 'obac'=>'MGP', 'categoria'=>'Servicios', 'fuente'=>'RO',
    'descripcion'=>'Servicio de mantenimiento preventivo de equipos de comunicaciones',
    'aprobado'=>1280000, 'ajustes'=> -80000, 'compromiso'=> 640000, 'devengado'=> 510000, 'pagado'=> 490000,
    'estado'=>'VIGENTE'
  ],
  [
    'id'=>103, 'codigo'=>'2.3.2.1.4', 'obac'=>'FAP', 'categoria'=>'Bienes', 'fuente'=>'RO',
    'descripcion'=>'Compra de repuestos críticos (stock estratégico)',
    'aprobado'=> 980000, 'ajustes'=>      0, 'compromiso'=> 310000, 'devengado'=> 110000, 'pagado'=>  80000,
    'estado'=>'EN REV.'
  ],
  [
    'id'=>104, 'codigo'=>'2.6.2.1.1', 'obac'=>'EP',  'categoria'=>'Obras', 'fuente'=>'RO',
    'descripcion'=>'Adecuación de almacenes para bienes estratégicos (tramo 1)',
    'aprobado'=>1900000, 'ajustes'=> 150000, 'compromiso'=> 420000, 'devengado'=> 210000, 'pagado'=> 120000,
    'estado'=>'PROGRAM.'
  ],
];

$ejercicio = 'AF-2026';

// KPIs (calculados)
$aprob = 0; $ajus = 0; $vig = 0; $comp = 0; $dev = 0; $pag = 0;
foreach($lineas as $r){
  $aprob += (float)$r['aprobado'];
  $ajus  += (float)$r['ajustes'];
  $vig   += ((float)$r['aprobado'] + (float)$r['ajustes']);
  $comp  += (float)$r['compromiso'];
  $dev   += (float)$r['devengado'];
  $pag   += (float)$r['pagado'];
}
$saldo = clamp0($vig - $comp);
$brecha = (float)$comp - (float)$pag;
$ejec = $vig > 0 ? round(($pag / $vig) * 100, 1) : 0;

// Estado gerencial (simple)
$semGlobal = 'OK';
if ($saldo <= 0) $semGlobal = 'ZERO';
else if ($vig > 0 && ($saldo / $vig) <= 0.10) $semGlobal = 'LOW';

function pillTone($tone){
  return match($tone){
    'vino'  => 'bg-vino-50 text-vino-900 border-vino-100',
    'dorado'=> 'bg-dorado-50 text-dorado-900 border-dorado-100',
    'gris'  => 'bg-slate-100 text-slate-700 border-slate-200',
    default => 'bg-slate-100 text-slate-700 border-slate-200'
  };
}
function badge($txt, $tone='gris'){
  $c = pillTone($tone);
  return '<span class="pill '.$c.'">'.h($txt).'</span>';
}
?>
<main class="page page-shell flex-1 px-5 pt-4 main-presupuesto">

  <!-- 1) HEADER STICKY -->
  <section class="mb-5 filtros-sticky">
    <div class="hdr-card">
      <div class="hdr-top">
        <div>
          <p class="hdr-sub">Planeamiento y presupuesto</p>
          <h2 class="hdr-title">Presupuesto</h2>
        </div>

        <div class="year-pill">
          <span class="dot"></span>
          <span><?= h($ejercicio) ?></span>
        </div>
      </div>

      <!-- KPIs (gerencial) -->
      <div class="kpis">
        <div class="kpi">
          <p class="k">Vigente</p>
          <p class="v"><?= h(fmt_money($vig)) ?></p>
          <p class="h">Aprobado + ajustes</p>
        </div>
        <div class="kpi">
          <p class="k">Compromiso</p>
          <p class="v"><?= h(fmt_money($comp)) ?></p>
          <p class="h">Contratos / OC</p>
        </div>
        <div class="kpi">
          <p class="k">Saldo</p>
          <p class="v"><?= h(fmt_money($saldo)) ?></p>
          <p class="h">Vigente − Comp.</p>
        </div>
      </div>

      <!-- Barra única (search + filtros) -->
      <div class="searchbar" role="search">
        <span class="s-ico" aria-hidden="true">⌕</span>
        <input id="q" type="text" placeholder="Buscar código, descripción…" />
        <button id="btnFiltros" class="s-btn" type="button"
          aria-haspopup="dialog" aria-controls="sheetFiltros" aria-label="Filtros">⚙️</button>
        <span id="badgeCount" class="badge-count hidden">0</span>
      </div>

      <!-- Chips activos -->
      <div id="chipsActivos" class="chips-activos hidden"></div>
    </div>
  </section>

  <!-- 2) RESUMEN GERENCIAL -->
  <section class="mb-4">
    <div class="summary-card">
      <div class="summary-row">
        <div class="summary-left">
          <p class="summary-k">Ejecución</p>
          <p class="summary-v"><?= h($ejec) ?>%</p>
        </div>

        <div class="summary-right">
          <?php if ($semGlobal === 'ZERO'): ?>
            <span class="pill pill-bad">Crítico</span>
            <span class="pill pill-warn">Saldo global 0</span>
          <?php elseif ($semGlobal === 'LOW'): ?>
            <span class="pill pill-warn">Cerca</span>
            <span class="pill pill-info">≤ 10% saldo global</span>
          <?php else: ?>
            <span class="pill pill-ok">OK</span>
            <span class="pill pill-info">Sin alertas críticas</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="hr"></div>

      <div class="summary-grid">
        <div class="mini">
          <p class="k">Devengado</p>
          <p class="v"><?= h(fmt_money($dev)) ?></p>
          <p class="h">Conformidades</p>
        </div>
        <div class="mini">
          <p class="k">Pagado</p>
          <p class="v"><?= h(fmt_money($pag)) ?></p>
          <p class="h">Tesorería</p>
        </div>
        <div class="mini">
          <p class="k">Brecha</p>
          <p class="v"><?= h(fmt_money($brecha)) ?></p>
          <p class="h">Comp − Pag</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 3) LISTA (UNA SOLA) -->
  <section class="lista-scroll">
    <section class="space-y-3" id="listaLineas">
      <?php foreach($lineas as $r): ?>
        <?php
          $vigente = (float)$r['aprobado'] + (float)$r['ajustes'];
          $saldoLn = clamp0($vigente - (float)$r['compromiso']);
          $pctComp = $vigente > 0 ? min(100, round(((float)$r['compromiso'] / $vigente) * 100, 1)) : 0;

          // semáforo (por línea)
          $sem = 'OK';
          if ($saldoLn <= 0) $sem = 'ZERO';
          else if ($vigente > 0 && ($saldoLn / $vigente) <= 0.10) $sem = 'LOW';

          $hay = strtoupper(trim(
            $r['codigo'].' '.$r['obac'].' '.$r['categoria'].' '.$r['fuente'].' '.$r['descripcion'].' '.$r['estado']
          ));
        ?>
        <article class="bud-card"
          data-open="<?= BASE_URL ?>/presupuesto/detalle?id=<?= (int)$r['id'] ?>"
          data-obac="<?= h($r['obac']) ?>"
          data-cat="<?= h($r['categoria']) ?>"
          data-estado="<?= h(strtoupper(trim($r['estado']))) ?>"
          data-sem="<?= h($sem) ?>"
          data-hay="<?= h($hay) ?>"
        >
          <a class="bud-open" href="<?= BASE_URL ?>/presupuesto/detalle?id=<?= (int)$r['id'] ?>" aria-label="Abrir línea"></a>

          <div class="bud-head">
            <div class="bud-head-left">
              <div class="badge-obac"><?= h($r['obac']) ?></div>
              <div class="bud-txt">
                <div class="bud-title">
                  <span class="code"><?= h($r['codigo']) ?></span>
                  <span class="dot-sep">•</span>
                  <span class="cat"><?= h($r['categoria']) ?></span>
                </div>
                <div class="bud-meta">
                  <span class="meta"><?= h($r['fuente']) ?></span>
                  <span class="dot-sep">•</span>
                  <span class="meta"><?= h($r['estado']) ?></span>
                </div>
              </div>
            </div>

            <div class="bud-head-right">
              <?php if ($sem === 'ZERO'): ?>
                <span class="pill pill-bad">SIN SALDO</span>
              <?php elseif ($sem === 'LOW'): ?>
                <span class="pill pill-warn">CERCA</span>
              <?php else: ?>
                <span class="pill pill-ok">OK</span>
              <?php endif; ?>

              <div class="actions">
                <button class="kebab" type="button" aria-label="Acciones" data-menu-btn></button>
                <div class="menu hidden" data-menu>
                  <a class="menu-item" href="<?= BASE_URL ?>/presupuesto/detalle?id=<?= (int)$r['id'] ?>">
                    <span class="mi">👁️</span> Ver
                  </a>
                  <a class="menu-item" href="<?= BASE_URL ?>/presupuesto/editar?id=<?= (int)$r['id'] ?>">
                    <span class="mi">✏️</span> Editar
                  </a>
                  <button class="menu-item danger" type="button"
                    data-anular
                    data-id="<?= (int)$r['id'] ?>"
                    data-name="<?= h($r['codigo']) ?>">
                    <span class="mi">🧾</span> Anular
                  </button>
                </div>
              </div>
            </div>
          </div>

          <p class="bud-desc"><?= h($r['descripcion']) ?></p>

          <div class="bud-amount">
            <div>
              <p class="k">Saldo disponible</p>
              <p class="v"><?= h(fmt_money($saldoLn)) ?></p>
            </div>
            <div class="right-metrics">
              <div class="m">
                <p class="k">Vigente</p>
                <p class="v2"><?= h(fmt_money($vigente)) ?></p>
              </div>
              <div class="m">
                <p class="k">Comp.</p>
                <p class="v2"><?= h(fmt_money($r['compromiso'])) ?></p>
              </div>
            </div>
          </div>

          <div class="pbar" aria-label="Porcentaje comprometido">
            <span style="width:<?= h($pctComp) ?>%"></span>
          </div>

          <div class="pmeta">
            <span><?= h($pctComp) ?>% comprometido</span>
            <span>Dev: <?= h(fmt_money($r['devengado'])) ?> · Pag: <?= h(fmt_money($r['pagado'])) ?></span>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <div class="mt-4 text-xs text-white/70" id="countLineas">
      Mostrando <?= count($lineas) ?> de <?= count($lineas) ?> líneas
    </div>
  </section>

  <!-- SHEET FILTROS -->
  <div id="overlayFiltros" class="overlay hidden" aria-hidden="true"></div>

  <div id="sheetFiltros" class="sheet hidden" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
    <div class="sheet-handle" aria-hidden="true"></div>

    <div class="sheet-head">
      <div>
        <p class="text-xs text-slate-500">Filtra y encuentra rápido</p>
        <h3 id="sheetTitle" class="text-lg font-black text-slate-900">Filtros</h3>
      </div>
      <button id="btnCerrarSheet" class="sheet-close" type="button" aria-label="Cerrar"></button>
    </div>

    <div class="sheet-body">
      <div class="sheet-section">
        <p class="sheet-label">OBAC</p>
        <div class="chips-grid" id="fObac">
          <button class="chip chip-active" type="button" data-filter="obac" data-value="ALL">Todos</button>
          <button class="chip" type="button" data-filter="obac" data-value="EP">EP</button>
          <button class="chip" type="button" data-filter="obac" data-value="FAP">FAP</button>
          <button class="chip" type="button" data-filter="obac" data-value="MGP">MGP</button>
        </div>
      </div>

      <div class="sheet-section">
        <p class="sheet-label">Categoría</p>
        <div class="chips-grid" id="fCat">
          <button class="chip chip-active" type="button" data-filter="cat" data-value="ALL">Todas</button>
          <button class="chip chip-soft" type="button" data-filter="cat" data-value="Bienes">Bienes</button>
          <button class="chip chip-soft" type="button" data-filter="cat" data-value="Servicios">Servicios</button>
          <button class="chip chip-soft" type="button" data-filter="cat" data-value="Obras">Obras</button>
        </div>
      </div>

      <div class="sheet-section">
        <p class="sheet-label">Estado</p>
        <div class="chips-grid" id="fEstado">
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="VIGENTE">Vigente</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="EN REV.">En revisión</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="PROGRAM.">Programado</button>
        </div>
      </div>

      <div class="sheet-section">
        <p class="sheet-label">Semáforo (saldo)</p>
        <div class="chips-grid" id="fSemaforo">
          <button class="chip chip-active" type="button" data-filter="sem" data-value="ALL">Todos</button>
          <button class="chip chip-soft" type="button" data-filter="sem" data-value="OK">Con saldo</button>
          <button class="chip chip-soft" type="button" data-filter="sem" data-value="LOW">Cerca al límite</button>
          <button class="chip chip-soft" type="button" data-filter="sem" data-value="ZERO">Sin saldo</button>
        </div>
      </div>
    </div>

    <div class="sheet-actions">
      <button id="btnLimpiar" class="btn-secondary" type="button">Limpiar</button>
      <button id="btnAplicar" class="btn-primary" type="button">Aplicar</button>
    </div>
  </div>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
/* =========================
   PRESUPUESTO PWA (GERENCIAL) — CSS
========================= */
:root{
  --vino:#6B1C26;
  --vino-50: rgba(107,28,38,.12);
  --vino-100: rgba(107,28,38,.22);

  --dorado:#7A5B00;
  --dorado-50: rgba(201,162,39,.15);
  --dorado-100: rgba(201,162,39,.22);

  --line: rgba(148,163,184,.22);
  --muted: rgba(15,23,42,.55);
  --bg: rgba(255,255,255,.96);
}

.main-presupuesto{ overflow:hidden; }

.filtros-sticky{
  position: sticky;
  top: 0;
  z-index: 80;
  padding-top: 6px;
}

.hdr-card{
  background: var(--bg);
  border: 1px solid rgba(148,163,184,.25);
  box-shadow: 0 18px 40px rgba(0,0,0,.16);
  backdrop-filter: blur(12px);
  border-radius: 22px;
  padding: 14px;
}

.hdr-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.hdr-sub{font-size:12px;font-weight:900;color:var(--muted)}
.hdr-title{font-size:22px;font-weight:900;letter-spacing:-.02em;line-height:1.05;color:#0f172a;margin-top:2px}

.year-pill{
  display:flex;align-items:center;gap:8px;
  padding:10px 12px;border-radius:9999px;
  background: var(--vino-50);
  color: var(--vino);
  border:1px solid rgba(107,28,38,.15);
  font-weight:900;font-size:.85rem;white-space:nowrap;
}
.year-pill .dot{
  width:8px;height:8px;border-radius:9999px;
  background:#C9A227;
  box-shadow:0 0 0 3px rgba(201,162,39,.18);
}

/* KPIs */
.kpis{
  margin-top:12px;
  display:grid;
  grid-template-columns:repeat(3,minmax(0,1fr));
  gap:10px;
}
.kpi{
  border-radius:16px;
  background:rgba(255,255,255,.90);
  border:1px solid var(--line);
  padding:10px 12px;
}
.kpi .k{font-size:11px;font-weight:900;color:var(--muted)}
.kpi .v{margin-top:2px;font-size:13px;font-weight:900;color:#0f172a;white-space:nowrap}
.kpi .h{margin-top:2px;font-size:10.5px;font-weight:900;color:rgba(15,23,42,.40)}

/* Searchbar */
.searchbar{
  margin-top:12px;
  display:flex;align-items:center;gap:10px;
  height:48px;
  padding:0 12px;
  border-radius:16px;
  background:rgba(248,250,252,.95);
  border:1px solid rgba(148,163,184,.30);
  position: relative;
}
.s-ico{opacity:.6;font-weight:900}
.searchbar input{
  flex:1;border:0;outline:0;background:transparent;
  font-weight:900;font-size:16px!important;color:#0f172a;
}
.searchbar input::placeholder{color:#94a3b8;font-weight:900}
.s-btn{
  width:38px;height:38px;border-radius:14px;
  border:1px solid rgba(148,163,184,.26);
  background:rgba(255,255,255,.92);
}
.badge-count{
  position:absolute;
  right:8px;
  top:-8px;
  min-width:22px;height:22px;padding:0 7px;
  border-radius:999px;
  background: var(--vino-50);
  color: var(--vino);
  border:1px solid var(--vino-100);
  font-size:.75rem;
  display:inline-flex;align-items:center;justify-content:center;
}
.hidden{display:none!important}

/* Chips activos */
.chips-activos{
  margin-top:10px;
  display:flex;gap:8px;overflow:auto;padding-bottom:2px;
}
.chip-x{
  padding:8px 10px;
  border-radius:9999px;
  border:1px solid rgba(148,163,184,.30);
  background: rgba(255,255,255,.92);
  font-weight:900;
  font-size:.78rem;
  color:#0f172a;
  display:flex;align-items:center;gap:8px;
  flex:0 0 auto;
}
.chip-x button{
  width:20px;height:20px;border-radius:999px;
  border:1px solid rgba(148,163,184,.30);
  background:#f8fafc;
  font-weight:900;
  line-height:1;
}

/* Summary */
.summary-card{
  border-radius:22px;
  background:#fff;
  border:1px solid rgba(148,163,184,.18);
  box-shadow:0 14px 34px rgba(0,0,0,.10);
  padding:14px;
}
.summary-row{display:flex;align-items:center;justify-content:space-between;gap:12px}
.summary-k{font-size:11px;font-weight:900;color:var(--muted)}
.summary-v{font-size:18px;font-weight:900;color:#0f172a;margin-top:2px}
.summary-right{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
.hr{height:1px;background:rgba(148,163,184,.16);margin:12px 0}
.summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
.mini{
  border-radius:16px;
  background:rgba(248,250,252,.78);
  border:1px solid rgba(148,163,184,.16);
  padding:10px 12px;
}
.mini .k{font-size:11px;font-weight:900;color:var(--muted)}
.mini .v{margin-top:2px;font-size:13px;font-weight:900;color:#0f172a;white-space:nowrap}
.mini .h{margin-top:2px;font-size:10.5px;font-weight:900;color:rgba(15,23,42,.40)}

/* Lista: estable en PWA */
.lista-scroll{
  overflow-y:auto;
  -webkit-overflow-scrolling:touch;
  max-height: calc(100dvh - 260px);
  padding-bottom: calc(140px + env(safe-area-inset-bottom));
}
@media (min-width:1024px){
  .lista-scroll{ max-height: calc(100dvh - 220px); padding-bottom: 40px; }
}

/* Card */
.bud-card{
  position:relative;
  background:#fff;
  border-radius:22px;
  border:1px solid rgba(148,163,184,.18);
  box-shadow:0 16px 40px rgba(0,0,0,.10);
  padding:14px;
}
.bud-open{position:absolute;inset:0;z-index:1;border-radius:22px}
.bud-card *{position:relative;z-index:2}

.bud-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.bud-head-left{display:flex;align-items:flex-start;gap:12px;min-width:0}
.badge-obac{
  width:42px;height:42px;border-radius:999px;
  display:flex;align-items:center;justify-content:center;
  font-weight:900;font-size:.82rem;
  background: var(--vino-50);
  color: var(--vino);
}
.bud-txt{min-width:0}
.bud-title{font-weight:900;color:#0f172a;display:flex;flex-wrap:wrap;gap:6px;line-height:1.1}
.code{
  font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
  font-weight:900;
}
.cat{font-weight:900;color:#0f172a}
.dot-sep{opacity:.35;padding:0 2px}
.bud-meta{margin-top:4px;font-size:11px;font-weight:900;color:var(--muted);display:flex;gap:8px;flex-wrap:wrap}
.bud-head-right{display:flex;align-items:center;gap:10px;flex:0 0 auto}

.bud-desc{
  margin-top:10px;
  font-size:.88rem;
  font-weight:800;
  color:#334155;
  line-height:1.35;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
}

.bud-amount{
  margin-top:12px;
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:12px;
}
.bud-amount .k{font-size:11px;font-weight:900;color:var(--muted)}
.bud-amount .v{margin-top:2px;font-size:18px;font-weight:900;color:#0f172a;white-space:nowrap}
.right-metrics{display:flex;flex-direction:column;gap:8px;align-items:flex-end}
.m .k{font-size:10.5px;font-weight:900;color:rgba(15,23,42,.42);text-align:right}
.m .v2{font-size:12.5px;font-weight:900;color:#0f172a;white-space:nowrap}

/* Progress */
.pbar{
  margin-top:12px;
  height:8px;border-radius:999px;
  background:rgba(226,232,240,.92);
  overflow:hidden;
  border:1px solid rgba(148,163,184,.16);
}
.pbar>span{
  display:block;height:100%;border-radius:999px;
  background: linear-gradient(90deg, rgba(15,23,42,.85), rgba(15,23,42,.45));
}
.pmeta{
  margin-top:8px;
  display:flex;
  justify-content:space-between;
  gap:12px;
  font-size:11px;
  font-weight:900;
  color:rgba(15,23,42,.55);
}

/* Pills */
.pill{
  display:inline-flex;
  align-items:center;
  padding:6px 10px;
  border-radius:999px;
  border:1px solid rgba(148,163,184,.22);
  font-size:11px;
  font-weight:900;
  white-space:nowrap;
}
.pill-ok{background: rgba(16,185,129,.12); color: rgba(6,95,70,.95); border-color: rgba(16,185,129,.22)}
.pill-warn{background: var(--dorado-50); color: var(--dorado); border-color: var(--dorado-100)}
.pill-info{background: rgba(148,163,184,.14); color:#334155; border-color: rgba(148,163,184,.22)}
.pill-bad{background: rgba(239,68,68,.12); color:#991b1b; border-color: rgba(239,68,68,.22)}

/* Kebab menu */
.actions{position:relative;z-index:3}
.kebab{
  width:34px;height:34px;border-radius:999px;
  border:1px solid rgba(148,163,184,.28);
  background:rgba(248,250,252,.92);
  display:grid;place-items:center;
  padding:0;
}
.kebab::before{content:"⋯";font-weight:900;font-size:1.25rem;transform:translateY(-1px);color:#0f172a}
.menu{
  position:absolute;top:40px;right:0;
  width:190px;background:#fff;border-radius:14px;
  box-shadow:0 18px 40px rgba(0,0,0,.22);
  overflow:hidden;z-index:999;
}
.menu-item{
  padding:12px 14px;
  display:flex;align-items:center;gap:10px;
  font-weight:900;font-size:.85rem;
  color:#0f172a;text-decoration:none;
  border:0;background:transparent;
  width:100%;text-align:left;cursor:pointer;
}
.menu-item:hover{background:#f1f5f9}
.menu-item .mi{width:22px;display:inline-flex;justify-content:center}
.menu-item.danger{color:#b91c1c}

/* Overlay + Sheet (simple) */
.overlay{
  position:fixed; inset:0;
  background:rgba(15,23,42,.35);
  backdrop-filter: blur(2px);
  z-index: 2000;
}
.sheet{
  position:fixed; left:0; right:0; bottom:0;
  background:#fff;
  border-top-left-radius:22px;
  border-top-right-radius:22px;
  box-shadow:0 -20px 60px rgba(0,0,0,.25);
  z-index: 2100;
  padding: 10px 14px 14px;
}
.sheet-handle{
  width:48px; height:5px;
  border-radius:999px;
  background:rgba(148,163,184,.45);
  margin: 6px auto 10px;
}
.sheet-head{display:flex;align-items:center;justify-content:space-between;gap:12px}
.sheet-close{
  width:40px;height:40px;border-radius:14px;
  border:1px solid rgba(148,163,184,.28);
  background:rgba(248,250,252,.92);
  position:relative;
}
.sheet-close::before{
  content:"×";
  position:absolute; inset:0;
  display:grid; place-items:center;
  font-weight:900; font-size:1.35rem;
  color:#0f172a;
}
.sheet-body{margin-top:10px; max-height: 54vh; overflow:auto; -webkit-overflow-scrolling:touch}
.sheet-section{margin-top:14px}
.sheet-label{font-size:11px;font-weight:900;color:rgba(15,23,42,.55); margin-bottom:8px}
.chips-grid{display:flex;flex-wrap:wrap;gap:10px}
.chip{
  padding:10px 12px;
  border-radius:999px;
  border:1px solid rgba(148,163,184,.28);
  background: rgba(255,255,255,.96);
  font-weight:900;
  font-size:.85rem;
}
.chip-soft{background: rgba(248,250,252,.9)}
.chip-active{
  background: var(--vino-50);
  color: var(--vino);
  border-color: var(--vino-100);
}
.sheet-actions{
  margin-top:12px;
  display:flex;
  gap:10px;
}
.btn-secondary, .btn-primary{
  flex:1;
  height:46px;
  border-radius:16px;
  font-weight:900;
  border:1px solid rgba(148,163,184,.26);
}
.btn-secondary{background: rgba(248,250,252,.95); color:#0f172a;}
.btn-primary{background: rgba(15,23,42,.92); color:#fff; border-color: rgba(15,23,42,.92);}
</style>

<script>
/* ===== KEBAB ===== */
const closeAllMenus = () => {
  document.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
};

document.addEventListener('click', (e) => {
  const btn = e.target.closest('[data-menu-btn]');
  const menu = e.target.closest('[data-menu]');

  if (btn) {
    e.preventDefault();
    e.stopPropagation();

    const wrap = btn.closest('.actions');
    const m = wrap?.querySelector('[data-menu]');
    const wasOpen = m && !m.classList.contains('hidden');

    closeAllMenus();
    if (m && !wasOpen) m.classList.remove('hidden');
    return;
  }

  if (menu) return;
  closeAllMenus();
});

/* ===== ANULAR (maqueta) ===== */
document.addEventListener('click', (e) => {
  const an = e.target.closest('[data-anular]');
  if (!an) return;

  e.preventDefault();
  e.stopPropagation();

  const id = an.getAttribute('data-id');
  const name = an.getAttribute('data-name') || 'esta línea';

  if (!confirm(`¿Anular ${name}?`)) return;
  window.location.href = `/presupuesto/anular?id=${id}`;
});

/* ===== FILTROS ===== */
let draft   = { obac:'ALL', cat:'ALL', estado:null, sem:'ALL' };
let applied = { obac:'ALL', cat:'ALL', estado:null, sem:'ALL' };

const q = document.getElementById('q');
const list = document.getElementById('listaLineas');
const countEl = document.getElementById('countLineas');
const chipsActivos = document.getElementById('chipsActivos');
const badgeCount = document.getElementById('badgeCount');

const setChipActive = (rootId, filterKey, value) => {
  const root = document.getElementById(rootId);
  if (!root) return;
  root.querySelectorAll('[data-filter="'+filterKey+'"]').forEach(b => {
    const v = b.getAttribute('data-value');
    b.classList.toggle('chip-active', v === value);
  });
};

const syncSheetUIFromDraft = () => {
  setChipActive('fObac', 'obac', draft.obac);
  setChipActive('fCat', 'cat', draft.cat);
  setChipActive('fSemaforo', 'sem', draft.sem);

  const fe = document.getElementById('fEstado');
  if (fe) {
    fe.querySelectorAll('[data-filter="estado"]').forEach(b => {
      const v = (b.getAttribute('data-value') || '').toUpperCase();
      b.classList.toggle('chip-active', draft.estado && v === draft.estado.toUpperCase());
    });
  }
};

const renderActiveChips = () => {
  const items = [];
  if (applied.obac !== 'ALL') items.push({k:'obac', label: applied.obac});
  if (applied.cat !== 'ALL') items.push({k:'cat', label: applied.cat});
  if (applied.sem !== 'ALL') items.push({
    k:'sem',
    label: applied.sem === 'OK' ? 'Con saldo' : applied.sem === 'LOW' ? 'Cerca al límite' : 'Sin saldo'
  });
  if (applied.estado) items.push({k:'estado', label: applied.estado});

  const count = items.length;
  if (count > 0) {
    chipsActivos.classList.remove('hidden');
    badgeCount.classList.remove('hidden');
    badgeCount.textContent = String(count);
  } else {
    chipsActivos.classList.add('hidden');
    badgeCount.classList.add('hidden');
  }

  chipsActivos.innerHTML = items.map(it => `
    <div class="chip-x" data-k="${it.k}">
      <span>${it.label}</span>
      <button type="button" aria-label="Quitar filtro">×</button>
    </div>
  `).join('');
};

chipsActivos?.addEventListener('click', (e) => {
  const chip = e.target.closest('.chip-x');
  const btn = e.target.closest('button');
  if (!chip || !btn) return;

  const k = chip.getAttribute('data-k');
  if (k === 'obac') applied.obac = 'ALL';
  if (k === 'cat') applied.cat = 'ALL';
  if (k === 'sem') applied.sem = 'ALL';
  if (k === 'estado') applied.estado = null;

  draft = { ...applied };
  renderActiveChips();
  applyFilters();
});

const applyFilters = () => {
  const term = (q?.value || '').trim().toUpperCase();
  const cards = list ? Array.from(list.querySelectorAll('.bud-card')) : [];
  let visible = 0;

  cards.forEach(card => {
    const obac = (card.getAttribute('data-obac') || '').toUpperCase();
    const cat  = (card.getAttribute('data-cat') || '');
    const est  = (card.getAttribute('data-estado') || '').toUpperCase();
    const sem  = (card.getAttribute('data-sem') || '');
    const hay  = (card.getAttribute('data-hay') || '').toUpperCase();

    const okSearch = !term || hay.includes(term);
    const okObac = applied.obac === 'ALL' || obac === applied.obac;
    const okCat  = applied.cat  === 'ALL' || cat === applied.cat;
    const okSem  = applied.sem  === 'ALL' || sem === applied.sem;
    const okEst  = !applied.estado || est === applied.estado.toUpperCase();

    const show = okSearch && okObac && okCat && okSem && okEst;
    card.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  if (countEl) countEl.textContent = `Mostrando ${visible} de ${cards.length} líneas`;
};

q?.addEventListener('input', applyFilters);

/* ===== SHEET ===== */
const overlay = document.getElementById('overlayFiltros');
const sheet = document.getElementById('sheetFiltros');
const btnOpen = document.getElementById('btnFiltros');
const btnClose = document.getElementById('btnCerrarSheet');
const btnAplicar = document.getElementById('btnAplicar');
const btnLimpiar = document.getElementById('btnLimpiar');

const openSheet = () => {
  if (!overlay || !sheet) return;
  draft = { ...applied };
  syncSheetUIFromDraft();
  overlay.classList.remove('hidden');
  sheet.classList.remove('hidden');
  overlay.setAttribute('aria-hidden', 'false');
};

const closeSheet = () => {
  if (!overlay || !sheet) return;
  overlay.classList.add('hidden');
  sheet.classList.add('hidden');
  overlay.setAttribute('aria-hidden', 'true');
};

btnOpen?.addEventListener('click', openSheet);
btnClose?.addEventListener('click', closeSheet);
overlay?.addEventListener('click', closeSheet);

sheet?.addEventListener('click', (e) => {
  const chip = e.target.closest('.chip[data-filter]');
  if (!chip) return;

  const k = chip.getAttribute('data-filter');
  const v = chip.getAttribute('data-value');

  if (k === 'estado') {
    const up = (v || '').toUpperCase();
    draft.estado = (draft.estado && draft.estado.toUpperCase() === up) ? null : v;
  } else {
    draft[k] = v;
  }
  syncSheetUIFromDraft();
});

btnLimpiar?.addEventListener('click', () => {
  draft = { obac:'ALL', cat:'ALL', estado:null, sem:'ALL' };
  syncSheetUIFromDraft();
});

btnAplicar?.addEventListener('click', () => {
  applied = { ...draft };
  renderActiveChips();
  applyFilters();
  closeSheet();
});

/* Inicial */
renderActiveChips();
applyFilters();
</script>