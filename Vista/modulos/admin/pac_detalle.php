<?php
// Vista/modulos/admin/pac_detalle.php
$titulo = 'Detalle | PAC';
$active = 'pac';

require_once __DIR__ . '/../../../Config/config.php';

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

function pillActividadEstado(string $estado): string
{
  $e = strtoupper(trim($estado));

  return match ($e) {
    'PUBLICADO'    => 'pill-green',
    'SOLICITADO'   => 'pill-blue',
    'REITERADO'    => 'pill-violet',
    'RECEPCIONADO' => 'pill-cyan',
    'OBSERVADO'    => 'pill-amber',
    'SUBSANADO'    => 'pill-orange',
    'APROBADO'     => 'pill-emerald',
    default        => 'pill-slate',
  };
}

function pillEstado(string $estado): string
{
  $e = strtoupper(trim($estado));
  return match ($e) {
    'PUBLICADO' => 'pill-green',
    'BORRADOR'  => 'pill-slate',
    'OBSERVADO' => 'pill-amber',
    'ANULADO'   => 'pill-rose',
    default     => 'pill-slate',
  };
}


$pac = $pac ?? null;
$actividades = $actividades ?? [];

$idPac = (int)($pac['id'] ?? ($_GET['id'] ?? 0));

require __DIR__ . '/../../layout/admin_layout.php';

if ($idPac <= 0) {
  echo "<div class='rounded-2xl border border-slate-200 bg-white p-4'>ID inválido.</div>";
  require __DIR__ . '/../../layout/admin_footer.php';
  exit;
}

if (!$pac || !is_array($pac)) {
  echo "<div class='rounded-2xl border border-slate-200 bg-white p-4'>PAC no encontrado.</div>";
  require __DIR__ . '/../../layout/admin_footer.php';
  exit;
}

$estadoUp = strtoupper(trim((string)($pac['estado_nombre'] ?? '')));
$last = !empty($actividades) ? $actividades[count($actividades) - 1] : null;
?>

<div class="detail-shell space-y-4">

  <!-- TOP BAR -->
  <div class="detail-topbar">
    <div class="left">
      <a href="<?= BASE_URL ?>/admin/pac" class="btn-icon" aria-label="Volver">←</a>

      <div class="tt">
        <div class="kicker">Detalle del PAC</div>
        <div class="title">PAC N° <?= h($pac['nopac'] ?? '-') ?></div>
      </div>
    </div>

    <div class="right">
      <span class="pill <?= h(pillEstado($estadoUp)) ?>"><?= h($estadoUp ?: '-') ?></span>

      <a href="<?= BASE_URL ?>/admin/pac?edit=<?= $idPac ?>" class="btn-soft">Editar</a>

      <button type="button" class="btn-primary" onclick="toggleActivityForm()">
        + Actividad
      </button>

      <div class="actions">
        <button type="button" class="btn-icon" data-menu-btn aria-label="Más acciones">⋯</button>
        <div class="menu hidden" data-menu>
          <a class="menu-item" href="<?= BASE_URL ?>/admin/pac?edit=<?= $idPac ?>">✏️ Editar PAC</a>
          <button
            type="button"
            class="menu-item danger"
            data-delete-pac
            data-id="<?= (int)$idPac ?>"
            data-nopac="<?= h($pac['nopac'] ?? '-') ?>">
            🗑️ Eliminar PAC
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- GRID -->
  <div class="detail-grid">

    <!-- LEFT -->
    <aside class="panel sticky-panel">
      <div class="panel-head">
        <div class="badge"><?= h($pac['pn'] ?? '-') ?></div>

        <div class="meta">
          <div class="h1"><?= h($pac['descripcion'] ?? '-') ?></div>
          <div class="submeta">
            <?= v($pac['obac_nombre'] ?? '') ?> · <?= v($pac['fuente_nombre'] ?? '') ?>
          </div>
        </div>
      </div>

      <div class="money-card">
        <div class="money-label">Estimado</div>
        <div class="money-value"><?= fmt_money($pac['estimado'] ?? 0) ?></div>
      </div>

      <div class="kv-grid">
        <div class="kv">
          <div class="k">Periodo</div>
          <div class="v"><?= v($pac['periodo_nombre'] ?? '') ?></div>
        </div>
        <div class="kv">
          <div class="k">Mes convocatoria</div>
          <div class="v"><?= v($pac['mesconvoca'] ?? '') ?></div>
        </div>
        <div class="kv">
          <div class="k">Cantidad</div>
          <div class="v"><?= v($pac['cantidad'] ?? '') ?></div>
        </div>
        <div class="kv">
          <div class="k">Última actividad</div>
          <div class="v"><?= $last ? v($last['tipo_actividad_nombre'] ?? '-') : '-' ?></div>
        </div>
      </div>

      <div class="panel-actions">
        <button type="button" class="btn-primary wfull" onclick="toggleActivityForm()">Registrar actividad</button>
      </div>
    </aside>

    <!-- RIGHT -->
    <section class="panel">
      <div class="section-head">
        <div>
          <div class="kicker2">Seguimiento</div>
          <div class="section-title">Línea de tiempo</div>
        </div>
        <div class="pill pill-slate"><?= (int)count($actividades) ?> actividades</div>
      </div>

      <div id="activityFormWrap" class="activity-form-wrap hidden">
        <form action="<?= BASE_URL ?>/admin/pac_actividad_guardar" method="POST" class="activity-form">
          <input type="hidden" name="pac_id" value="<?= (int)$idPac ?>">

          <div class="form-grid">
            <div class="field field-full">
              <label>Actividad</label>
              <select name="tipo_actividad_id" required>
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

      <?php if (empty($actividades)): ?>
        <div class="empty">
          No hay actividades registradas para este PAC.
        </div>
      <?php else: ?>
        <ol class="timeline">
          <?php foreach ($actividades as $a): ?>
            <li class="titem">
              <div class="tline"></div>
              <div class="dot dot-blue"></div>

              <article class="tcard">
                <div class="trow">
                  <div class="ttitle"><?= v($a['tipo_actividad_nombre'] ?? '-') ?></div>

                  <span class="pill <?= h(pillActividadEstado($a['tipo_actividad_estado'] ?? '')) ?>">
                    <?= h($a['tipo_actividad_estado'] ?? '-') ?>
                  </span>
                </div>

                <div class="tmeta">
                  <span><?= fmt_date($a['fecha'] ?? null) ?></span>
                </div>

                <?php if (!empty($a['comentario'])): ?>
                  <div class="tdesc">
                    <?= nl2br(h($a['comentario'])) ?>
                  </div>
                <?php endif; ?>
              </article>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </section>

  </div>
