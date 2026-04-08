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

  <!-- TOP BAR -->
  <div class="detail-topbar">
    <div class="left">
      <a href="<?= BASE_URL ?>/admin/procesos" class="btn-icon" aria-label="Volver">←</a>

      <div class="tt">
        <div class="kicker">
          <span>Detalle del proceso</span>
        </div>

        <div class="title-wrap">
          <div class="title marker-label"><?= h($proceso['descripcion'] ?? '-') ?></div>

          <div class="top-meta">
            <span class="type-pill <?= h($tipoPillClass) ?>">
              <?= h($tipoLabel) ?>
            </span>

            <span class="top-sep">•</span>
            <span class="top-code"><?= v($proceso['codigo_proceso'] ?? '') ?></span>

            <?php if (!empty($proceso['periodo'])): ?>
              <span class="top-sep">•</span>
              <span class="top-code">Periodo <?= h($proceso['periodo']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="right">
      <span class="pill <?= h(statusPillClass($estadoUp)) ?>">
        <?= h($estadoUp ?: '-') ?>
      </span>

      <button type="button" class="btn-soft" onclick="printTimeline()">
        🖨️ Timeline
      </button>

      <button type="button" class="btn-soft" onclick="printProject()">
        🖨️ Project
      </button>

      <button type="button" class="btn-primary" onclick="toggleActivityForm()">
        + Actividad
      </button>

      <div class="actions">
        <button type="button" class="btn-icon" data-menu-btn aria-label="Más acciones">⋯</button>

        <div class="menu hidden" data-menu>
          <a class="menu-item" href="<?= BASE_URL ?>/admin/procesos/editar?id=<?= $idProceso ?>">
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
        <div class="k">Tipo</div>
        <div class="v"><?= h($tipoLabel) ?></div>
      </div>

      <div class="kv">
        <div class="k">Año convocatoria</div>
        <div class="v"><?= v($proceso['anio_convocatoria'] ?? '') ?></div>
      </div>

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

      <div class="kv">
        <div class="k">PACs vinculados</div>
        <div class="v" style="display:flex;flex-wrap:wrap;gap:6px;">
          <?php if (empty($pacs_vinculados)): ?>
            -
          <?php else: ?>
            <?php foreach ($pacs_vinculados as $p): ?>
              <?php
              $obac = trim((string)($p['obac_nombre'] ?? ''));
              $nopac = trim((string)($p['nopac'] ?? ''));
              $codigo = $obac && $nopac ? $obac . '-' . $nopac : $nopac;
              ?>
              <span class="chip"><?= h($codigo) ?></span>
            <?php endforeach; ?>
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

  @media (max-width:1200px) {
    .detail-grid {
      grid-template-columns: 360px 1fr;
    }
  }

  @media (max-width:1023px) {
    .detail-grid {
      grid-template-columns: 1fr;
    }
  }

  .detail-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 18px;
    position: sticky;
    top: 12px;
    z-index: 20;
  }

  .detail-topbar .left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1;
  }

  .detail-topbar .right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .tt {
    min-width: 0;
  }

  .kicker,
  .kicker2 {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    letter-spacing: .01em;
  }

  .marker-label {
    display: inline-block;
    position: relative;
    color: #334155;
    font-weight: 800;
    padding: 0 4px;
    z-index: 1;
  }

  .marker-label::before {
    content: "";
    position: absolute;
    left: -2px;
    right: -2px;
    bottom: 1px;
    height: 0.8em;
    background: #fde047;
    border-radius: 4px;
    z-index: -1;
    transform: rotate(-1deg);
    opacity: .95;
  }

  .title-wrap {
    min-width: 0;
  }

  .title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.15;
    letter-spacing: -0.02em;
    margin-top: 2px;
  }

  .top-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
  }

  .top-sep {
    color: #94a3b8;
    font-size: 12px;
  }

  .top-code {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
  }

  .type-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 11px;
    font-size: 11px;
    font-weight: 700;
  }

  .tp-ind {
    background: #e0f2fe;
    color: #0369a1;
  }

  .tp-corp {
    background: #ede9fe;
    color: #6d28d9;
  }

  .btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    text-decoration: none;
    color: #0f172a;
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
  }

  .btn-soft,
  .btn-ghost {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #0f172a;
  }

  .btn-primary {
    border: 1px solid #0f172a;
    background: #0f172a;
    color: #fff;
  }

  .actions {
    position: relative;
  }

  .menu {
    position: absolute;
    right: 0;
    top: 46px;
    width: 210px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, .12);
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
    color: #0f172a;
    text-decoration: none;
    border: none;
    background: transparent;
    cursor: pointer;
  }

  .menu-item:hover {
    background: #f8fafc;
  }

  .menu-item.danger {
    color: #e11d48;
  }

  .panel {
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 18px;
    padding: 18px;
  }

  .sticky-panel {
    position: sticky;
    top: 86px;
    align-self: start;
  }

  @media (max-width:1023px) {
    .sticky-panel {
      position: relative;
      top: auto;
    }
  }

  .summary-panel {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .summary-head {
    display: flex;
    gap: 14px;
    align-items: flex-start;
  }

  .summary-badge {
    min-width: 58px;
    height: 58px;
    border-radius: 18px;
    background: #0f172a;
    color: #fff;
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
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .summary-title {
    margin-top: 4px;
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    letter-spacing: -0.02em;
  }

  .obac-name {
    margin-top: 4px;
    font-size: 13px;
    color: #64748b;
  }

  .chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
  }

  .chip {
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 4px 10px;
  }

  .money-card {
    padding: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #f8fafc;
  }

  .money-label {
    font-size: 12px;
    color: #64748b;
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
    color: #64748b;
  }

  .money-value {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: #0f172a;
    letter-spacing: -0.03em;
  }

  .money-sub {
    margin-top: 6px;
    font-size: 12px;
    color: #64748b;
  }

  .desc-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #fff;
    padding: 16px;
  }

  .desc-kicker {
    font-size: 12px;
    color: #64748b;
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
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px;
    background: #fff;
  }

  .kv .k {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
  }

  .kv .v {
    font-size: 14px;
    color: #0f172a;
    font-weight: 700;
    margin-top: 3px;
    line-height: 1.4;
  }

  .adj-block {
    border: 1px solid #86efac;
    border-radius: 16px;
    padding: 14px;
    background: #f0fdf4;
  }

  .adj-label {
    font-size: 11px;
    font-weight: 700;
    color: #15803d;
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
    background: #fff;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 10px;
  }

  .adj-kv.full {
    grid-column: 1 / -1;
  }

  .adj-k {
    font-size: 12px;
    color: #15803d;
    font-weight: 600;
  }

  .adj-v {
    font-size: 14px;
    color: #14532d;
    font-weight: 700;
    margin-top: 3px;
  }

  .right-panel {
    display: flex;
    flex-direction: column;
  }

  .tab-bar {
    display: flex;
    gap: 4px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 16px;
  }

  .tab-btn {
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
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
    color: #0f172a;
    border-bottom-color: #0f172a;
  }

  .tab-count {
    background: #f1f5f9;
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
    color: #0f172a;
    line-height: 1.15;
    letter-spacing: -0.02em;
    margin-top: 2px;
  }

  .section-subtitle {
    margin-top: 6px;
    font-size: 13px;
    color: #64748b;
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
    background: #f1f5f9;
    color: #334155;
  }

  .pill-wine {
    background: #7a1e2c;
    color: #fff;
  }

  .pill-amber {
    background: #fef3c7;
    color: #92400e;
  }

  .pill-rose {
    background: #ffe4e6;
    color: #be123c;
  }

  .pill-dark {
    background: #e2e8f0;
    color: #0f172a;
  }

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
    background: #e2e8f0;
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
    border: 3px solid #fff;
  }

  .dot-wine {
    background: #7a1e2c;
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
    background: #94a3b8;
  }

  .tcard {
    border: 1px solid #dbe3ee;
    border-radius: 18px;
    padding: 16px;
    background: #fff;
    transition: .18s ease;
  }

  .tcard:hover {
    border-color: #cbd5e1;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    transform: translateY(-1px);
  }

  .titem.is-current .tcard {
    border-color: #cbd5e1;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
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
    color: #0f172a;
    line-height: 1.35;
  }

  .tbadge {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 5px 10px;
    border-radius: 999px;
    white-space: nowrap;
  }

  .tmeta {
    margin-top: 7px;
    font-size: 12px;
    color: #64748b;
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
    background: #e2e8f0;
    color: #334155;
    font-size: 11px;
    font-weight: 700;
  }

  .tdesc {
    margin-top: 11px;
    color: #475569;
    line-height: 1.6;
    font-size: 13px;
  }

  .pac-table {
    display: flex;
    flex-direction: column;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
  }

  .pac-table-head {
    display: grid;
    grid-template-columns: 80px 70px 70px 1fr 110px 120px;
    padding: 10px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    gap: 12px;
    align-items: center;
  }

  .pac-table-head>div {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .pac-row {
    display: grid;
    grid-template-columns: 70px 64px 1fr 110px 90px 120px;
    padding: 14px 16px;
    gap: 12px;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
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
    background: #0f172a;
    color: #fff;
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
    background: #e0f2fe;
    color: #0369a1;
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
    color: #0f172a;
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100px;
  }

  .tag-empty {
    font-size: 13px;
    color: #94a3b8;
  }

  .pac-estado {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 11px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
  }

  .pe-inc {
    background: #e0f2fe;
    color: #0369a1;
  }

  .pe-adj {
    background: #dcfce7;
    color: #15803d;
  }

  .pe-obs {
    background: #ffe4e6;
    color: #be123c;
  }

  .pac-money {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
  }

  .roadmap {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .roadmap-card {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 16px;
    background: #fff;
    transition: .2s ease;
  }

  .roadmap-card:hover {
    box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
    border-color: #cbd5e1;
  }

  .roadmap-card:last-child {
    border-color: #cbd5e1;
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
    color: #0f172a;
  }

  .roadmap-meta {
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
    font-weight: 600;
  }

  .roadmap-last {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    background: #f1f5f9;
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
    background: #cbd5e1;
  }

  .roadmap-node.is-last .roadmap-dot {
    width: 14px;
    height: 14px;
    background: #0f172a;
    box-shadow: 0 0 0 4px rgba(15, 23, 42, .08);
  }

  .roadmap-date {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
  }

  .roadmap-line {
    height: 2px;
    width: 34px;
    background: linear-gradient(to right, #e2e8f0, #94a3b8);
  }

  .reprog-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
    margin-left: 6px;
  }

  .roadmap-desc {
    font-size: 13px;
    color: #475569;
    line-height: 1.5;
    margin-top: 6px;
  }

  .empty {
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    padding: 14px;
    color: #64748b;
    background: #fff;
  }

  .activity-form-wrap {
    margin-bottom: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px;
    background: #f8fafc;
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
    color: #64748b;
  }

  .field input,
  .field select,
  .field textarea {
    width: 100%;
    border: 1px solid #dbe2ea;
    border-radius: 14px;
    background: #fff;
    color: #0f172a;
    padding: 11px 13px;
    font-size: 14px;
    outline: none;
    transition: .18s ease;
  }

  .field input:focus,
  .field select:focus,
  .field textarea:focus {
    border-color: #94a3b8;
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

  @media (max-width:1100px) {
    .form-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width:900px) {
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

  @media (max-width:800px) {
    .pac-row {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width:640px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .detail-topbar {
      flex-direction: column;
      align-items: flex-start;
    }

    .detail-topbar .right {
      width: 100%;
    }

    .trow {
      flex-direction: column;
    }

    .kv-grid,
    .adj-grid {
      grid-template-columns: 1fr;
    }

    .money-value {
      font-size: 24px;
    }

    .title {
      font-size: 18px;
    }

    .summary-title {
      font-size: 17px;
    }

    .section-title {
      font-size: 18px;
    }
  }

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