<?php
// Vista/modulos/admin/procesos_detalle.php
$titulo = 'Detalle | Proceso';
$active = 'procesos';

require_once __DIR__ . '/../../../Config/config.php';

/* =========================
   Helpers
   ========================= */
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
  $ts = strtotime((string)$d);
  return $ts ? date('d/m/Y', $ts) : h($d);
}

function v($x)
{
  return trim((string)$x) !== '' ? h($x) : '-';
}

function badgeFromCodigo($codigo)
{
  $p = explode('-', (string)$codigo);
  return strtoupper(trim($p[0] ?? (string)$codigo));
}

function statusPillClass($estado)
{
  $e = strtoupper(trim((string)$estado));
  return match ($e) {
    'ADJUDICADO' => 'pill-amber',
    'CONVOCADO'  => 'pill-wine',
    'OBSERVADO'  => 'pill-rose',
    'PUBLICADO'  => 'pill-slate',
    'DESIERTO'   => 'pill-dark',
    default      => 'pill-slate',
  };
}

function parseObacs($raw)
{
  $raw   = (string)$raw;
  $parts = preg_split('/[\/,\|;]+/', $raw);
  $obacs = array_values(array_filter(array_map('trim', $parts)));

  if (empty($obacs) && $raw !== '') {
    $obacs = [$raw];
  }

  return $obacs;
}

function dotClassFromTipo($tipoCodigo)
{
  $cod = strtoupper((string)($tipoCodigo ?? ''));
  return match ($cod) {
    'CONVOCATORIA' => 'dot-wine',
    'CONSULTAS'    => 'dot-amber',
    'ABSOLUCION'   => 'dot-blue',
    'PROPUESTAS'   => 'dot-indigo',
    'BUENA_PRO'    => 'dot-green',
    'DESIERTO'     => 'dot-slate',
    default        => 'dot-slate',
  };
}

function tipoProcesoPillClass($tipo)
{
  $t = strtoupper(trim((string)$tipo));
  return match ($t) {
    'CORPORATIVO' => 'tp-corp',
    default       => 'tp-ind',
  };
}

function tipoProcesoLabel($tipo)
{
  $t = strtoupper(trim((string)$tipo));
  return $t === 'CORPORATIVO' ? 'Corporativo' : 'Individual';
}

function pacEstadoPillClass($estado)
{
  $e = strtoupper(trim((string)$estado));
  return match ($e) {
    'ADJUDICADO' => 'pe-adj',
    'INCLUIDO'   => 'pe-inc',
    'OBSERVADO'  => 'pe-obs',
    default      => 'pe-inc',
  };
}

/* =========================
   Data
   ========================= */
$proceso             = $proceso ?? null;
$actividades         = $actividades ?? [];
$timelineActividades = $timelineActividades ?? $actividades;
$pacs_vinculados     = $pacs_vinculados ?? [];

$idProceso = (int)(is_array($proceso) ? ($proceso['id'] ?? 0) : 0);
if ($idProceso <= 0) {
  $idProceso = (int)($_GET['id'] ?? 0);
}

require __DIR__ . '/../../layout/admin_layout.php';

if ($idProceso <= 0) {
  echo "<div class='rounded-2xl border border-slate-200 bg-white p-4'>ID inválido.</div>";
  require __DIR__ . '/../../layout/admin_footer.php';
  exit;
}

if (!$proceso || !is_array($proceso)) {
  echo "<div class='rounded-2xl border border-slate-200 bg-white p-4'>Proceso no encontrado.</div>";
  require __DIR__ . '/../../layout/admin_footer.php';
  exit;
}


$obacs         = parseObacs($proceso['obac'] ?? '');
$estadoUp      = strtoupper(trim((string)($proceso['estado_nombre'] ?? $proceso['estado'] ?? '')));
$tipoProceso   = strtoupper(trim((string)($proceso['tipo_proceso'] ?? 'INDIVIDUAL')));
$tipoLabel     = tipoProcesoLabel($tipoProceso);
$tipoPillClass = tipoProcesoPillClass($tipoProceso);
$totalPacs     = count($pacs_vinculados);
$esAdjudicado  = $estadoUp === 'ADJUDICADO';
?>

