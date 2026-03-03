<?php
$titulo = 'Procesos | Procesos';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';


function fmt_money($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}
function badgeFromObac($obac)
{
  $p = explode('-', $obac);
  return strtoupper(trim($p[0] ?? $obac));
}
function statusClass($estado)
{
  $e = strtoupper(trim($estado));
  return match ($e) {
    'ADJUDICADO' => 'status-dorado',
    'CONVOCADO'  => 'status-vino',
    'PUBLICADO'  => 'status-gris',
    'OBSERVADO'  => 'status-vino',
    'DESIERTO'   => 'status-gris',
    default      => 'status-gris',
  };
}
?>

<main class="page page-shell flex-1 px-5 pt-4 main-procesos">
  <?php $procesos = $procesos ?? []; ?>

  <!-- HEADER / FILTROS -->
  <!-- HEADER / FILTROS (COMPACTO + SHEET) -->
  <section class="mb-5 filtros-sticky">
    <div class="bg-white/90 text-slate-900 rounded-2xl p-4 shadow-lg filtros-wrap">

      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm text-slate-500">Gestión y seguimiento</p>
          <h2 class="text-2xl font-semibold mt-1">Procesos</h2>
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
          <input id="q" type="text" placeholder="Buscar Proceso o descripción..." />
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
        <p class="sheet-label">Estado</p>
        <div class="chips-grid" id="fEstado">
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="CONVOCADO">Convocado</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="PUBLICADO">Publicado</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="ADJUDICADO">Adjudicado</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="OBSERVADO">Observado</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="DESIERTO">Desierto</button>
        </div>
      </div>
    </div>

    <div class="sheet-actions">
      <button id="btnLimpiar" class="btn-secondary" type="button">Limpiar</button>
      <button id="btnAplicar" class="btn-primary" type="button">Aplicar</button>
    </div>
  </div>

  <!-- LISTA (DATA COMO SISTEMA ANTIGUO) -->
  <section class="lista-scroll">
    <section class="space-y-3" id="listaProcesos">
      <?php foreach ($procesos as $p): ?>
        <?php
        $obacPrefix = badgeFromObac($p['obac']);   // EP/FAP/MGP/CORP...
        $estadoUp   = strtoupper(trim($p['estado']));
        $hay = strtoupper(trim(
          $p['proceso'] . ' ' . $p['expediente'] . ' ' . $p['obac'] . ' ' . $p['descripcion'] . ' ' . $p['estado']
        ));
        ?>
        <div
          class="proc-item"
          data-open="<?= BASE_URL ?>/actividades?id=<?= (int)$p['id'] ?>"
          data-obac="<?= htmlspecialchars($obacPrefix) ?>"
          data-estado="<?= htmlspecialchars($estadoUp) ?>"
          data-hay="<?= htmlspecialchars($hay) ?>">
          <a class="proc-open" href="<?= BASE_URL ?>/actividades?id=<?= (int)$p['id'] ?>" aria-label="Abrir proceso"></a>

          <div class="left">
            <div class="badge badge-vino"><?= htmlspecialchars(badgeFromObac($p['obac'])) ?></div>

            <div class="info">
              <p class="title">
                <?= htmlspecialchars($p['proceso']) ?>
              </p>

              <p class="sub">
                <span class="sub-strong"><?= htmlspecialchars($p['obac']) ?></span>
              </p>

              <p class="desc">
                <?= htmlspecialchars($p['descripcion']) ?>
              </p>
            </div>
          </div>

          <div class="right">
            <div class="top-right">
              <span class="status <?= statusClass($p['estado']) ?>">
                <?= htmlspecialchars($p['estado']) ?>
              </span>

              <div class="actions">
                <button class="kebab" type="button" aria-label="Acciones" data-menu-btn></button>

                <div class="menu hidden" data-menu>
                  <a class="menu-item" href="<?= BASE_URL ?>/actividades?id=<?= (int)$p['id'] ?>">
                    <span class="mi">👁️</span> Ver
                  </a>

                  <a class="menu-item" href="<?= BASE_URL ?>/procesos/editar?id=<?= (int)$p['id'] ?>">
                    <span class="mi">✏️</span> Editar
                  </a>

                  <button class="menu-item danger" type="button"
                    data-delete
                    data-id="<?= (int)$p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['proceso']) ?>">
                    <span class="mi">🗑️</span> Eliminar
                  </button>
                </div>
              </div>
            </div>

            <p class="money"><?= fmt_money($p['estimado']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </section>

  <div class="mt-4 text-xs text-white/70">
    Mostrando un total de <?= count($procesos) ?> procesos
  </div>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  /* ===== CONTENEDOR DESKTOP ===== */
  .page-shell {
    width: 100%;
  }

  @media (min-width: 1024px) {
    .page-shell {
      max-width: 1120px;
      margin: 0 auto;
      padding-left: 24px;
      padding-right: 24px;
    }
  }

  /* ===== YEAR PILL ===== */
  .year-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border-radius: 9999px;
    background: rgba(107, 28, 38, .10);
    color: #6B1C26;
    border: 1px solid rgba(107, 28, 38, .15);
    font-weight: 700;
    font-size: .85rem;
    white-space: nowrap;
  }

  .year-pill .dot {
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background: #C9A227;
    box-shadow: 0 0 0 3px rgba(201, 162, 39, .20);
  }

  /* ===== CHIPS ===== */
  .chip {
    padding: 10px 14px;
    border-radius: 9999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: rgba(248, 250, 252, .85);
    color: #334155;
    font-weight: 700;
    font-size: .82rem;
    flex: 0 0 auto;
    transition: transform .12s ease;
  }

  .chip:active {
    transform: scale(.96);
  }

  .chip-active {
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
    border-color: rgba(107, 28, 38, .22);
  }

  .chip-soft {
    background: rgba(255, 255, 255, .92);
  }

  /* ===== SEARCH ===== */
  .search {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 1rem;
    background: rgba(248, 250, 252, .95);
    border: 1px solid rgba(148, 163, 184, .35);
  }

  .search-ico {
    font-size: 1rem;
    opacity: .75;
  }

  .search input {
    width: 100%;
    background: transparent;
    outline: none;
    border: none;
    color: #0f172a;
    font-weight: 700;
    font-size: 16px !important;
    -webkit-text-size-adjust: 100%;
  }

  .search input::placeholder {
    color: #94a3b8;
    font-weight: 700;
  }

  /* ===== LAYOUT: filtros fijo + lista scrollea ===== */
  .main-procesos {
    overflow: hidden;
  }

  .filtros-sticky {
    position: sticky;
    top: 0;
    z-index: 80;
    padding-top: 6px;
  }

  .filtros-sticky>div {
    background: rgba(255, 255, 255, .96) !important;
    border: 1px solid rgba(148, 163, 184, .25);
    box-shadow: 0 18px 40px rgba(0, 0, 0, .18);
    backdrop-filter: blur(12px);
  }

  /* SOLO la lista scrollea y NO se parte el último card */
  .lista-scroll {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;

    /* ocupa el alto disponible del main */
    height: calc(100vh - 290px);

    /* espacio real para el bottom-nav + safe area */
    padding-bottom: calc(140px + env(safe-area-inset-bottom));
  }

  /* Desktop: normalmente el bottom-nav no estorba tanto */
  @media (min-width: 1024px) {
    .lista-scroll {
      height: calc(100vh - 260px);
      padding-bottom: 40px;
    }
  }

  /* ============================= */
  /*          CARD PROCESO         */
  /* ============================= */

  .proc-item {
    position: relative;
    display: grid;
    grid-template-columns: 52px 1fr auto;
    gap: 14px;
    align-items: center;
    background: #fff;
    padding: 18px 18px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
  }

  .proc-open {
    position: absolute;
    inset: 0;
    z-index: 1;
    border-radius: 20px;
  }

  .proc-item>.left,
  .proc-item>.right {
    position: relative;
    z-index: 2;
  }

  /* Columna izquierda */
  .left {
    display: contents;
    /* usa las 3 columnas del grid del card */
  }

  /* Badge */
  .badge {
    width: 48px;
    height: 48px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: .85rem;
    background: rgba(107, 28, 38, .10);
    color: #6B1C26;
  }

  /* Info */
  .info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .title {
    font-weight: 900;
    font-size: 1rem;
    color: #0f172a;
    line-height: 1.15;
  }

  .dot-sep {
    opacity: .4;
    padding: 0 6px;
  }

  .sub {
    font-size: .8rem;
    font-weight: 800;
    color: #64748b;
  }

  .desc {
    font-size: .85rem;
    font-weight: 700;
    color: #334155;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  @media (max-width: 640px) {
    .desc {
      -webkit-line-clamp: 3;
    }
  }

  /* Columna derecha ordenada */
  .right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
    min-width: 140px;
  }

  .top-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Status */
  .status {
    font-weight: 900;
    font-size: .7rem;
    padding: 6px 12px;
    border-radius: 999px;
    white-space: nowrap;
  }

  .status-dorado {
    background: rgba(201, 162, 39, .15);
    color: #7A5B00;
  }

  .status-vino {
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
  }

  .status-gris {
    background: rgba(148, 163, 184, .15);
    color: #475569;
  }

  /* Monto */
  .money {
    font-weight: 900;
    font-size: 1rem;
    color: #0f172a;
    white-space: nowrap;
  }

  /* Acciones */
  .actions {
    position: relative;
    z-index: 3;
    /* encima del proc-open */
  }

  .kebab {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    cursor: pointer;
    display: grid;
    place-items: center;
    line-height: 1;
    padding: 0;
    color: #0f172a;
  }

  /* Kebab “seguro” (siempre visible) */
  .kebab::before {
    content: "⋯";
    font-weight: 900;
    font-size: 1.3rem;
    transform: translateY(-1px);
  }

  /* Menu */
  .menu {
    position: absolute;
    top: 42px;
    right: 0;
    width: 190px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, .25);
    overflow: hidden;
    z-index: 999;
  }

  .menu.hidden {
    display: none;
  }

  .menu-item {
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    font-size: .85rem;
    color: #0f172a;
    text-decoration: none;
    border: 0;
    background: transparent;
    width: 100%;
    text-align: left;
    cursor: pointer;
  }

  .menu-item:hover {
    background: #f1f5f9;
  }

  .menu-item .mi {
    width: 22px;
    display: inline-flex;
    justify-content: center;
  }

  .menu-item.danger {
    color: #b91c1c;
  }

  #listaProcesos {
    padding-bottom: 12px;
  }

  /* ===== Botón filtros compacto ===== */
  .btn-filtros {
    height: 48px;
    padding: 0 12px;
    border-radius: 14px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 900;
    color: #0f172a;
    white-space: nowrap;
  }

  .btn-filtros .txt {
    display: none;
  }

  .btn-filtros .ico {
    font-size: 1rem;
  }

  .badge-count {
    min-width: 22px;
    height: 22px;
    padding: 0 7px;
    border-radius: 999px;
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
    border: 1px solid rgba(107, 28, 38, .22);
    font-size: .75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* En pantallas algo más grandes, muestra texto */
  @media (min-width: 480px) {
    .btn-filtros .txt {
      display: inline;
    }
  }

  /* ===== Chips activos (resumen) ===== */
  .chip-x {
    padding: 8px 10px;
    border-radius: 9999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: rgba(255, 255, 255, .95);
    font-weight: 900;
    font-size: .78rem;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 0 0 auto;
  }

  .chip-x button {
    width: 20px;
    height: 20px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    font-weight: 900;
    line-height: 1;
  }

  /* ===== Overlay + Bottom Sheet ===== */
  .overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .45);
    z-index: 200;
  }

  .sheet {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 220;
    background: #fff;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    box-shadow: 0 -20px 50px rgba(0, 0, 0, .25);
    transform: translateY(0);
    max-height: calc(100vh - 80px);
    display: flex;
    flex-direction: column;
  }

  .sheet-handle {
    width: 54px;
    height: 5px;
    border-radius: 999px;
    background: rgba(148, 163, 184, .55);
    margin: 10px auto 6px;
  }

  .sheet-head {
    padding: 10px 14px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px solid rgba(148, 163, 184, .25);
  }

  .sheet-close {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    cursor: pointer;
  }

  /* Dibujar la X con líneas */
  .sheet-close::before,
  .sheet-close::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 18px;
    height: 2px;
    background: #0f172a;
    border-radius: 2px;
  }

  .sheet-close::before {
    transform: translate(-50%, -50%) rotate(45deg);
  }

  .sheet-close::after {
    transform: translate(-50%, -50%) rotate(-45deg);
  }

  .sheet-body {
    padding: 12px 14px 0;
    overflow: auto;
    -webkit-overflow-scrolling: touch;
  }

  .sheet-section {
    margin-bottom: 14px;
  }

  .sheet-label {
    font-weight: 900;
    color: #334155;
    font-size: .8rem;
    margin-bottom: 8px;
  }

  .chips-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .sheet-actions {
    padding: 12px 14px;
    display: flex;
    gap: 10px;
    border-top: 1px solid rgba(148, 163, 184, .25);
    padding-bottom: calc(12px + env(safe-area-inset-bottom));
  }

  .btn-primary,
  .btn-secondary {
    height: 46px;
    border-radius: 14px;
    font-weight: 900;
    flex: 1;
  }

  .btn-primary {
    background: rgba(107, 28, 38, .95);
    color: #fff;
  }

  .btn-secondary {
    background: #f1f5f9;
    border: 1px solid rgba(148, 163, 184, .35);
    color: #0f172a;
  }

  /* Desktop: en vez de sheet, deja el bloque siempre visible (opcional) */
  @media (min-width: 1024px) {

    .overlay,
    .sheet {
      display: none !important;
    }
  }

  .hidden {
    display: none !important;
  }
