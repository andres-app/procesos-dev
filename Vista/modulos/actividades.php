<?php
$titulo = 'Detalle | Proceso';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function fmt_money($n)
{
    return 'S/ ' . number_format((float)$n, 2, '.', ',');
}
function fmt_date($d)
{
    if (!$d) return '-';
    $ts = strtotime($d);
    return $ts ? date('d/m/Y', $ts) : h($d);
}
function badgeFromObac($obac)
{
    $p = explode('-', (string)$obac);
    return strtoupper(trim($p[0] ?? (string)$obac));
}
function statusClass($estado)
{
    $e = strtoupper(trim((string)$estado));
    return match ($e) {
        'ADJUDICADO' => 'status-dorado',
        'CONVOCADO'  => 'status-vino',
        'PUBLICADO'  => 'status-gris',
        'OBSERVADO'  => 'status-vino',
        'DESIERTO'   => 'status-gris',
        default      => 'status-gris',
    };
}

$last = !empty($actividades) ? $actividades[count($actividades) - 1] : null;
?>

<main class="page page-shell flex-1 px-5 pt-4 pb-28">

    <div class="topbar">
        <a class="back" href="<?= BASE_URL ?>/procesos" aria-label="Volver">←</a>
        <div class="tt">
            <div class="kicker">Detalle del proceso</div>
            <div class="title"><?= h($proceso['proceso']) ?></div>
        </div>
        <span class="status <?= statusClass($proceso['estado']) ?>"><?= h($proceso['estado']) ?></span>
    </div>

    <section class="card">
        <div class="card-head">
            <div class="badge"><?= h(badgeFromObac($proceso['obac'])) ?></div>
            <div class="meta">
                <div class="h1"><?= h($proceso['proceso']) ?></div>

                <div class="obac-row">
                    <?php
                    $raw = (string)($proceso['obac'] ?? '');
                    $parts = preg_split('/[\/,\|;]+/', $raw);     // soporta "/", "," "|" ";"
                    $obacs = array_values(array_filter(array_map('trim', $parts)));
                    if (empty($obacs) && $raw !== '') $obacs = [$raw];
                    ?>
                    <?php foreach ($obacs as $ob): ?>
                        <span class="obac-chip"><?= h($ob) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="money"><?= fmt_money($proceso['estimado'] ?? 0) ?></div>
        </div>

        <div class="desc"><?= nl2br(h($proceso['descripcion'] ?? '')) ?></div>

        <div class="grid">
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
                <div class="v"><?= $last ? h($last['titulo']) : '-' ?></div>
            </div>
            <div class="kv">
                <div class="k">Fecha última</div>
                <div class="v"><?= $last ? fmt_date($last['fecha']) : '-' ?></div>
            </div>
        </div>
    </section>

    <section class="card mt">
        <div class="section-head">
            <div>
                <div class="kicker2">Seguimiento</div>
                <div class="section-title">Línea de tiempo</div>
            </div>
            <div class="pill"><?= count($actividades) ?> actividades</div>
        </div>

        <?php if (empty($actividades)): ?>
            <div class="empty">No hay actividades registradas para este proceso.</div>
        <?php else: ?>
            <ol class="timeline">
                <?php foreach ($actividades as $a): ?>
                    <li class="titem">
                        <div class="tline"></div>
                        <div class="dot dot-gray"></div>

                        <div class="tcard">

                            <?php
                            $cod = strtoupper((string)($a['tipo_codigo'] ?? ''));
                            $dot = match ($cod) {
                                'CONVOCATORIA' => 'dot-wine',
                                'CONSULTAS'    => 'dot-amber',
                                'ABSOLUCION'   => 'dot-blue',
                                'PROPUESTAS'   => 'dot-indigo',
                                'BUENA_PRO'    => 'dot-green',
                                'DESIERTO'     => 'dot-gray',
                                default        => 'dot-gray',
                            };
                            ?>

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
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
    /* =========================
   LAYOUT / CONTENEDOR
   ========================= */
