<?php
$titulo = 'Alertas | Seguimiento';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

/* =========================================
   VISTA SOLO ALERTAS (MAQUETA)
   - Sin indicadores
   - Con filtros (severidad + módulo)
   - Buscador
   - Sheet filtros (móvil) estilo tu app
========================================= */

// ===== MOCK ALERTAS =====
$alerts = [
  // sev: critical | warn | info
  [
    'id' => 101,
    'sev' => 'critical',
    'module' => 'PROCESOS',
    'title' => 'Proceso OBSERVADO requiere atención',
    'desc' => 'Expediente 610 - Responder observaciones para continuar.',
    'time' => 'Hace 2 h',
    'href' => BASE_URL . '/procesos'
  ],
  [
    'id' => 102,
    'sev' => 'critical',
    'module' => 'PROCESOS',
    'title' => 'Proceso DESIERTO',
    'desc' => 'Requiere reprogramación / nueva convocatoria.',
    'time' => 'Hoy',
    'href' => BASE_URL . '/procesos'
  ],
  [
    'id' => 201,
    'sev' => 'warn',
    'module' => 'PAC',
    'title' => 'PAC SOLICITADO pendiente',
    'desc' => 'Pendiente de publicación y programación.',
    'time' => 'Hace 1 d',
    'href' => BASE_URL . '/pac'
  ],
  [
    'id' => 301,
    'sev' => 'warn',
    'module' => 'SISTEMA',
    'title' => 'Registros incompletos',
    'desc' => '1 registro con monto 0 o datos faltantes.',
    'time' => 'Hace 3 d',
    'href' => '#'
  ],
  [
    'id' => 401,
    'sev' => 'info',
    'module' => 'SISTEMA',
    'title' => 'Sincronización completada',
    'desc' => 'Actualización nocturna finalizada correctamente.',
    'time' => 'Ayer',
    'href' => '#'
  ],
];

function sevLabel($sev) {
  return match ($sev) {
    'critical' => 'Crítica',
    'warn' => 'Atención',
    default => 'Info',
  };
}
function sevPillClass($sev) {
  return match ($sev) {
    'critical' => 'pill-critical',
    'warn' => 'pill-warn',
    default => 'pill-info',
  };
}
function sevIcon($sev) {
  return match ($sev) {
    'critical' => '⛔',
    'warn' => '⚠️',
    default => 'ℹ️',
  };
}
?>