<div class="detail-shell space-y-4">

  <!-- TOP BAR REDISEÑADO -->
  <div class="detail-topbar premium-topbar">

    <div class="topbar-main">
      <div class="topbar-left">
        <a href="<?= BASE_URL ?>/admin/procesos" class="btn-icon btn-back" aria-label="Volver">←</a>

        <div class="process-head">
          <div class="process-kicker-row">
            <span class="process-kicker">Detalle del proceso</span>
            <?php if (!empty($proceso['codigo_proceso'])): ?>
              <span class="process-mini-code"><?= h($proceso['codigo_proceso']) ?></span>
            <?php endif; ?>
          </div>

          <h1 class="process-title">
            <?= h($proceso['descripcion'] ?? '-') ?>
          </h1>

          <div class="process-meta">
            <span class="type-pill <?= h($tipoPillClass) ?>">
              <?= h($tipoLabel) ?>
            </span>

            <?php if (!empty($proceso['periodo'])): ?>
              <span class="meta-chip">Periodo <?= h($proceso['periodo']) ?></span>
            <?php endif; ?>

            <?php if (!empty($proceso['anio_convocatoria'])): ?>
              <span class="meta-chip">Convocatoria <?= h($proceso['anio_convocatoria']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="topbar-right">
        <div class="status-block">
          <div class="status-label">Estado actual</div>
          <span class="pill <?= h(statusPillClass($estadoUp)) ?> status-pill-lg">
            <?= h($estadoUp ?: '-') ?>
          </span>
        </div>
      </div>
    </div>

    <div class="topbar-actions">
      <div class="topbar-actions-left">
        <button type="button" class="btn-soft btn-action" onclick="printTimeline()">
          🖨️ <span>Timeline</span>
        </button>

        <button type="button" class="btn-soft btn-action" onclick="printProject()">
          🖨️ <span>Project</span>
        </button>
      </div>

      <div class="topbar-actions-right">
        <button type="button" class="btn-primary btn-action-main" onclick="toggleActivityForm()">
          + Actividad
        </button>

        <div class="actions">
          <button type="button" class="btn-icon" data-menu-btn aria-label="Más acciones">⋯</button>

          <div class="menu hidden" data-menu>
            <a class="menu-item" href="<?= BASE_URL ?>/admin/procesos_editar?id=<?= $idProceso ?>">
              ✏️ Editar
            </a>

            <button
              class="menu-item danger"
              type="button"
              data-del="<?= $idProceso ?>"
              data-name="<?= h($proceso['descripcion'] ?? 'este proceso') ?>">
              🗑️ Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="detail-grid">

  <!-- ===== IZQUIERDA ===== -->
  <aside class="panel sticky-panel summary-panel">

    <div class="summary-head">
      <div class="summary-badge"><?= h(badgeFromCodigo($proceso['codigo_proceso'] ?? '')) ?></div>

      <div class="summary-main">
        <div class="summary-kicker">Nomenclatura</div>

        <?php if (!empty($proceso['codigo_proceso'])): ?>
          <div class="summary-title"><?= h($proceso['codigo_proceso']) ?></div>
        <?php endif; ?>

        <?php if (!empty($proceso['obac_nombre'])): ?>
          <div class="obac-name"><?= h($proceso['obac_nombre']) ?></div>
        <?php endif; ?>

        <div class="chips">
          <?php foreach ($obacs as $ob): ?>
            <span class="chip"><?= h($ob) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="money-card">
      <div class="money-label">Valor estimado</div>
      <div class="money-value-wrap">
        <span class="money-symbol">S/</span>
        <span class="money-value"><?= number_format((float)($proceso['estimado'] ?? 0), 2, '.', ',') ?></span>
      </div>
      <?php if (!empty($proceso['moneda'])): ?>
        <div class="money-sub">Moneda: <?= h($proceso['moneda']) ?></div>
      <?php endif; ?>
    </div>

    <?php $objetoContratacion = trim((string)($proceso['objeto_contratacion'] ?? '')); ?>

    <?php if ($objetoContratacion !== ''): ?>
      <div class="desc-card">
        <div class="desc-kicker">Objeto de contratación</div>
        <div class="desc"><?= nl2br(h($objetoContratacion)) ?></div>
      </div>
    <?php endif; ?>

    <div class="kv-grid">
      <div class="kv">
        <div class="k">Periodo</div>
        <div class="v"><?= v($proceso['periodo'] ?? '') ?></div>
      </div>

      <div class="kv">
        <div class="k">Expediente</div>
        <div class="v"><?= v($proceso['expediente'] ?? '') ?></div>
      </div>

      <div class="kv">
        <div class="k">F. convocatoria</div>
        <div class="v"><?= fmt_date($proceso['convocatoria'] ?? null) ?></div>
      </div>

      <div class="kv kv-pacs">
        <div class="k">PACs vinculados</div>
        <div class="v">
          <?php if (empty($pacs_vinculados)): ?>
            <div class="chips chips-empty">-</div>
          <?php else: ?>
            <div class="chips">
              <?php foreach ($pacs_vinculados as $p): ?>
                <?php
                $obac = trim((string)($p['obac_nombre'] ?? ''));
                $nopac = trim((string)($p['nopac'] ?? ''));
                $codigo = $obac && $nopac ? $obac . '-' . $nopac : $nopac;
                ?>
                <span class="chip"><?= h($codigo) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if ($esAdjudicado && (!empty($proceso['ganador']) || !empty($proceso['fecha_adjudicacion']))): ?>
      <div class="adj-block">
        <div class="adj-label">Adjudicación</div>

        <div class="adj-grid">
          <?php if (!empty($proceso['ganador'])): ?>
            <div class="adj-kv full">
              <div class="adj-k">Ganador</div>
              <div class="adj-v"><?= h($proceso['ganador']) ?></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($proceso['fecha_adjudicacion'])): ?>
            <div class="adj-kv">
              <div class="adj-k">F. adjudicación</div>
              <div class="adj-v"><?= fmt_date($proceso['fecha_adjudicacion']) ?></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($proceso['fecha_consentido'])): ?>
            <div class="adj-kv">
              <div class="adj-k">F. consentido</div>
              <div class="adj-v"><?= fmt_date($proceso['fecha_consentido']) ?></div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </aside>

  <!-- ===== DERECHA ===== -->
  <section class="panel right-panel">

    <div class="tab-bar">
      <button class="tab-btn active" data-tab="timeline">
        Línea de tiempo
        <span class="tab-count"><?= count($timelineActividades) ?></span>
      </button>

      <button class="tab-btn" data-tab="project">
        Project
        <span class="tab-count">Detallado</span>
      </button>

      <button class="tab-btn" data-tab="pacs">
        PACs vinculados
        <span class="tab-count"><?= $totalPacs ?></span>
      </button>
    </div>

    <div id="activityFormWrap" class="activity-form-wrap hidden">
      <form action="<?= BASE_URL ?>/admin/proceso_actividad_guardar" method="POST" class="activity-form">
        <input type="hidden" name="proceso_id" value="<?= (int)$idProceso ?>">

        <div class="form-grid">
          <div class="field field-full">
            <label>Actividad</label>
            <select name="tipo_id" required>
              <option value="">Seleccione actividad</option>
              <?php foreach (($tiposActividad ?? []) as $t): ?>
                <option value="<?= (int)$t['id'] ?>">
                  <?= h($t['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label>Fecha</label>
            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="field field-full">
            <label>Comentario</label>
            <textarea name="comentario" rows="4" placeholder="Detalle o seguimiento"></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-ghost" onclick="toggleActivityForm()">Cancelar</button>
          <button type="submit" class="btn-primary">Guardar actividad</button>
        </div>
      </form>
    </div>

    <!-- TAB: Timeline -->
    <div class="tab-content" id="tab-timeline">
      <div class="section-head">
        <div>
          <div class="kicker2">Seguimiento</div>
          <div class="section-title">Línea de tiempo</div>
          <div class="section-subtitle">Historial cronológico de actuaciones del proceso</div>
        </div>
        <div class="pill pill-slate"><?= count($timelineActividades) ?> actividades</div>
      </div>

      <?php if (empty($timelineActividades)): ?>
        <div class="empty">No hay actividades registradas para este proceso.</div>
      <?php else: ?>
        <ol class="timeline">
          <?php foreach ($timelineActividades as $i => $a): ?>
            <?php
            $dot = dotClassFromTipo($a['tipo_codigo'] ?? '');
            $isLastItem = $i === count($timelineActividades) - 1;
            ?>
            <li class="titem <?= $isLastItem ? 'is-current' : '' ?>">
              <div class="tline"></div>
              <div class="dot <?= h($dot) ?>"></div>

              <article class="tcard">
                <div class="trow">
                  <div class="tcontent">
                    <div class="ttitle"><?= h($a['titulo'] ?? '-') ?></div>
                    <div class="tmeta">
                      <span><?= fmt_date($a['fecha'] ?? null) ?></span>
                      <?php if ($isLastItem): ?>
                        <span class="tcurrent-badge">Estado actual</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <?php if (!empty($a['tipo_nombre'])): ?>
                    <span class="tbadge"><?= h($a['tipo_nombre']) ?></span>
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
    </div>

    <!-- TAB: Project -->
    <div class="tab-content hidden" id="tab-project">
      <?php
      $projectRows = [];

      foreach ($actividades as $a) {
        $label = trim((string)($a['titulo'] ?? $a['tipo_nombre'] ?? 'Actividad'));

        if (!isset($projectRows[$label])) {
          $projectRows[$label] = [];
        }

        $projectRows[$label][] = $a;
      }

      foreach ($projectRows as &$items) {
        usort($items, function ($x, $y) {
          return strtotime($x['fecha']) <=> strtotime($y['fecha']);
        });
      }
      unset($items);
      ?>

      <div class="section-head">
        <div>
          <div class="kicker2">Histórico visual</div>
          <div class="section-title">Project</div>
          <div class="section-subtitle">Evolución de cada actividad en el tiempo</div>
        </div>
        <div class="pill pill-slate"><?= count($actividades) ?> registros</div>
      </div>

      <?php if (empty($projectRows)): ?>
        <div class="empty">No hay actividades registradas.</div>
      <?php else: ?>
        <div class="roadmap">
          <?php foreach ($projectRows as $label => $items): ?>
            <?php
            $total = count($items);
            $lastItem = end($items);
            ?>
            <div class="roadmap-card">
              <div class="roadmap-head">
                <div>
                  <div class="roadmap-title"><?= h($label) ?></div>
                  <div class="roadmap-meta">
                    <?= $total ?> registro<?= $total !== 1 ? 's' : '' ?>
                    <?php if ($total > 1): ?>
                      <span class="reprog-badge">
                        Reprogramado <?= $total - 1 ?> vez<?= ($total - 1) !== 1 ? 'es' : '' ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="roadmap-last">
                  <?= fmt_date($lastItem['fecha'] ?? null) ?>
                </div>
              </div>

              <div class="roadmap-track">
                <?php foreach ($items as $i => $it): ?>
                  <div class="roadmap-node <?= $i === ($total - 1) ? 'is-last' : '' ?>">
                    <div class="roadmap-dot"></div>
                    <div class="roadmap-date"><?= date('d/m', strtotime($it['fecha'])) ?></div>
                  </div>

                  <?php if ($i < $total - 1): ?>
                    <div class="roadmap-line"></div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>

              <?php if (!empty($lastItem['comentario'])): ?>
                <div class="roadmap-desc">
                  <?= nl2br(h($lastItem['comentario'])) ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- TAB: PACs -->
    <div class="tab-content hidden" id="tab-pacs">
      <div class="section-head">
        <div>
          <div class="kicker2">Origen</div>
          <div class="section-title">PACs vinculados</div>
          <div class="section-subtitle">Items del Plan Anual de Contrataciones que originan este proceso</div>
        </div>
        <span class="type-pill <?= h($tipoPillClass) ?>"><?= h($tipoLabel) ?> · <?= $totalPacs ?> PAC</span>
      </div>

      <?php if (empty($pacs_vinculados)): ?>
        <div class="empty">No hay PACs vinculados a este proceso.</div>
      <?php else: ?>
        <div class="pac-table">

          <div class="pac-table-head">
            <div class="col-obac">OBAC</div>
            <div class="col-num">N° PAC</div>
            <div class="col-pn">P/NP</div>
            <div class="col-desc">Descripción</div>
            <div class="col-estado">Estado</div>
            <div class="col-monto">Estimado</div>
          </div>

          <?php foreach ($pacs_vinculados as $pac): ?>
            <div class="pac-row">
              <div class="col-obac">
                <?php if (!empty($pac['obac_nombre'])): ?>
                  <span class="tag-obac"><?= h($pac['obac_nombre']) ?></span>
                <?php else: ?>
                  <span class="tag-empty">—</span>
                <?php endif; ?>
              </div>

              <div class="col-num">
                <span class="num-badge"><?= h($pac['nopac'] ?? '-') ?></span>
              </div>

              <div class="col-pn">
                <span class="pn-badge"><?= h($pac['pn'] ?? '-') ?></span>
              </div>

              <div class="col-desc">
                <span class="pac-desc-text"><?= h($pac['descripcion'] ?? '-') ?></span>
              </div>

              <div class="col-estado">
                <span class="pac-estado <?= pacEstadoPillClass($pac['estado_nombre'] ?? '') ?>">
                  <?= h($pac['estado_nombre'] ?? '-') ?>
                </span>
              </div>

              <div class="col-monto">
                <span class="pac-money"><?= fmt_money($pac['estimado'] ?? 0) ?></span>
              </div>

            </div>
          <?php endforeach; ?>

        </div>
      <?php endif; ?>
    </div>

  </section>
</div>

<style>
  :root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-900: #0f172a;

    --white: #ffffff;
    --blue-50: #e0f2fe;
    --blue-700: #0369a1;
    --indigo-50: #ede9fe;
    --indigo-700: #6d28d9;
    --green-50: #dcfce7;
    --green-700: #15803d;
    --green-900: #14532d;
    --green-100: #f0fdf4;
    --green-200: #bbf7d0;
    --green-300: #86efac;
    --rose-50: #ffe4e6;
    --rose-700: #be123c;
    --amber-50: #fef3c7;
    --amber-700: #92400e;
    --orange-50: #fff7ed;
    --orange-700: #c2410c;
    --orange-200: #fed7aa;
    --wine: #7a1e2c;
    --shadow-sm: 0 8px 20px rgba(15, 23, 42, .06);
    --shadow-md: 0 12px 30px rgba(15, 23, 42, .08);
    --shadow-lg: 0 20px 45px rgba(15, 23, 42, .12);
    --radius-xl: 18px;
    --radius-lg: 16px;
    --radius-md: 14px;
  }

  * {
    box-sizing: border-box;
  }

  .detail-shell {
    width: 100%;
    max-width: 1600px;
    margin: 0;
  }

  .detail-grid {
    display: grid;
    grid-template-columns: 390px 1fr;
    gap: 18px;
  }

  .panel {
    border: 1px solid var(--slate-200);
    background: var(--white);
    border-radius: var(--radius-xl);
    padding: 18px;
  }

  .right-panel,
  .summary-panel {
    display: flex;
    flex-direction: column;
  }

  .summary-panel {
    gap: 16px;
  }

  .sticky-panel {
    position: sticky;
    top: 92px;
    align-self: start;
  }

  /* =========================
     HEADER PREMIUM
     ========================= */
  .detail-topbar {
    display: flex;
    flex-direction: column;
    gap: 18px;
    padding: 20px 22px;
    border: 1px solid #dbe3ee;
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
    border-radius: 22px;
    position: sticky;
    top: 12px;
    z-index: 20;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .04);
  }

  .topbar-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
  }

  .topbar-left {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    min-width: 0;
    flex: 1;
  }

  .topbar-right {
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    flex-shrink: 0;
  }

  .btn-back {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    margin-top: 2px;
    flex-shrink: 0;
  }

  .process-head {
    min-width: 0;
    flex: 1;
  }

  .process-kicker-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 8px;
  }

  .process-kicker,
  .kicker2 {
    font-size: 12px;
    color: var(--slate-500);
    font-weight: 600;
    letter-spacing: .01em;
  }

  .process-mini-code {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    font-size: 11px;
    font-weight: 700;
    color: var(--slate-600);
  }

  .process-title {
    margin: 0;
    font-size: 25px;
    line-height: 1.08;
    letter-spacing: -0.04em;
    font-weight: 850;
    color: var(--slate-900);
    max-width: 920px;
    text-wrap: balance;
  }

  .process-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 14px;
  }

  .meta-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 11px;
    border-radius: 999px;
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    font-size: 12px;
    font-weight: 700;
    color: var(--slate-600);
  }

  .status-block {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    min-width: 130px;
  }

  .status-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--slate-400);
  }

  .status-pill-lg {
    padding: 9px 14px;
    font-size: 12px;
    letter-spacing: .01em;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .18);
  }

  .topbar-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding-top: 14px;
    border-top: 1px solid #edf2f7;
    flex-wrap: wrap;
  }

  .topbar-actions-left,
  .topbar-actions-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn-action {
    min-height: 40px;
    padding: 10px 14px;
  }

  .btn-action-main {
    min-height: 42px;
    padding: 10px 16px;
  }

  /* =========================
     BOTONES Y PILLS
     ========================= */
  .type-pill,
  .pill,
  .chip,
  .tab-count,
  .tbadge,
  .tcurrent-badge,
  .pac-estado,
  .meta-chip,
  .process-mini-code {
    white-space: nowrap;
  }

  .type-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .01em;
  }

  .tp-ind {
    background: var(--blue-50);
    color: var(--blue-700);
  }

  .tp-corp {
    background: var(--indigo-50);
    color: var(--indigo-700);
  }

  .btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 14px;
    border: 1px solid var(--slate-200);
    background: var(--white);
    cursor: pointer;
    text-decoration: none;
    color: var(--slate-900);
    transition: .18s ease;
  }

  .btn-icon:hover {
    background: var(--slate-50);
    border-color: var(--slate-300);
  }

  .btn-soft,
  .btn-primary,
  .btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: 14px;
    padding: 9px 14px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: .18s ease;
  }

  .btn-soft,
  .btn-ghost {
    border: 1px solid var(--slate-200);
    background: var(--white);
    color: var(--slate-900);
  }

  .btn-soft:hover,
  .btn-ghost:hover {
    background: var(--slate-50);
    border-color: var(--slate-300);
  }

  .btn-primary {
    border: 1px solid var(--slate-900);
    background: var(--slate-900);
    color: var(--white);
    box-shadow: 0 8px 20px rgba(15, 23, 42, .10);
  }

  .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, .14);
  }

  .pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
  }

  .pill-slate {
    background: var(--slate-100);
    color: #334155;
  }

  .pill-wine {
    background: var(--wine);
    color: var(--white);
  }

  .pill-amber {
    background: var(--amber-50);
    color: var(--amber-700);
  }

  .pill-rose {
    background: var(--rose-50);
    color: var(--rose-700);
  }

  .pill-dark {
    background: var(--slate-200);
    color: var(--slate-900);
  }

  /* =========================
     MENU ACCIONES
     ========================= */
  .actions {
    position: relative;
  }

  .menu {
    position: absolute;
    right: 0;
    top: 46px;
    width: 210px;
    background: var(--white);
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 6px;
  }

  .menu.hidden {
    display: none;
  }

  .menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    color: var(--slate-900);
    text-decoration: none;
    border: none;
    background: transparent;
    cursor: pointer;
  }

  .menu-item:hover {
    background: var(--slate-50);
  }

  .menu-item.danger {
    color: #e11d48;
  }

  /* =========================
     COLUMNA IZQUIERDA
     ========================= */
  .summary-head {
    display: flex;
    gap: 14px;
    align-items: flex-start;
  }

  .summary-badge {
    min-width: 58px;
    height: 58px;
    border-radius: 18px;
    background: var(--slate-900);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 800;
  }

  .summary-main {
    flex: 1;
    min-width: 0;
  }

  .summary-kicker {
    font-size: 11px;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .summary-title {
    margin-top: 4px;
    font-size: 19px;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.35;
    letter-spacing: -0.02em;
  }

  .obac-name {
    margin-top: 4px;
    font-size: 13px;
    color: var(--slate-500);
  }

  .chips {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    /* centra horizontal */
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    min-height: 52px;
    /* da aire vertical */
  }

  .chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    text-align: center;
  }

  .money-card {
    padding: 16px;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    background: var(--slate-50);
  }

  .money-label {
    font-size: 12px;
    color: var(--slate-500);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .money-value-wrap {
    margin-top: 6px;
    display: flex;
    align-items: baseline;
    gap: 6px;
  }

  .money-symbol {
    font-size: 13px;
    font-weight: 700;
    color: var(--slate-500);
  }

  .money-value {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: var(--slate-900);
    letter-spacing: -0.03em;
  }

  .money-sub {
    margin-top: 6px;
    font-size: 12px;
    color: var(--slate-500);
  }

  .desc-card {
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    background: var(--white);
    padding: 16px;
  }

  .desc-kicker {
    font-size: 12px;
    color: var(--slate-500);
    font-weight: 700;
    margin-bottom: 8px;
  }

  .desc {
    color: #334155;
    line-height: 1.6;
    font-size: 14px;
  }

  .kv-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .kv {
    border: 1px solid var(--slate-200);
    border-radius: 14px;
    padding: 12px;
    background: var(--white);
  }

  .kv .k {
    font-size: 12px;
    color: var(--slate-500);
    font-weight: 600;
  }

  .kv .v {
    font-size: 14px;
    color: #0f172a;
    font-weight: 700;
    margin-top: 6px;
    line-height: 1.4;
  }

  .kv-pacs .v {
    margin-top: 8px;
  }

  .chips-empty {
    justify-content: center;
    color: #94a3b8;
    min-height: 40px;
  }

  .adj-block {
    border: 1px solid var(--green-300);
    border-radius: var(--radius-lg);
    padding: 14px;
    background: var(--green-100);
  }

  .adj-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--green-700);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 10px;
  }

  .adj-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }

  .adj-kv {
    background: var(--white);
    border: 1px solid var(--green-200);
    border-radius: 12px;
    padding: 10px;
  }

  .adj-kv.full {
    grid-column: 1 / -1;
  }

  .adj-k {
    font-size: 12px;
    color: var(--green-700);
    font-weight: 600;
  }

  .adj-v {
    font-size: 14px;
    color: var(--green-900);
    font-weight: 700;
    margin-top: 3px;
  }

  /* =========================
     TABS
     ========================= */
  .tab-bar {
    display: flex;
    gap: 4px;
    border-bottom: 1px solid var(--slate-200);
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .tab-btn {
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--slate-500);
    border: none;
    border-bottom: 2px solid transparent;
    background: transparent;
    cursor: pointer;
    border-radius: 10px 10px 0 0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .tab-btn.active {
    color: var(--slate-900);
    border-bottom-color: var(--slate-900);
  }

  .tab-count {
    background: var(--slate-100);
    border-radius: 999px;
    padding: 2px 7px;
    font-size: 11px;
    color: #334155;
  }

  .tab-content {
    display: block;
  }

  .tab-content.hidden {
    display: none;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .section-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.15;
    letter-spacing: -0.02em;
    margin-top: 2px;
  }

  .section-subtitle {
    margin-top: 6px;
    font-size: 13px;
    color: var(--slate-500);
  }

  .empty {
    border: 1px dashed var(--slate-300);
    border-radius: var(--radius-lg);
    padding: 14px;
    color: var(--slate-500);
    background: var(--white);
  }

  /* =========================
     FORMULARIO
     ========================= */
  .activity-form-wrap {
    margin-bottom: 16px;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    padding: 14px;
    background: var(--slate-50);
  }

  .activity-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .field-full {
    grid-column: 1 / -1;
  }

  .field label {
    font-size: 12px;
    font-weight: 600;
    color: var(--slate-500);
  }

  .field input,
  .field select,
  .field textarea {
    width: 100%;
    border: 1px solid #dbe2ea;
    border-radius: 14px;
    background: var(--white);
    color: var(--slate-900);
    padding: 11px 13px;
    font-size: 14px;
    outline: none;
    transition: .18s ease;
  }

  .field input:focus,
  .field select:focus,
  .field textarea:focus {
    border-color: var(--slate-400);
    box-shadow: 0 0 0 4px rgba(148, 163, 184, .15);
  }

  .field textarea {
    min-height: 110px;
    resize: vertical;
  }

  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
  }

  /* =========================
     TIMELINE
     ========================= */
  .timeline {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .titem {
    position: relative;
    display: grid;
    grid-template-columns: 28px 1fr;
    column-gap: 14px;
    align-items: stretch;
  }

  .tline {
    position: absolute;
    left: 13px;
    top: 0;
    bottom: -16px;
    width: 2px;
    background: var(--slate-200);
  }

  .titem:last-child .tline {
    bottom: 32px;
  }

  .dot {
    width: 14px;
    height: 14px;
    border-radius: 999px;
    align-self: center;
    justify-self: center;
    z-index: 2;
    border: 3px solid var(--white);
  }

  .dot-wine {
    background: var(--wine);
  }

  .dot-amber {
    background: #f59e0b;
  }

  .dot-blue {
    background: #3b82f6;
  }

  .dot-indigo {
    background: #6366f1;
  }

  .dot-green {
    background: #22c55e;
  }

  .dot-slate {
    background: var(--slate-400);
  }

  .tcard {
    border: 1px solid #dbe3ee;
    border-radius: var(--radius-xl);
    padding: 16px;
    background: var(--white);
    transition: .18s ease;
  }

  .tcard:hover {
    border-color: var(--slate-300);
    box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    transform: translateY(-1px);
  }

  .titem.is-current .tcard {
    border-color: var(--slate-300);
    box-shadow: var(--shadow-md);
  }

  .trow {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
  }

  .tcontent {
    min-width: 0;
    flex: 1;
  }

  .ttitle {
    font-size: 16px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1.35;
  }

  .tbadge {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    background: var(--slate-100);
    border: 1px solid var(--slate-200);
    padding: 5px 10px;
    border-radius: 999px;
  }

  .tmeta {
    margin-top: 7px;
    font-size: 12px;
    color: var(--slate-500);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
  }

  .tcurrent-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 9px;
    background: var(--slate-200);
    color: #334155;
    font-size: 11px;
    font-weight: 700;
  }

  .tdesc {
    margin-top: 11px;
    color: var(--slate-600);
    line-height: 1.6;
    font-size: 13px;
  }

  /* =========================
     PROJECT
     ========================= */
  .roadmap {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .roadmap-card {
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-xl);
    padding: 16px;
    background: var(--white);
    transition: .2s ease;
  }

  .roadmap-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--slate-300);
  }

  .roadmap-card:last-child {
    border-color: var(--slate-300);
    box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
  }

  .roadmap-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 12px;
  }

  .roadmap-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--slate-900);
  }

  .roadmap-meta {
    font-size: 12px;
    color: var(--slate-500);
    margin-top: 4px;
    font-weight: 600;
  }

  .roadmap-last {
    font-size: 13px;
    font-weight: 800;
    color: var(--slate-900);
    background: var(--slate-100);
    padding: 6px 10px;
    border-radius: 999px;
  }

  .roadmap-track {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 10px 0 12px;
    flex-wrap: wrap;
  }

  .roadmap-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    position: relative;
  }

  .roadmap-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: var(--slate-300);
  }

  .roadmap-node.is-last .roadmap-dot {
    width: 14px;
    height: 14px;
    background: var(--slate-900);
    box-shadow: 0 0 0 4px rgba(15, 23, 42, .08);
  }

  .roadmap-date {
    font-size: 11px;
    font-weight: 700;
    color: var(--slate-500);
  }

  .roadmap-line {
    height: 2px;
    width: 34px;
    background: linear-gradient(to right, var(--slate-200), var(--slate-400));
  }

  .reprog-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    background: var(--orange-50);
    color: var(--orange-700);
    border: 1px solid var(--orange-200);
    margin-left: 6px;
  }

  .roadmap-desc {
    font-size: 13px;
    color: var(--slate-600);
    line-height: 1.5;
    margin-top: 6px;
  }

  /* =========================
     PACS
     ========================= */
  .pac-table {
    display: flex;
    flex-direction: column;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
  }

  .pac-table-head {
    display: grid;
    grid-template-columns: 80px 70px 70px 1fr 110px 120px;
    padding: 10px 16px;
    background: var(--slate-50);
    border-bottom: 1px solid var(--slate-200);
    gap: 12px;
    align-items: center;
  }

  .pac-table-head>div {
    font-size: 11px;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .pac-row {
    display: grid;
    grid-template-columns: 70px 64px 70px 1fr 110px 120px;
    padding: 14px 16px;
    gap: 12px;
    align-items: center;
    border-bottom: 1px solid var(--slate-100);
    transition: .15s ease;
  }

  .pac-row:last-child {
    border-bottom: none;
  }

  .pac-row:hover {
    background: #fafbfc;
  }

  .col-num,
  .col-pn,
  .col-obac,
  .col-estado {
    display: flex;
    align-items: center;
  }

  .col-desc {
    min-width: 0;
  }

  .col-monto {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }

  .num-badge {
    min-width: 32px;
    height: 32px;
    border-radius: 10px;
    background: var(--slate-900);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
  }

  .pn-badge {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    background: var(--blue-50);
    color: var(--blue-700);
    border: 1px solid #bae6fd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
  }

  .pac-desc-text {
    font-size: 13px;
    font-weight: 600;
    color: var(--slate-900);
    line-height: 1.45;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .tag-obac {
    font-size: 11px;
    font-weight: 700;
    color: #1e40af;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 4px 8px;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100px;
  }

  .tag-empty {
    font-size: 13px;
    color: var(--slate-400);
  }

  .pac-estado {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 11px;
    font-size: 11px;
    font-weight: 700;
  }

  .pe-inc {
    background: var(--blue-50);
    color: var(--blue-700);
  }

  .pe-adj {
    background: var(--green-50);
    color: var(--green-700);
  }

  .pe-obs {
    background: var(--rose-50);
    color: var(--rose-700);
  }

  .pac-money {
    font-size: 13px;
    font-weight: 800;
    color: var(--slate-900);
    white-space: nowrap;
  }

  /* =========================
     RESPONSIVE
     ========================= */
  @media (max-width: 1200px) {
    .detail-grid {
      grid-template-columns: 360px 1fr;
    }
  }

  @media (max-width: 1100px) {
    .form-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 1024px) {
    .topbar-main {
      flex-direction: column;
      align-items: stretch;
    }

    .topbar-right {
      justify-content: flex-start;
    }

    .status-block {
      align-items: flex-start;
    }

    .process-title {
      font-size: 28px;
      max-width: 100%;
    }
  }

  @media (max-width: 1023px) {
    .detail-grid {
      grid-template-columns: 1fr;
    }

    .sticky-panel {
      position: relative;
      top: auto;
    }
  }

  @media (max-width: 900px) {
    .pac-table-head {
      display: none;
    }

    .pac-row {
      grid-template-columns: 1fr;
      gap: 8px;
      padding: 14px;
    }

    .col-monto {
      justify-content: flex-start;
    }
  }

  @media (max-width: 640px) {
    .detail-topbar {
      padding: 16px;
      gap: 16px;
    }

    .topbar-left {
      gap: 12px;
    }

    .process-title {
      font-size: 22px;
      line-height: 1.12;
    }

    .process-meta {
      margin-top: 12px;
    }

    .topbar-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .topbar-actions-left,
    .topbar-actions-right {
      width: 100%;
    }

    .topbar-actions-left {
      flex-direction: column;
    }

    .topbar-actions-left .btn-soft,
    .topbar-actions-right .btn-primary {
      width: 100%;
    }

    .trow {
      flex-direction: column;
    }

    .kv-grid,
    .adj-grid,
    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .money-value {
      font-size: 24px;
    }

    .summary-title {
      font-size: 17px;
    }

    .section-title {
      font-size: 18px;
    }
  }

  /* =========================
     PRINT
     ========================= */
  @media print {
    body {
      background: #fff !important;
    }

    .detail-topbar,
    .tab-bar,
    .actions,
    .btn-primary,
    .btn-soft,
    .btn-ghost,
    #activityFormWrap {
      display: none !important;
    }

    .summary-panel {
      display: none !important;
    }

    .detail-grid {
      grid-template-columns: 1fr !important;
    }

    .panel {
      border: none !important;
      box-shadow: none !important;
    }

    body.print-timeline #tab-project,
    body.print-timeline #tab-pacs {
      display: none !important;
    }

    body.print-project #tab-timeline,
    body.print-project #tab-pacs {
      display: none !important;
    }

    .tcard,
    .roadmap-card {
      page-break-inside: avoid;
    }

    .timeline,
    .roadmap {
      gap: 12px;
    }

    .section-title {
      font-size: 18px;
    }
  }