.page-shell { width: 100%; }
@media (min-width:1024px){
  .page-shell{
    max-width:1120px;
    margin:0 auto;
    padding-left:24px;
    padding-right:24px;
  }
}

/* =========================
   TOPBAR (glass)
   ========================= */
.topbar{
  position: sticky;
  top: 0;
  z-index: 60;

  display:flex;
  align-items:center;
  gap:12px;

  padding:10px 12px;
  margin:0 0 14px 0;

  border-radius:18px;
  background: rgba(255,255,255,.10);
  border: 1px solid rgba(255,255,255,.14);
  backdrop-filter: blur(14px) saturate(140%);
  -webkit-backdrop-filter: blur(14px) saturate(140%);
  box-shadow: 0 16px 40px rgba(0,0,0,.28);
}

.back{
  width:42px;height:42px;
  border-radius:14px;
  border: 1px solid rgba(255,255,255,.18);
  background: rgba(255,255,255,.10);
  display:grid;
  place-items:center;
  text-decoration:none;
  font-weight:900;
  color: rgba(255,255,255,.92);
}

.tt{ min-width:0; flex:1; }
.kicker{
  font-size:.78rem;
  font-weight:900;
  color: rgba(255,255,255,.78);
  line-height:1;
}
.title{
  font-size:1.1rem;
  font-weight:900;
  color: rgba(255,255,255,.96);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
  text-shadow: 0 2px 10px rgba(0,0,0,.35);
}