</div>

<!-- MODAL ELIMINAR -->
<div id="deleteModal" class="modal-backdrop hidden" aria-hidden="true">
  <div class="modal-card">
    <div class="modal-title">Eliminar PAC</div>
    <div class="modal-text">
      ¿Deseas eliminar el PAC <strong id="delPacTxt">-</strong>? Esta acción no se puede deshacer.
    </div>
    <div class="modal-actions">
      <button type="button" class="btn-ghost" onclick="closeDeleteModal()">Cancelar</button>
      <button type="button" class="btn-danger" onclick="goDelete()">Eliminar</button>
    </div>
  </div>
</div>

<style>
  .detail-shell {
    width: 100%;
    max-width: 1600px;
    margin: 0;
  }

  .detail-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 18px;
  }

  @media (max-width:1023px) {
    .detail-grid {
      grid-template-columns: 1fr;
    }
  }

  /* TOP BAR */
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
  }

  .detail-topbar .right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .kicker,
  .kicker2 {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    letter-spacing: .01em;
  }

  .title,
  .section-title {
    color: #0f172a;
    line-height: 1.2;
  }

  .title {
    font-size: 18px;
    font-weight: 700;
  }

  .section-title {
    font-size: 18px;
    font-weight: 700;
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
  .btn-ghost,
  .btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border-radius: 14px;
    padding: 9px 14px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: .18s ease;
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

  .btn-danger {
    border: 1px solid #fecdd3;
    background: #fff1f2;
    color: #be123c;
  }

  .wfull {
    width: 100%;
  }

  /* MENU */
  .actions {
    position: relative;
  }

  .menu {
    position: absolute;
    right: 0;
    top: 46px;
    width: 200px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, .12);
    padding: 6px;
  }

  .menu.hidden,
  .hidden {
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

  /* PANELS */
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

  /* LEFT SUMMARY */
  .panel-head {
    display: flex;
    gap: 14px;
    align-items: flex-start;
  }

  .badge {
    min-width: 54px;
    height: 54px;
    border-radius: 16px;
    background: #0f172a;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 700;
  }

  .meta {
    flex: 1;
    min-width: 0;
  }

  .h1 {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
  }

  .submeta {
    margin-top: 4px;
    font-size: 13px;
    color: #64748b;
    line-height: 1.5;
  }

  .money-card {
    margin-top: 16px;
    padding: 14px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #f8fafc;
  }

  .money-label {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
  }

  .money-value {
    margin-top: 4px;
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
  }

  .kv-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 16px;
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
    font-weight: 600;
    margin-top: 3px;
    line-height: 1.4;
  }

  .panel-actions {
    margin-top: 18px;
  }

  /* SECTION */
  .section-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }

  /* PILLS */
  .pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .pill-slate {
    background: #f1f5f9;
    color: #334155;
  }

  .pill-green {
    background: #dcfce7;
    color: #166534;
  }

  .pill-amber {
    background: #fef3c7;
    color: #92400e;
  }

  .pill-rose {
    background: #ffe4e6;
    color: #be123c;
  }

  /* FORM */
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
  }

  .field input:focus,
  .field select:focus,
  .field textarea:focus {
    border-color: #94a3b8;
    box-shadow: 0 0 0 4px rgba(148, 163, 184, .15);
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

  @media (max-width:640px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
  }

  /* TIMELINE */
  .timeline {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: relative;
  }

  .titem {
    position: relative;
    display: grid;
    grid-template-columns: 24px 1fr;
    gap: 14px;
  }

  .tline {
    position: absolute;
    left: 12px;
    top: 0;
    bottom: -14px;
    width: 2px;
    background: #e2e8f0;
  }

  .dot {
    width: 14px;
    height: 14px;
    border-radius: 999px;
    margin-top: 18px;
    margin-left: 4px;
    border: 2px solid #fff;
    box-shadow: 0 6px 16px rgba(15, 23, 42, .08);
    background: #94a3b8;
  }

  .dot-green {
    background: #22c55e;
  }

  .dot-blue {
    background: #3b82f6;
  }

  .dot-amber {
    background: #f59e0b;
  }

  .dot-rose {
    background: #e11d48;
  }

  .dot-slate {
    background: #94a3b8;
  }

  .tcard {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 14px;
    background: #fff;
  }

  .trow {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: flex-start;
  }

  .ttitle {
    font-size: 15px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.4;
  }

  .tbadge {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 4px 8px;
    border-radius: 999px;
    white-space: nowrap;
  }

  .tmeta {
    margin-top: 6px;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .sep {
    color: #94a3b8;
  }

  .tdesc {
    margin-top: 10px;
    color: #334155;
    line-height: 1.55;
    font-size: 13px;
  }

  .empty {
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    padding: 14px;
    color: #64748b;
    background: #fff;
  }

  /* MODAL */
  .modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .35);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 60;
    padding: 16px;
  }

  .hidden {
    display: none !important;
  }

  .modal-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 22px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
    padding: 22px;
  }

  .modal-title {
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
  }

  .modal-text {
    font-size: 14px;
    color: #475569;
    line-height: 1.5;
  }

  .modal-actions {
    margin-top: 18px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
  }

  @media (max-width:640px) {
    .detail-topbar {
      align-items: flex-start;
      flex-direction: column;
    }

    .detail-topbar .right {
      width: 100%;
    }

    .trow,
    .form-actions,
    .modal-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .kv-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
  let deletePacId = null;

  const deleteModal = document.getElementById('deleteModal');
  const delPacTxt = document.getElementById('delPacTxt');

  function toggleActivityForm() {
    const wrap = document.getElementById('activityFormWrap');
    if (!wrap) return;
    wrap.classList.toggle('hidden');
  }

  function closeAllMenus() {
    document.querySelectorAll('[data-menu]').forEach(menu => {
      menu.classList.add('hidden');
    });
  }

  function openDeleteModal(id, nopac) {
    deletePacId = id;
    delPacTxt.textContent = nopac || '-';
    deleteModal.classList.remove('hidden');
    deleteModal.setAttribute('aria-hidden', 'false');
  }

  function closeDeleteModal() {
    deletePacId = null;
    deleteModal.classList.add('hidden');
    deleteModal.setAttribute('aria-hidden', 'true');
  }

  function goDelete() {
    if (!deletePacId) return;
    window.location.href = '<?= BASE_URL ?>/admin/pac_eliminar?id=' + encodeURIComponent(deletePacId);
  }

  document.addEventListener('DOMContentLoaded', function() {
    closeDeleteModal();

    document.querySelectorAll('[data-menu-btn]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const wrap = btn.closest('.actions, .more-actions');
        const menu = wrap ? wrap.querySelector('[data-menu]') : null;
        if (!menu) return;

        const wasOpen = !menu.classList.contains('hidden');
        closeAllMenus();

        if (!wasOpen) {
          menu.classList.remove('hidden');
        }
      });
    });

    document.querySelectorAll('[data-delete-pac]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const id = btn.getAttribute('data-id');
        const nopac = btn.getAttribute('data-nopac') || '-';

        closeAllMenus();
        openDeleteModal(id, nopac);
      });
    });

    document.addEventListener('click', function(e) {
      if (
        e.target.closest('[data-menu]') ||
        e.target.closest('[data-menu-btn]') ||
        e.target.closest('.modal-card')
      ) {
        return;
      }

      closeAllMenus();

      if (!deleteModal.classList.contains('hidden')) {
        closeDeleteModal();
      }
    });

    deleteModal.addEventListener('click', function(e) {
      if (e.target === deleteModal) {
        closeDeleteModal();
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeAllMenus();
        closeDeleteModal();
      }
    });
  });
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>