</style>

<script>
  function closeAllMenus() {
    document.querySelectorAll('[data-menu]').forEach(menu => {
      menu.classList.add('hidden');
    });
  }

  function toggleActivityForm() {
    const wrap = document.getElementById('activityFormWrap');
    if (!wrap) return;
    wrap.classList.toggle('hidden');
  }

  function activarTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.tab === tabName);
    });

    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.add('hidden');
    });

    const target = document.getElementById('tab-' + tabName);
    if (target) {
      target.classList.remove('hidden');
    }
  }

  function printTimeline() {
    document.body.classList.remove('print-project');
    document.body.classList.add('print-timeline');

    activarTab('timeline');

    setTimeout(() => {
      window.print();
      document.body.classList.remove('print-timeline');
    }, 300);
  }

  function printProject() {
    document.body.classList.remove('print-timeline');
    document.body.classList.add('print-project');

    activarTab('project');

    setTimeout(() => {
      window.print();
      document.body.classList.remove('print-project');
    }, 300);
  }

  document.addEventListener('click', (e) => {
    const menuBtn = e.target.closest('[data-menu-btn]');
    if (menuBtn) {
      e.preventDefault();
      e.stopPropagation();

      const wrap = menuBtn.closest('.actions');
      const menu = wrap?.querySelector('[data-menu]');
      const wasOpen = menu && !menu.classList.contains('hidden');

      closeAllMenus();

      if (menu && !wasOpen) {
        menu.classList.remove('hidden');
      }
      return;
    }

    const deleteBtn = e.target.closest('[data-del]');
    if (deleteBtn) {
      e.preventDefault();
      e.stopPropagation();

      const id = deleteBtn.getAttribute('data-del');
      const name = deleteBtn.getAttribute('data-name') || 'este proceso';

      if (!confirm(`¿Eliminar ${name}? Esta acción no se puede deshacer.`)) {
        return;
      }

      window.location.href = `<?= BASE_URL ?>/admin/procesos/eliminar?id=${id}`;
      return;
    }

    if (e.target.closest('[data-menu]')) {
      return;
    }

    closeAllMenus();
  });

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        activarTab(btn.dataset.tab);
      });
    });

    activarTab('timeline');
  });
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>