/* status legible en topbar */
.status{
  font-weight:900;
  font-size:.7rem;
  padding:6px 12px;
  border-radius:999px;
  white-space:nowrap;
}
.status-dorado{ background:rgba(201,162,39,.15); color:#7A5B00; }
.status-vino{ background:rgba(107,28,38,.12); color:#6B1C26; }
.status-gris{ background:rgba(148,163,184,.15); color:#475569; }

.topbar .status{
  font-weight:950;
  font-size:.72rem;
  padding:7px 12px;
  letter-spacing:.02em;

  background: rgba(255,255,255,.12);
  color: rgba(255,255,255,.96);
  border: 1px solid rgba(255,255,255,.18);
  box-shadow: 0 10px 24px rgba(0,0,0,.22);
}
.topbar .status-dorado{
  background: rgba(201,162,39,.22);
  color:#fff;
  border-color: rgba(201,162,39,.35);
}
.topbar .status-vino{
  background: rgba(255,255,255,.12);
  color: rgba(255,255,255,.96);
  border-color: rgba(255,255,255,.18);
}
.topbar .status-gris{
  background: rgba(148,163,184,.18);
  color: rgba(255,255,255,.92);
  border-color: rgba(148,163,184,.28);
}

/* =========================
   CARD / RESUMEN
   ========================= */
.card{
  background:#fff;
  border-radius:20px;
  box-shadow: 0 10px 25px rgba(0,0,0,.12);
  padding:16px;
  border: 1px solid rgba(148,163,184,.18);
}
.mt{ margin-top:14px; }

.card-head{
  display:flex;
  align-items:flex-start;
  gap:12px;
}

.badge{
  width:48px;height:48px;
  border-radius:999px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:900;
  font-size:.85rem;
  background: rgba(107,28,38,.10);
  color:#6B1C26;
  flex:0 0 auto;
}

.meta{ min-width:0; flex:1; }
.h1{ font-weight:900; color:#0f172a; line-height:1.2; }
.h2{ margin-top:4px; font-size:.82rem; font-weight:800; color:#64748b; }
.money{ font-weight:900; color:#0f172a; white-space:nowrap; }

.desc{
  margin-top:12px;
  font-size:.9rem;
  font-weight:700;
  color:#334155;
  line-height:1.35;
}

.grid{
  margin-top:12px;
  display:grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap:10px;
}
@media (min-width:1024px){
  .grid{ grid-template-columns: repeat(4, minmax(0, 1fr)); }
}

.kv{
  background:#f8fafc;
  border:1px solid rgba(148,163,184,.25);
  border-radius:16px;
  padding:10px;
}
.k{ font-size:.72rem; font-weight:900; color:#64748b; }
.v{
  margin-top:3px;
  font-size:.86rem;
  font-weight:900;
  color:#0f172a;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

/* OBAC chips */
.obac-row{
  margin-top:8px;
  display:flex;
  flex-wrap:wrap;
  gap:8px;
}
.obac-chip{
  padding:6px 10px;
  border-radius:999px;
  background:#f1f5f9;
  border:1px solid rgba(148,163,184,.25);
  color:#0f172a;
  font-weight:900;
  font-size:.75rem;
  white-space:nowrap;
}

/* =========================
   SECCIÓN TIMELINE
   ========================= */
.section-head{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:10px;
  margin-bottom:12px;
}
.kicker2{ font-size:.78rem; font-weight:900; color:#64748b; line-height:1; }
.section-title{ font-size:1.05rem; font-weight:900; color:#0f172a; margin-top:2px; }
.pill{
  padding:8px 10px;
  border-radius:999px;
  background: rgba(148,163,184,.12);
  border: 1px solid rgba(148,163,184,.22);
  font-weight:900;
  font-size:.78rem;
  color:#334155;
  white-space:nowrap;
}

.timeline{ list-style:none; margin:0; padding:4px 0 0 0; }

.titem{
  position:relative;
  display:grid;
  grid-template-columns:24px 1fr;
  column-gap:12px;
  padding:10px 0;
}

.tline{
  position:absolute;
  left:11px; top:0; bottom:0;
  width:2px;
  background: rgba(148,163,184,.35);
}

.dot{
  width:14px;height:14px;
  border-radius:999px;
  margin-left:4px;
  margin-top:10px;
  box-shadow: 0 0 0 4px rgba(148,163,184,.15);
  z-index:2;
}

.tcard{
  background:#fff;
  border:1px solid rgba(148,163,184,.22);
  border-radius:18px;
  padding:12px;
  box-shadow: 0 8px 18px rgba(0,0,0,.08);
  overflow:hidden; /* evita que algo “se salga” */
}

/* fila título + badge (FIX overflow) */
.trow{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:10px;
}
.ttitle{
  min-width:0;
  flex: 1 1 auto;
  font-weight:900;
  color:#0f172a;
  line-height:1.2;

  overflow:hidden;
  text-overflow:ellipsis;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
}
.tbadge{
  flex:0 0 auto;
  max-width:44%;
  padding:6px 10px;
  border-radius:999px;
  font-weight:950;
  font-size:.72rem;
  border:1px solid rgba(148,163,184,.22);
  background: rgba(148,163,184,.10);
  color:#334155;

  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;
}
@media (max-width:420px){
  .trow{ flex-direction:column; align-items:flex-start; }
  .tbadge{ max-width:100%; }
}

.tmeta{
  margin-top:6px;
  font-size:.78rem;
  font-weight:900;
  color:#64748b;
}
.sep{ opacity:.6; padding:0 6px; }
.tcode{ font-weight:950; font-size:.72rem; color:#64748b; }

.tdesc{
  margin-top:8px;
  font-size:.86rem;
  font-weight:700;
  color:#334155;
  line-height:1.35;
}

.empty{
  padding:14px;
  border-radius:16px;
  background:#f8fafc;
  border:1px dashed rgba(148,163,184,.45);
  font-weight:800;
  color:#64748b;
}

/* dots por tipo */
.dot-wine{ background:#6B1C26; box-shadow:0 0 0 4px rgba(107,28,38,.18); }
.dot-amber{ background:#C9A227; box-shadow:0 0 0 4px rgba(201,162,39,.22); }
.dot-blue{ background:#2563eb; box-shadow:0 0 0 4px rgba(37,99,235,.18); }
.dot-indigo{ background:#4f46e5; box-shadow:0 0 0 4px rgba(79,70,229,.18); }
.dot-green{ background:#16a34a; box-shadow:0 0 0 4px rgba(22,163,74,.18); }
.dot-gray{ background:#64748b; box-shadow:0 0 0 4px rgba(148,163,184,.20); }
</style>