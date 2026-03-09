<?php
/**
 * Vista: Admin / Detalle del Proceso (Desktop-first)
 * Archivo REAL: Vista/modulos/admin/actividades.php
 *
 * IMPORTANTE:
 * - Esta vista NO debe consultar BD.
 * - Debe recibir desde el controlador:
 *   - $proceso (array)
 *   - $actividades (array)
 * - Usa ?id=XX en la URL.
 */

$titulo = 'Detalle | Proceso';
$active = 'procesos';

require_once __DIR__ . '/../../../Config/config.php';

/* =========================
   Helpers
   ========================= */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function fmt_money($n){ return 'S/ ' . number_format((float)$n, 2, '.', ','); }
function fmt_date($d){
  if (!$d) return '-';
  $ts = strtotime((string)$d);
  return $ts ? date('d/m/Y', $ts) : h($d);
}
function badgeFromObac($obac){
  $p = explode('-', (string)$obac);
  return strtoupper(trim($p[0] ?? (string)$obac));
}
function statusPillClass($estado){
  $e = strtoupper(trim((string)$estado));
  return match($e){
    'ADJUDICADO' => 'pill-amber',
    'CONVOCADO'  => 'pill-wine',
    'OBSERVADO'  => 'pill-wine',
    'PUBLICADO'  => 'pill-slate',
    'DESIERTO'   => 'pill-slate',
    default      => 'pill-slate',
  };
}
function parseObacs($raw){
  $raw = (string)$raw;
  $parts = preg_split('/[\/,\|;]+/', $raw);
  $obacs = array_values(array_filter(array_map('trim', $parts)));
  if (empty($obacs) && $raw !== '') $obacs = [$raw];
  return $obacs;
}
function dotClassFromTipo($tipoCodigo){
  $cod = strtoupper((string)($tipoCodigo ?? ''));
  return match($cod){
    'CONVOCATORIA' => 'dot-wine',
    'CONSULTAS'    => 'dot-amber',
    'ABSOLUCION'   => 'dot-blue',
    'PROPUESTAS'   => 'dot-indigo',
    'BUENA_PRO'    => 'dot-green',
    'DESIERTO'     => 'dot-gray',
    default        => 'dot-gray',
  };
}

/* =========================
   DATA (viene del controlador)
   ========================= */
$proceso     = $proceso ?? null;
$actividades = $actividades ?? [];

// ID para links (preferimos el del proceso; fallback a GET)
$idProceso = (int)(is_array($proceso) ? ($proceso['id'] ?? 0) : 0);
if ($idProceso <= 0) $idProceso = (int)($_GET['id'] ?? 0);

require __DIR__ . '/../../layout/admin_layout.php';

if ($idProceso <= 0) {
  echo "<div class='rounded-2xl border border-slate-200 bg-white p-4'>ID inválido.</div>";
  require __DIR__ . '/../../layout/admin_footer.php';
  exit;
}

if (!$proceso || !is_array($proceso)) {
  echo "<div class='rounded-2xl border border-slate-200 bg-white p-4'>
          Proceso no encontrado.<br>
          <span class='text-xs text-slate-500'>
            (Esto pasa cuando la ruta /admin/actividades no está pasando por el controlador que carga \$proceso)
          </span>
        </div>";
  require __DIR__ . '/../../layout/admin_footer.php';
  exit;
}

$last     = !empty($actividades) ? $actividades[count($actividades)-1] : null;
$obacs    = parseObacs($proceso['obac'] ?? '');
$estadoUp = strtoupper(trim((string)($proceso['estado'] ?? '')));
?>