<main class="page page-shell flex-1 px-5 pt-4 main-alertas">

  <!-- HEADER / FILTROS (COMPACTO + SHEET) -->
  <section class="mb-5 filtros-sticky">
    <div class="bg-white/90 text-slate-900 rounded-2xl p-4 shadow-lg filtros-wrap">

      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm text-slate-500">Bandeja de seguimiento</p>
          <h2 class="text-2xl font-semibold mt-1">Alertas</h2>
        </div>

        <div class="year-pill">
          <span class="dot"></span>
          <span>AF-2026</span>
        </div>
      </div>

      <!-- BAR COMPACTA -->
      <div class="mt-4 flex items-center gap-2">
        <div class="search flex-1">
          <span class="search-ico">🔎</span>
          <input id="q" type="text" placeholder="Buscar alerta, módulo o detalle..." />
        </div>

        <button id="btnFiltros" type="button" class="btn-filtros" aria-haspopup="dialog" aria-controls="sheetFiltros">
          <span class="ico">⚙️</span>
          <span class="txt">Filtros</span>
          <span id="badgeCount" class="badge-count hidden">0</span>
        </button>
      </div>

      <!-- CHIPS ACTIVOS (RESUMEN) -->
      <div id="chipsActivos" class="mt-3 flex gap-2 overflow-x-auto pb-1 hidden"></div>

    </div>
  </section>

  <!-- SHEET FILTROS (MÓVIL) -->
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

      <!-- SEVERIDAD -->
      <div class="sheet-section" id="fSev">
        <p class="sheet-label">Severidad</p>
        <div class="chips-grid">
          <button class="chip chip-active" type="button" data-filter="sev" data-value="">Todas</button>
          <button class="chip" type="button" data-filter="sev" data-value="critical">Críticas</button>
          <button class="chip" type="button" data-filter="sev" data-value="warn">Atención</button>
          <button class="chip" type="button" data-filter="sev" data-value="info">Info</button>
        </div>
      </div>

      <!-- MÓDULO -->
      <div class="sheet-section" id="fMod">
        <p class="sheet-label">Módulo</p>
        <div class="chips-grid">
          <button class="chip chip-active" type="button" data-filter="module" data-value="">Todos</button>
          <button class="chip" type="button" data-filter="module" data-value="PROCESOS">Procesos</button>
          <button class="chip" type="button" data-filter="module" data-value="PAC">PAC</button>
          <button class="chip" type="button" data-filter="module" data-value="SISTEMA">Sistema</button>
        </div>
      </div>

      <!-- ESTADO (solo UI) -->
      <div class="sheet-section" id="fEstado">
        <p class="sheet-label">Estado</p>
        <div class="chips-grid">
          <button class="chip chip-active chip-soft" type="button" data-filter="st" data-value="">Todas</button>
          <button class="chip chip-soft" type="button" data-filter="st" data-value="OPEN">Abiertas</button>
          <button class="chip chip-soft" type="button" data-filter="st" data-value="DONE">Resueltas</button>
        </div>
      </div>

    </div>

    <div class="sheet-actions">
      <button id="btnLimpiar" class="btn-secondary" type="button">Limpiar</button>
      <button id="btnAplicar" class="btn-primary" type="button">Aplicar</button>
    </div>
  </div>

  <!-- LISTA ALERTAS -->
  <section class="lista-scroll">
    <section class="space-y-3" id="listaAlertas">

      <?php foreach ($alerts as $a): ?>
        <?php
          $sev = (string)$a['sev'];
          $mod = strtoupper(trim((string)$a['module']));
          $title = (string)$a['title'];
          $desc = (string)$a['desc'];
          $time = (string)$a['time'];
          $hay = strtolower($title.' '.$desc.' '.$mod.' '.$sev);
        ?>

        <div
          class="alert-item"
          data-sev="<?= htmlspecialchars($sev) ?>"
          data-module="<?= htmlspecialchars($mod) ?>"
          data-st="OPEN"
          data-haystack="<?= htmlspecialchars($hay) ?>"
          data-open="<?= htmlspecialchars($a['href']) ?>">

          <a class="alert-open" href="<?= htmlspecialchars($a['href']) ?>" aria-label="Abrir alerta"></a>

          <div class="left">
            <div class="sev-badge <?= htmlspecialchars(sevPillClass($sev)) ?>">
              <span class="sev-ico"><?= htmlspecialchars(sevIcon($sev)) ?></span>
            </div>

            <div class="info">
              <p class="title"><?= htmlspecialchars($title) ?></p>

              <p class="sub">
                <span class="pill <?= htmlspecialchars(sevPillClass($sev)) ?>">
                  <?= htmlspecialchars(sevLabel($sev)) ?>
                </span>
                <span class="pill pill-mod"><?= htmlspecialchars($mod) ?></span>
                <span class="time"><?= htmlspecialchars($time) ?></span>
              </p>

              <p class="desc"><?= htmlspecialchars($desc) ?></p>
            </div>
          </div>

          <div class="right">
            <div class="actions">
              <button class="kebab" type="button" aria-label="Acciones" data-menu-btn></button>

              <div class="menu hidden" data-menu>
                <a class="menu-item" href="<?= htmlspecialchars($a['href']) ?>">
                  <span class="mi">👁️</span> Ver
                </a>

                <button class="menu-item" type="button" data-mark-done data-id="<?= (int)$a['id'] ?>">
                  <span class="mi">✅</span> Marcar resuelta
                </button>

                <button class="menu-item danger" type="button" data-delete data-id="<?= (int)$a['id'] ?>">
                  <span class="mi">🗑️</span> Eliminar
                </button>
              </div>
            </div>
          </div>

        </div>
      <?php endforeach; ?>

    </section>

    <div class="mt-4 text-xs text-white/70" id="countText">
      Mostrando <?= count($alerts) ?> alertas
    </div>
  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  /* ===== CONTENEDOR DESKTOP (igual patrón) ===== */
  .page-shell { width: 100%; }
  @media (min-width: 1024px) {
    .page-shell { max-width: 1120px; margin: 0 auto; padding-left: 24px; padding-right: 24px; }
  }

  .main-alertas { overflow: hidden; }

  /* ===== Header sticky ===== */
  .filtros-sticky { position: sticky; top: 0; z-index: 80; padding-top: 6px; }
  .filtros-sticky>div {
    background: rgba(255, 255, 255, .96) !important;
    border: 1px solid rgba(148, 163, 184, .25);
    box-shadow: 0 18px 40px rgba(0, 0, 0, .18);
    backdrop-filter: blur(12px);
  }

  .year-pill {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px; border-radius: 9999px;
    background: rgba(107, 28, 38, .10);
    color: #6B1C26;
    border: 1px solid rgba(107, 28, 38, .15);
    font-weight: 700; font-size: .85rem; white-space: nowrap;
  }
  .year-pill .dot {
    width: 8px; height: 8px; border-radius: 9999px;
    background: #C9A227;
    box-shadow: 0 0 0 3px rgba(201, 162, 39, .20);
  }

  /* ===== Search ===== */
  .search {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    border-radius: 1rem;
    background: rgba(248, 250, 252, .95);
    border: 1px solid rgba(148, 163, 184, .35);
  }
  .search-ico { font-size: 1rem; opacity: .75; }
  .search input {
    width: 100%; background: transparent; outline: none; border: none;
    color: #0f172a; font-weight: 800; font-size: .95rem;
  }
  .search input::placeholder { color: #94a3b8; font-weight: 800; }

  /* ===== Lista scroll ===== */
  .lista-scroll {
    overflow-y: auto; -webkit-overflow-scrolling: touch;
    height: calc(100vh - 300px);
    padding-bottom: calc(140px + env(safe-area-inset-bottom));
  }
  @media (min-width: 1024px) {
    .lista-scroll { height: calc(100vh - 260px); padding-bottom: 40px; }
  }

  /* ===== Alert item ===== */
  .alert-item {
    position: relative;
    display: grid;
    grid-template-columns: 52px 1fr auto;
    gap: 14px;
    align-items: start;
    background: #fff;
    padding: 18px 18px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
  }

  .alert-open {
    position: absolute; inset: 0; z-index: 1;
    border-radius: 20px;
  }

  .alert-item>.left, .alert-item>.right { position: relative; z-index: 2; }
  .left { display: contents; }

  .sev-badge {
    width: 48px; height: 48px; border-radius: 999px;
    display: grid; place-items: center;
    font-weight: 900;
    border: 1px solid rgba(148, 163, 184, .25);
  }
  .sev-ico { font-size: 1.1rem; }

  .info { min-width: 0; display: flex; flex-direction: column; gap: 6px; }
  .title { font-weight: 950; font-size: 1rem; color: #0f172a; line-height: 1.15; }
  .sub {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    font-size: .78rem; font-weight: 900; color: #64748b;
  }
  .time { font-weight: 900; color: #475569; opacity: .85; }

  .desc {
    font-size: .85rem; font-weight: 800; color: #334155; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  }
  @media (max-width: 640px) { .desc { -webkit-line-clamp: 3; } }

  /* Pills */
  .pill {
    padding: 5px 10px; border-radius: 999px;
    font-size: .72rem; font-weight: 950;
    border: 1px solid rgba(148, 163, 184, .25);
    white-space: nowrap;
  }
  .pill-mod { background: rgba(148,163,184,.12); color: #0f172a; }

  .pill-critical { background: rgba(185, 28, 28, .12); color: #991b1b; border-color: rgba(185, 28, 28, .20); }
  .pill-warn     { background: rgba(245, 158, 11, .14); color: #7A4A00; border-color: rgba(245, 158, 11, .22); }
  .pill-info     { background: rgba(2, 132, 199, .10); color: #075985; border-color: rgba(2, 132, 199, .20); }

  /* Right */
  .right { display: flex; justify-content: flex-end; min-width: 44px; }
  .actions { position: relative; z-index: 3; }

  /* Kebab + menu (igual patrón) */
  .kebab {
    width: 36px; height: 36px; border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    cursor: pointer;
    display: grid; place-items: center;
    padding: 0; color: #0f172a;
  }
  .kebab::before { content: "⋯"; font-weight: 900; font-size: 1.3rem; transform: translateY(-1px); }

  .menu {
    position: absolute; top: 42px; right: 0;
    width: 210px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, .25);
    overflow: hidden;
    z-index: 999;
  }
  .menu.hidden { display: none; }

  .menu-item {
    padding: 12px 14px;
    display: flex; align-items: center; gap: 10px;
    font-weight: 900; font-size: .85rem;
    color: #0f172a;
    text-decoration: none;
    border: 0;
    background: transparent;
    width: 100%;
    text-align: left;
    cursor: pointer;
  }
  .menu-item:hover { background: #f1f5f9; }
  .menu-item .mi { width: 22px; display: inline-flex; justify-content: center; }
  .menu-item.danger { color: #b91c1c; }

  /* Botón filtros + badge */
  .btn-filtros {
    height: 48px; padding: 0 12px; border-radius: 14px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    display: inline-flex; align-items: center; gap: 8px;
    font-weight: 950; color: #0f172a; white-space: nowrap;
  }
  .btn-filtros .txt { display: none; }
  @media (min-width: 480px) { .btn-filtros .txt { display: inline; } }

  .badge-count {
    min-width: 22px; height: 22px; padding: 0 7px;
    border-radius: 999px;
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
    border: 1px solid rgba(107, 28, 38, .22);
    font-size: .75rem;
    display: inline-flex; align-items: center; justify-content: center;
  }

  /* Chips activos */
  .chip-x {
    padding: 8px 10px;
    border-radius: 9999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: rgba(255, 255, 255, .95);
    font-weight: 950;
    font-size: .78rem;
    color: #0f172a;
    display: flex; align-items: center; gap: 8px;
    flex: 0 0 auto;
  }
  .chip-x button {
    width: 20px; height: 20px; border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    font-weight: 950; line-height: 1;
  }

  /* Overlay + Sheet */
  .overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, .45); z-index: 200; }
  .sheet {
    position: fixed; left: 0; right: 0; bottom: 0;
    z-index: 220;
    background: #fff;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    box-shadow: 0 -20px 50px rgba(0, 0, 0, .25);
    max-height: calc(100vh - 80px);
    display: flex; flex-direction: column;
  }
  .sheet-handle {
    width: 54px; height: 5px; border-radius: 999px;
    background: rgba(148, 163, 184, .55);
    margin: 10px auto 6px;
  }
  .sheet-head {
    padding: 10px 14px;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;
    border-bottom: 1px solid rgba(148, 163, 184, .25);
  }
  .sheet-close {
    position: relative;
    width: 40px; height: 40px; border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc; cursor: pointer;
  }
  .sheet-close::before, .sheet-close::after {
    content: ""; position: absolute; top: 50%; left: 50%;
    width: 18px; height: 2px; background: #0f172a; border-radius: 2px;
  }
  .sheet-close::before { transform: translate(-50%, -50%) rotate(45deg); }
  .sheet-close::after  { transform: translate(-50%, -50%) rotate(-45deg); }

  .sheet-body {
    padding: 12px 14px 0;
    overflow: auto; -webkit-overflow-scrolling: touch;
  }
  .sheet-section { margin-bottom: 14px; }
  .sheet-label { font-weight: 950; color: #334155; font-size: .8rem; margin-bottom: 8px; }
  .chips-grid { display: flex; flex-wrap: wrap; gap: 8px; }

  .chip {
    padding: 10px 14px;
    border-radius: 9999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: rgba(248, 250, 252, .85);
    color: #334155;
    font-weight: 900;
    font-size: .82rem;
    flex: 0 0 auto;
    transition: transform .12s ease;
  }
  .chip:active { transform: scale(.96); }
  .chip-active { background: rgba(107, 28, 38, .12); color: #6B1C26; border-color: rgba(107, 28, 38, .22); }
  .chip-soft { background: rgba(255, 255, 255, .92); }

  .sheet-actions {
    padding: 12px 14px;
    display: flex; gap: 10px;
    border-top: 1px solid rgba(148, 163, 184, .25);
    padding-bottom: calc(12px + env(safe-area-inset-bottom));
  }
  .btn-primary, .btn-secondary {
    height: 46px;
    border-radius: 14px;
    font-weight: 950;
    flex: 1;
  }
  .btn-primary { background: rgba(107, 28, 38, .95); color: #fff; }
  .btn-secondary { background: #f1f5f9; border: 1px solid rgba(148, 163, 184, .35); color: #0f172a; }

  .hidden { display: none !important; }

  @media (min-width: 1024px) {
    .overlay, .sheet { display: none !important; }
  }
</style>

<script>
  // ===== MENÚ KEBAB =====
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

  // Acciones mock
  document.addEventListener('click', (e) => {
    const done = e.target.closest('[data-mark-done]');
    if (done) {
      e.preventDefault();
      e.stopPropagation();
      alert('MAQUETA: marcar como resuelta (sin backend)');
      closeAllMenus();
      return;
    }
    const del = e.target.closest('[data-delete]');
    if (del) {
      e.preventDefault();
      e.stopPropagation();
      if (!confirm('¿Eliminar alerta? (MAQUETA)')) return;
      alert('MAQUETA: eliminar (sin backend)');
      closeAllMenus();
    }
  });

  // ===== FILTROS + BUSCADOR =====
  const state = { sev: '', module: '', st: '', q: '' };

  const applyFilters = () => {
    const cards = document.querySelectorAll('.alert-item');
    let visible = 0;

    cards.forEach(c => {
      const sev = c.getAttribute('data-sev') || '';
      const mod = c.getAttribute('data-module') || '';
      const st  = c.getAttribute('data-st') || '';
      const hay = c.getAttribute('data-haystack') || '';

      const okSev = !state.sev || sev === state.sev;
      const okMod = !state.module || mod === state.module;
      const okSt  = !state.st || st === state.st;
      const okQ   = !state.q || hay.includes(state.q);

      const ok = okSev && okMod && okSt && okQ;
      c.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    const ct = document.getElementById('countText');
    if (ct) ct.textContent = `Mostrando ${visible} alertas`;
  };

  // ===== SHEET FILTROS =====
  const btnFiltros = document.getElementById('btnFiltros');
  const overlay = document.getElementById('overlayFiltros');
  const sheet = document.getElementById('sheetFiltros');
  const btnCerrar = document.getElementById('btnCerrarSheet');
  const btnAplicar = document.getElementById('btnAplicar');
  const btnLimpiar = document.getElementById('btnLimpiar');

  const chipsActivos = document.getElementById('chipsActivos');
  const badgeCount = document.getElementById('badgeCount');

  let draft = { sev: '', module: '', st: '' };
  let applied = { sev: '', module: '', st: '' };

  const openSheet = () => {
    overlay.classList.remove('hidden');
    sheet.classList.remove('hidden');
    overlay.setAttribute('aria-hidden', 'false');
    syncUIToDraft();
    document.body.style.overflow = 'hidden';
  };

  const closeSheet = () => {
    overlay.classList.add('hidden');
    sheet.classList.add('hidden');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };

  const esc = (v) => (window.CSS && CSS.escape) ? CSS.escape(v) : v.replace(/"/g, '\\"');

  const setActiveInGroup = (groupEl, value) => {
    if (!groupEl) return;
    groupEl.querySelectorAll('button.chip').forEach(b => b.classList.remove('chip-active'));
    const btn = groupEl.querySelector(`button[data-value="${esc(value)}"]`);
    if (btn) btn.classList.add('chip-active');
  };

  const syncUIToDraft = () => {
    setActiveInGroup(document.getElementById('fSev'), draft.sev);
    setActiveInGroup(document.getElementById('fMod'), draft.module);
    setActiveInGroup(document.getElementById('fEstado'), draft.st);
  };

  const labelSev = (v) => v === 'critical' ? 'Críticas' : (v === 'warn' ? 'Atención' : (v === 'info' ? 'Info' : 'Todas'));
  const labelMod = (v) => v || 'Todos';
  const labelSt  = (v) => v === 'OPEN' ? 'Abiertas' : (v === 'DONE' ? 'Resueltas' : 'Todas');

  const renderActiveChips = () => {
    const items = [];
    if (applied.sev) items.push({ k: 'sev', label: `Sev: ${labelSev(applied.sev)}` });
    if (applied.module) items.push({ k: 'module', label: `Módulo: ${labelMod(applied.module)}` });
    if (applied.st) items.push({ k: 'st', label: `Estado: ${labelSt(applied.st)}` });

    const count = items.length;
    if (count > 0) {
      chipsActivos.classList.remove('hidden');
      badgeCount.classList.remove('hidden');
      badgeCount.textContent = String(count);
    } else {
      chipsActivos.classList.add('hidden');
      badgeCount.classList.add('hidden');
      badgeCount.textContent = '0';
    }

    chipsActivos.innerHTML = items.map(it => `
      <div class="chip-x" data-k="${it.k}">
        <span>${it.label}</span>
        <button type="button" aria-label="Quitar filtro">×</button>
      </div>
    `).join('');
  };

  btnFiltros?.addEventListener('click', openSheet);
  overlay?.addEventListener('click', closeSheet);
  btnCerrar?.addEventListener('click', closeSheet);

  sheet?.addEventListener('click', (e) => {
    const b = e.target.closest('button[data-filter]');
    if (!b) return;

    const filter = b.getAttribute('data-filter');
    const value = (b.getAttribute('data-value') || '');

    if (filter === 'sev') draft.sev = value;
    if (filter === 'module') draft.module = (value || '').toUpperCase();
    if (filter === 'st') draft.st = (value || '').toUpperCase();

    syncUIToDraft();
  });

  btnLimpiar?.addEventListener('click', () => {
    draft = { sev: '', module: '', st: '' };
    applied = { sev: '', module: '', st: '' };

    state.sev = '';
    state.module = '';
    state.st = '';

    syncUIToDraft();
    renderActiveChips();
    applyFilters();
    closeSheet();
  });

  btnAplicar?.addEventListener('click', () => {
    applied = { ...draft };

    state.sev = applied.sev;
    state.module = applied.module;
    state.st = applied.st;

    renderActiveChips();
    applyFilters();
    closeSheet();
  });

  chipsActivos?.addEventListener('click', (e) => {
    const wrap = e.target.closest('.chip-x');
    const btn = e.target.closest('button');
    if (!wrap || !btn) return;

    const k = wrap.getAttribute('data-k');
    applied[k] = '';
    draft = { ...applied };
    state[k] = '';

    renderActiveChips();
    applyFilters();
  });

  document.getElementById('q')?.addEventListener('input', (e) => {
    state.q = (e.target.value || '').trim().toLowerCase();
    applyFilters();
  });

  // Inicial
  renderActiveChips();
  applyFilters();
</script>