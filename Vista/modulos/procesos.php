<?php
$titulo = 'Procesos | Procesos';
$appName = 'Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

/* =========================
   MAQUETA (DATA COMO SISTEMA ANTIGUO)
   Campos: proceso, expediente, OBAC, descripción, estado, estimado
   ========================= */
$procesos = [
  [
    'id' => 1,
    'proceso' => 'LPA-06-2025',
    'expediente' => '583',
    'obac' => 'FAP-0268',
    'descripcion' => 'ADQUISICIÓN DE 2 CAMIONETAS PARA LAS ALAS AÉREAS DE PROVINCIA (ALAR1 ALAR3 ALAR4 ALAR5) PP-0135 (ITEM 2)',
    'estado' => 'ADJUDICADO',
    'estimado' => 356800.00
  ],
  [
    'id' => 2,
    'proceso' => 'RES-36-2025',
    'expediente' => '610',
    'obac' => 'EP-0420',
    'descripcion' => 'ADQUISICIÓN DE EQUIPOS CONTRA INCENDIOS HELIBALDE PARA EL BAT N° 821-AE - AF 2026',
    'estado' => 'ADJUDICADO',
    'estimado' => 9031433.08
  ],
  [
    'id' => 3,
    'proceso' => 'CPC-08-2025',
    'expediente' => '609',
    'obac' => 'MGP-0338',
    'descripcion' => 'SUPERVISIÓN DE LA EJECUCIÓN DE LA OBRA 03 PARQUEADEROS DEL PROYECTO DE INVERSIÓN: AMPLIACIÓN Y MEJORAMIENTO DE LOS SERVICIOS DEL ASTILLERO DEL ARSENAL NAVAL DE LA MARINA DE GUERRA DEL PERÚ EN LA BASE NAVAL DEL CALLAO',
    'estado' => 'CONVOCADO',
    'estimado' => 2266776.72
  ],
  [
    'id' => 4,
    'proceso' => 'LP-09-2025',
    'expediente' => '606',
    'obac' => 'EP-0422',
    'descripcion' => 'ADQUISICIÓN DE VEHÍCULOS DE FLOTA LIVIANA AF-2026',
    'estado' => 'ADJUDICADO',
    'estimado' => 18213000.00
  ],
  [
    'id' => 5,
    'proceso' => 'RES-28-2025',
    'expediente' => '590',
    'obac' => 'EP-0408',
    'descripcion' => 'ADQUISICIÓN DE ACCESORIO Y MATERIALES PARA LOS COMBATIENTES DEL CE-VRAEM',
    'estado' => 'DESIERTO',
    'estimado' => 1370460.00
  ],
];

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

  <!-- HEADER / FILTROS -->
  <section class="mb-5 filtros-sticky">
    <div class="bg-white/90 text-slate-900 rounded-2xl p-5 shadow-lg">
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

      <!-- Chips OBAC -->
      <div class="mt-4 flex gap-2 overflow-x-auto pb-1">
        <button class="chip chip-active" type="button">Todos</button>
        <button class="chip" type="button">EP</button>
        <button class="chip" type="button">FAP</button>
        <button class="chip" type="button">MGP</button>
      </div>

      <!-- Chips Estado -->
      <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
        <button class="chip chip-soft" type="button">Convocado</button>
        <button class="chip chip-soft" type="button">Publicado</button>
        <button class="chip chip-soft" type="button">Adjudicado</button>
        <button class="chip chip-soft" type="button">Observado</button>
        <button class="chip chip-soft" type="button">Desierto</button>
      </div>

      <!-- Buscador -->
      <div class="mt-4">
        <div class="search">
          <span class="search-ico">🔎</span>
          <input id="q" type="text" placeholder="Buscar por Expediente, Proceso, OBAC o descripción..." />
        </div>
      </div>
    </div>
  </section>

  <!-- LISTA (DATA COMO SISTEMA ANTIGUO) -->
  <section class="lista-scroll">
    <section class="space-y-3" id="listaProcesos">
      <?php foreach ($procesos as $p): ?>
        <div class="proc-item" data-open="<?= BASE_URL ?>/actividades?id=<?= (int)$p['id'] ?>">
          <a class="proc-open" href="<?= BASE_URL ?>/actividades?id=<?= (int)$p['id'] ?>" aria-label="Abrir proceso"></a>

          <div class="left">
            <div class="badge badge-vino"><?= htmlspecialchars(badgeFromObac($p['obac'])) ?></div>

            <div class="info">
              <p class="title">
                <?= htmlspecialchars($p['proceso']) ?>
                <span class="dot-sep">·</span>
                Exp. <?= htmlspecialchars($p['expediente']) ?>
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

    <div class="mt-4 text-xs text-white/70">
      Mostrando un total de <?= count($procesos) ?> procesos
    </div>
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
    font-size: .95rem;
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
</script>