<div class="detail-shell space-y-4">

  <!-- TOP BAR -->
  <div class="detail-topbar">
    <div class="left">
      <a href="<?= BASE_URL ?>/admin/procesos" class="btn-icon" aria-label="Volver">←</a>

      <div class="tt">
        <div class="kicker">Detalle del proceso</div>
        <div class="title"><?= h($proceso['proceso'] ?? '-') ?></div>
      </div>
    </div>

    <div class="right">
      <span class="pill <?= h(statusPillClass($estadoUp)) ?>"><?= h($estadoUp ?: '-') ?></span>

      <a href="<?= BASE_URL ?>/admin/procesos/editar?id=<?= $idProceso ?>" class="btn-soft">Editar</a>

      <!-- kebab -->
      <div class="actions">
        <button type="button" class="btn-icon" data-menu-btn aria-label="Más acciones">⋯</button>
        <div class="menu hidden" data-menu>
          <a class="menu-item" href="<?= BASE_URL ?>/admin/actividades?id=<?= $idProceso ?>">📌 Actividades</a>
          <a class="menu-item" href="<?= BASE_URL ?>/admin/procesos/editar?id=<?= $idProceso ?>">✏️ Editar</a>

          <button class="menu-item danger" type="button"
                  data-del="<?= $idProceso ?>"
                  data-name="<?= h($proceso['proceso'] ?? 'este proceso') ?>">
            🗑️ Eliminar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- GRID 2 COL -->
  <div class="detail-grid">

    <!-- LEFT: RESUMEN -->
    <aside class="panel sticky-panel">
      <div class="panel-head">
        <div class="badge"><?= h(badgeFromObac($proceso['obac'] ?? '')) ?></div>

        <div class="meta">
          <div class="h1"><?= h($proceso['proceso'] ?? '-') ?></div>

          <div class="chips">
            <?php foreach ($obacs as $ob): ?>
              <span class="chip"><?= h($ob) ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="money"><?= fmt_money($proceso['estimado'] ?? 0) ?></div>
      </div>

      <div class="desc"><?= nl2br(h($proceso['descripcion'] ?? '')) ?></div>

      <div class="kv-grid">
        <div class="kv">
          <div class="k">Estado</div>
          <div class="v"><?= h($proceso['estado'] ?? '-') ?></div>
        </div>
        <div class="kv">
          <div class="k">Año convocatoria</div>
          <div class="v"><?= h($proceso['anio_convocatoria'] ?? '-') ?></div>
        </div>
        <div class="kv">
          <div class="k">Última actividad</div>
          <div class="v"><?= $last ? h($last['titulo'] ?? '-') : '-' ?></div>
        </div>
        <div class="kv">
          <div class="k">Fecha última</div>
          <div class="v"><?= $last ? fmt_date($last['fecha'] ?? null) : '-' ?></div>
        </div>
      </div>

      <div class="panel-actions">
        <a class="btn-primary" href="<?= BASE_URL ?>/admin/actividades?id=<?= $idProceso ?>">Ir a actividades →</a>
        <a class="btn-ghost" href="<?= BASE_URL ?>/admin/procesos/editar?id=<?= $idProceso ?>">Editar →</a>
      </div>
    </aside>

    <!-- RIGHT: TIMELINE -->
    <section class="panel">
      <div class="section-head">
        <div>
          <div class="kicker2">Seguimiento</div>
          <div class="section-title">Línea de tiempo</div>
        </div>
        <div class="pill pill-slate"><?= (int)count($actividades) ?> actividades</div>
      </div>

      <?php if (empty($actividades)): ?>
        <div class="empty">No hay actividades registradas para este proceso.</div>
      <?php else: ?>
        <ol class="timeline">
          <?php foreach ($actividades as $a): ?>
            <?php $dot = dotClassFromTipo($a['tipo_codigo'] ?? ''); ?>
            <li class="titem">
              <div class="tline"></div>
              <div class="dot <?= h($dot) ?>"></div>

              <article class="tcard">
                <div class="trow">
                  <div class="ttitle"><?= h($a['titulo'] ?? '-') ?></div>
                  <?php if (!empty($a['tipo_nombre'])): ?>
                    <span class="tbadge"><?= h($a['tipo_nombre']) ?></span>
                  <?php endif; ?>
                </div>

                <div class="tmeta">
                  <span class="tdate"><?= fmt_date($a['fecha'] ?? null) ?></span>
                  <?php if (!empty($a['tipo_codigo'])): ?>
                    <span class="sep">·</span>
                    <span class="tcode"><?= h($a['tipo_codigo']) ?></span>
                  <?php endif; ?>
                </div>

                <?php if (!empty($a['comentario'])): ?>
                  <div class="tdesc"><?= nl2br(h($a['comentario'])) ?></div>
                <?php endif; ?>
              </article>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </section>

  </div>