</style>

<script>
  // ===== MENÚ KEBAB (⋯) =====
  const closeAllMenus = () => {
    document.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-menu-btn]');
    const menu = e.target.closest('[data-menu]');

    // Click en el botón ⋯ => abre/cierra SOLO su menú
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

    // Click dentro del menú => no cierres
    if (menu) return;

    // Click fuera => cierra todos
    closeAllMenus();
  });

  // Eliminar con confirmación (si usas data-delete)
  document.addEventListener('click', (e) => {
    const del = e.target.closest('[data-delete]');
    if (!del) return;

    e.preventDefault();
    e.stopPropagation();

    const id = del.getAttribute('data-id');
    const name = del.getAttribute('data-name') || 'este proceso';

    if (!confirm(`¿Eliminar ${name}? Esta acción no se puede deshacer.`)) return;

    // Ajusta a tu ruta real
    window.location.href = `<?= BASE_URL ?>/procesos/eliminar?id=${id}`;
  });

  // ===== BUSCADOR (maqueta) =====
  const q = document.getElementById('q');
  q?.addEventListener('input', () => {
    // luego conectas a tu backend o filtrado real
  });

  // ===== SHEET FILTROS =====
  const btnFiltros = document.getElementById('btnFiltros');
  const overlay = document.getElementById('overlayFiltros');
  const sheet = document.getElementById('sheetFiltros');
  const btnCerrar = document.getElementById('btnCerrarSheet');
  const btnAplicar = document.getElementById('btnAplicar');
  const btnLimpiar = document.getElementById('btnLimpiar');

  const chipsActivos = document.getElementById('chipsActivos');
  const badgeCount = document.getElementById('badgeCount');

  // estado temporal (lo que el usuario toca en el sheet)
  let draft = {
    obac: 'ALL',
    estado: null
  };
  // estado aplicado (lo que realmente filtra)
  let applied = {
    obac: 'ALL',
    estado: null
  };

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

  btnFiltros?.addEventListener('click', openSheet);
  overlay?.addEventListener('click', closeSheet);
  btnCerrar?.addEventListener('click', closeSheet);

  // seleccionar chips dentro del sheet
  sheet?.addEventListener('click', (e) => {
    const b = e.target.closest('[data-filter]');
    if (!b) return;

    const filter = b.getAttribute('data-filter');
    const value = b.getAttribute('data-value');

    if (filter === 'obac') {
      draft.obac = value;
      // solo uno activo
      document.querySelectorAll('#fObac .chip').forEach(x => x.classList.remove('chip-active'));
      b.classList.add('chip-active');
    }

    if (filter === 'estado') {
      // toggle: si vuelves a tocar el mismo, lo desmarcas
      const isSame = draft.estado === value;
      draft.estado = isSame ? null : value;

      document.querySelectorAll('#fEstado .chip').forEach(x => x.classList.remove('chip-active'));
      if (!isSame) b.classList.add('chip-active');
    }
  });

  btnLimpiar?.addEventListener('click', () => {
    draft = {
      obac: 'ALL',
      estado: null
    };
    applied = {
      obac: 'ALL',
      estado: null
    };
    syncUIToDraft();
    renderActiveChips();
    // aquí conectarías tu filtrado real
    closeSheet();
  });

  btnAplicar?.addEventListener('click', () => {
    applied = {
      ...draft
    };
    renderActiveChips();
    // aquí conectarías tu filtrado real (backend o JS)
    closeSheet();
  });

  const syncUIToDraft = () => {
    // OBAC
    document.querySelectorAll('#fObac .chip').forEach(x => x.classList.remove('chip-active'));
    document.querySelectorAll(`#fObac .chip[data-value="${draft.obac}"]`).forEach(x => x.classList.add('chip-active'));

    // Estado
    document.querySelectorAll('#fEstado .chip').forEach(x => x.classList.remove('chip-active'));
    if (draft.estado) {
      document.querySelectorAll(`#fEstado .chip[data-value="${draft.estado}"]`).forEach(x => x.classList.add('chip-active'));
    }
  };

  const renderActiveChips = () => {
    const items = [];
    if (applied.obac && applied.obac !== 'ALL') items.push({
      k: 'obac',
      v: applied.obac,
      label: applied.obac
    });
    if (applied.estado) items.push({
      k: 'estado',
      v: applied.estado,
      label: applied.estado
    });

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
    const x = e.target.closest('.chip-x');
    const btn = e.target.closest('button');
    if (!x || !btn) return;

    const k = x.getAttribute('data-k');
    if (k === 'obac') applied.obac = 'ALL';
    if (k === 'estado') applied.estado = null;

    draft = {
      ...applied
    };
    renderActiveChips();
    // aquí conectarías tu filtrado real
  });

  const list = document.getElementById('listaProcesos');
  const countEl = document.querySelector('.mt-4.text-xs.text-white\\/70');

  const applyFilters = () => {
    const term = (q?.value || '').trim().toUpperCase();

    const cards = list ? Array.from(list.querySelectorAll('.proc-item')) : [];
    let visible = 0;

    cards.forEach(card => {
      const obac = (card.getAttribute('data-obac') || '').toUpperCase();
      const estado = (card.getAttribute('data-estado') || '').toUpperCase();
      const hay = (card.getAttribute('data-hay') || '').toUpperCase();

      const okSearch = !term || hay.includes(term);
      const okObac = !applied.obac || applied.obac === 'ALL' || obac === applied.obac;
      const okEstado = !applied.estado || estado === applied.estado;

      const show = okSearch && okObac && okEstado;
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    if (countEl) countEl.textContent = `Mostrando ${visible} de ${cards.length} procesos`;
  };

  // Buscar en tiempo real
  q?.addEventListener('input', applyFilters);

  // Aplicar / Limpiar también deben filtrar
  const _oldAplicar = btnAplicar?.onclick;
  btnAplicar?.addEventListener('click', () => {
    // ya estás seteando applied en tu handler actual
    applyFilters();
  });

  btnLimpiar?.addEventListener('click', () => {
    // ya estás reseteando draft/applied en tu handler actual
    if (q) q.value = '';
    applyFilters();
  });

  // Cuando quitas chip activo
  chipsActivos?.addEventListener('click', () => {
    // tu handler ya actualiza applied/draft
    applyFilters();
  });

  // Inicial
  applyFilters();

  // inicial
  renderActiveChips();
</script>