</div>

<style>
/* ===============================
   LAYOUT GENERAL (FULL WIDTH)
=================================*/
.detail-shell{
  width:100%;
  max-width:1600px;
  margin:0;
}

.detail-grid{
  display:grid;
  grid-template-columns:480px 1fr;
  gap:18px;
}

@media (max-width:1200px){
  .detail-grid{
    grid-template-columns:420px 1fr;
  }
}

@media (max-width:1023px){
  .detail-grid{
    grid-template-columns:1fr;
  }
}

/* ===============================
   TOP BAR
=================================*/
.detail-topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
  padding:14px 16px;
  border:1px solid #e2e8f0;
  background:#fff;
  border-radius:18px;
  position:sticky;
  top:12px;
  z-index:20;
}

.detail-topbar .left{
  display:flex;
  align-items:center;
  gap:12px;
  min-width:0;
}

.detail-topbar .right{
  display:flex;
  align-items:center;
  gap:10px;
}

.kicker{
  font-size:12px;
  color:#64748b;
  font-weight:700;
}

.title{
  font-size:18px;
  font-weight:900;
  color:#0f172a;
}

.btn-icon{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:40px;
  height:40px;
  border-radius:14px;
  border:1px solid #e2e8f0;
  background:#fff;
  cursor:pointer;
}

.btn-soft{
  display:inline-flex;
  align-items:center;
  gap:6px;
  border:1px solid #e2e8f0;
  background:#fff;
  border-radius:14px;
  padding:8px 14px;
  font-weight:800;
  font-size:13px;
}

/* ===============================
   DROPDOWN
=================================*/
.actions{position:relative;}

.menu{
  position:absolute;
  right:0;
  top:46px;
  width:200px;
  background:#fff;
  border:1px solid #e2e8f0;
  border-radius:16px;
  box-shadow:0 20px 45px rgba(15,23,42,.12);
  padding:6px;
}

.menu.hidden{display:none;}

.menu-item{
  display:flex;
  align-items:center;
  gap:10px;
  width:100%;
  padding:10px;
  border-radius:12px;
  font-size:13px;
  font-weight:800;
  color:#0f172a;
  text-decoration:none;
  border:none;
  background:transparent;
  cursor:pointer;
}

.menu-item:hover{background:#f1f5f9;}

.menu-item.danger{color:#e11d48;}

/* ===============================
   PANEL BASE
=================================*/
.panel{
  border:1px solid #e2e8f0;
  background:#fff;
  border-radius:18px;
  padding:18px;
}

.sticky-panel{
  position:sticky;
  top:92px;
  align-self:start;
}

@media (max-width:1023px){
  .sticky-panel{
    position:relative;
    top:auto;
  }
}

/* ===============================
   RESUMEN IZQUIERDA
=================================*/
.panel-head{
  display:flex;
  gap:14px;
  align-items:flex-start;
}

.badge{
  min-width:58px;
  height:58px;
  border-radius:18px;
  background:#0f172a;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:900;
}

.meta{flex:1;}

.h1{
  font-size:18px;
  font-weight:900;
  color:#0f172a;
}

.chips{
  display:flex;
  flex-wrap:wrap;
  gap:6px;
  margin-top:6px;
}

.chip{
  font-size:12px;
  font-weight:900;
  color:#334155;
  background:#f1f5f9;
  border:1px solid #e2e8f0;
  border-radius:999px;
  padding:4px 10px;
}

.money{
  font-size:18px;
  font-weight:900;
  color:#0f172a;
  white-space:nowrap;
}

.desc{
  margin-top:14px;
  color:#334155;
  line-height:1.5;
}

.kv-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:12px;
  margin-top:16px;
}

.kv{
  border:1px solid #e2e8f0;
  border-radius:14px;
  padding:12px;
  background:#fff;
}

.kv .k{
  font-size:12px;
  color:#64748b;
  font-weight:800;
}

.kv .v{
  font-size:14px;
  color:#0f172a;
  font-weight:900;
  margin-top:2px;
}

.panel-actions{
  display:flex;
  gap:10px;
  margin-top:18px;
}

.btn-primary{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:16px;
  padding:10px 16px;
  background:#0f172a;
  color:#fff;
  font-weight:900;
  font-size:13px;
  text-decoration:none;
}

.btn-ghost{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:16px;
  padding:10px 16px;
  border:1px solid #e2e8f0;
  background:#fff;
  color:#0f172a;
  font-weight:900;
  font-size:13px;
  text-decoration:none;
}

/* ===============================
   TIMELINE
=================================*/
.section-head{
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  margin-bottom:14px;
}

.kicker2{
  font-size:12px;
  color:#64748b;
  font-weight:800;
}

.section-title{
  font-size:18px;
  font-weight:900;
  color:#0f172a;
}

.pill{
  display:inline-flex;
  align-items:center;
  border-radius:999px;
  padding:6px 12px;
  font-size:12px;
  font-weight:900;
}

.pill-slate{
  background:#f1f5f9;
  color:#334155;
}

.pill-wine{background:#7a1e2c;color:#fff;}
.pill-amber{background:#f59e0b;color:#111827;}

/* timeline */
.timeline{
  list-style:none;
  margin:0;
  padding:0;
  display:flex;
  flex-direction:column;
  gap:14px;
  position:relative;
}

.titem{
  position:relative;
  display:grid;
  grid-template-columns:24px 1fr;
  gap:14px;
}

.tline{
  position:absolute;
  left:12px;
  top:0;
  bottom:-14px;
  width:2px;
  background:#e2e8f0;
}

.dot{
  width:14px;
  height:14px;
  border-radius:999px;
  margin-top:18px;
  margin-left:4px;
  border:2px solid #fff;
  box-shadow:0 6px 16px rgba(15,23,42,.12);
  background:#94a3b8;
}

.dot-wine{background:#7a1e2c;}
.dot-amber{background:#f59e0b;}
.dot-blue{background:#3b82f6;}
.dot-indigo{background:#6366f1;}
.dot-green{background:#22c55e;}
.dot-gray{background:#94a3b8;}

.tcard{
  border:1px solid #e2e8f0;
  border-radius:18px;
  padding:14px;
  background:#fff;
}

.trow{
  display:flex;
  justify-content:space-between;
  gap:12px;
}

.ttitle{
  font-size:14px;
  font-weight:900;
  color:#0f172a;
}

.tbadge{
  font-size:11px;
  font-weight:900;
  color:#334155;
  background:#f1f5f9;
  border:1px solid #e2e8f0;
  padding:4px 8px;
  border-radius:999px;
}

.tmeta{
  margin-top:6px;
  font-size:12px;
  font-weight:800;
  color:#64748b;
  display:flex;
  gap:6px;
}

.tcode{
  font-weight:900;
  color:#0f172a;
}

.tdesc{
  margin-top:10px;
  color:#334155;
  line-height:1.5;
  font-size:13px;
}

.empty{
  border:1px dashed #cbd5e1;
  border-radius:16px;
  padding:14px;
  color:#64748b;
  background:#fff;
}
</style>

<script>
  const closeAllMenus = () => {
    document.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
  };

  document.addEventListener('click', (e) => {
    const btn  = e.target.closest('[data-menu-btn]');
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

  document.addEventListener('click', (e) => {
    const del = e.target.closest('[data-del]');
    if (!del) return;

    e.preventDefault();
    e.stopPropagation();

    const id = del.getAttribute('data-del');
    const name = del.getAttribute('data-name') || 'este proceso';

    if (!confirm(`¿Eliminar ${name}? Esta acción no se puede deshacer.`)) return;
    window.location.href = `<?= BASE_URL ?>/admin/procesos/eliminar?id=${id}`;
  });
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>