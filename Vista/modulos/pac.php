<?php
$titulo = 'PAC | Procesos';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

$pacs = $pacs ?? [];

function fmt_money($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}

function selCode($sel)
{
  $s = strtoupper(trim((string)$sel));

  return match ($s) {
    '1', 'AS', 'ADJUDICACIÓN SIMPLIFICADA', 'ADJUDICACION SIMPLIFICADA' => 'AS',
    '2', 'CPRE', 'COMPARACIÓN DE PRECIOS', 'COMPARACION DE PRECIOS' => 'CPRE',
    '3', 'CP', 'CONCURSO PÚBLICO', 'CONCURSO PUBLICO' => 'CP',
    '4', 'CD', 'CONTRATACIÓN DIRECTA', 'CONTRATACION DIRECTA' => 'CD',
    '5', 'LP', 'LICITACIÓN PÚBLICA', 'LICITACION PUBLICA' => 'LP',
    '6', 'SIE', 'SUBASTA INVERSA ELECTRÓNICA', 'SUBASTA INVERSA ELECTRONICA' => 'SIE',
    '7', 'CATAL', 'COMPRAS POR CATÁLOGO (CONVENIO MARCO)' => 'CATAL',
    '8', 'CONV', 'CONVENIO' => 'CONV',
    '9', 'RES', 'RÉGIMEN ESPECIAL', 'REGIMEN ESPECIAL' => 'RES',
    '10', 'CE', 'CONTRATACIÓN INTERNACIONAL', 'CONTRATACION INTERNACIONAL' => 'CE',
    '11', 'CC', 'SUPUESTO DE INAPLICACIÓN MENOR O IGUAL A 8 UIT' => 'CC',
    '13', 'PEC', 'PROCEDIMIENTO ESPECIAL DE CONTRATACIÓN', 'PROCEDIMIENTO ESPECIAL DE CONTRATACION' => 'PEC',
    '15', 'EE', 'ENTRE ESTADOS' => 'EE',
    '16', 'SCI', 'SELECCIÓN DE CONSULTORES INDIVIDUALES', 'SELECCION DE CONSULTORES INDIVIDUALES' => 'SCI',
    '17', 'CPA', 'CONCURSO PÚBLICO ABREVIADO', 'CONCURSO PUBLICO ABREVIADO' => 'CPA',
    '18', 'LPA', 'LICITACIÓN PÚBLICA ABREVIADA HOMOLOGACIÓN', 'LICITACION PUBLICA ABREVIADA HOMOLOGACION' => 'LPA',
    '19', 'LPA', 'LICITACIÓN PÚBLICA ABREVIADA', 'LICITACION PUBLICA ABREVIADA' => 'LPA',
    '20', 'CPS', 'CONCURSO PÚBLICO DE SERVICIOS', 'CONCURSO PUBLICO DE SERVICIOS' => 'CPS',
    '21', 'CPC', 'CONCURSO PÚBLICO PARA CONSULTORÍA', 'CONCURSO PUBLICO PARA CONSULTORIA' => 'CPC',
    default => $s,
  };
}

function badgeFromObac($obac)
{
  $o = strtoupper(trim((string)$obac));

  return match ($o) {
    'EJERCITO DEL PERU', 'EJÉRCITO DEL PERÚ', 'EP' => 'EP',
    'FUERZA AEREA DEL PERU', 'FUERZA AÉREA DEL PERÚ', 'FAP' => 'FAP',
    'MARINA DE GUERRA DEL PERU', 'MARINA DE GUERRA DEL PERÚ', 'MGP' => 'MGP',
    'COMANDO CONJUNTO DE LAS FUERZAS ARMADAS', 'CCFFAA' => 'CCFFAA',
    'CONIDA' => 'CONIDA',
    default => $o,
  };
}

function statusClass($estado)
{
  $e = strtoupper(trim((string)$estado));
  return match ($e) {
    'PUBLICADO'  => 'status-gris',
    'SOLICITADO' => 'status-vino',
    default      => 'status-gris',
  };
}

function selClass($sel)
{
  $s = selCode($sel);

  return match ($s) {
    'LP'    => 'pill-amber',
    'SIE'   => 'pill-emerald',
    'RES'   => 'pill-vino',
    'CD'    => 'pill-vino',
    'CP'    => 'pill-slate',
    'CPRE'  => 'pill-slate',
    'CPA'   => 'pill-slate',
    'CPS'   => 'pill-slate',
    'CPC'   => 'pill-slate',
    'AS'    => 'pill-slate',
    'LPA'   => 'pill-slate',
    'SCI'   => 'pill-slate',
    'EE'    => 'pill-slate',
    'PEC'   => 'pill-slate',
    'CE'    => 'pill-slate',
    'CC'    => 'pill-slate',
    'CATAL' => 'pill-slate',
    'CONV'  => 'pill-slate',
    default => 'pill-slate',
  };
}
?>

<main class="page page-shell flex-1 px-5 pt-4 main-procesos">
  <section class="mb-5 filtros-sticky">
    <div class="bg-white/90 text-slate-900 rounded-2xl p-4 shadow-lg filtros-wrap">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm text-slate-500">Programación y control</p>
          <h2 class="text-2xl font-semibold mt-1">PAC</h2>
        </div>

        <div class="year-pill">
          <span class="dot"></span>
          <span>AF-2026</span>
        </div>
      </div>
      <div class="mt-4 flex items-center gap-2">

        <div class="search flex-1">
          <span class="search-ico">🔎</span>
          <input id="q" type="text" placeholder="Buscar N° PAC, OBAC o descripción..." />
        </div>

        <button id="btnFiltros"
          type="button"
          class="btn-filtros"
          aria-haspopup="dialog"
          aria-controls="sheetFiltros">
          <span class="ico">⚙️</span>
          <span class="txt">Filtros</span>
          <span id="badgeCount" class="badge-count hidden">0</span>
        </button>

      </div>

      <?php $f = $_GET['f'] ?? 'acffaa'; ?>

      <div class="mt-3 flex gap-2 overflow-x-auto pb-1">

        <a href="?f=acffaa"
          class="chip <?= $f === 'acffaa' ? 'chip-active' : '' ?>">
          ACFFAA
        </a>

        <a href="?f=inversiones"
          class="chip <?= $f === 'inversiones' ? 'chip-active' : '' ?>">
          Inversiones
        </a>

        <a href="?f=todos"
          class="chip <?= $f === 'todos' ? 'chip-active' : '' ?>">
          Todos
        </a>

      </div>

      <div id="chipsActivos" class="mt-3 flex gap-2 overflow-x-auto pb-1 hidden"></div>
    </div>
  </section>

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
          <button class="chip" type="button" data-filter="obac" data-value="CCFFAA">CCFFAA</button>
          <button class="chip" type="button" data-filter="obac" data-value="CONIDA">CONIDA</button>
        </div>
      </div>

      <div class="sheet-section">
        <p class="sheet-label">Estado</p>
        <div class="chips-grid" id="fEstado">
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="PUBLICADO">Publicado</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="SOLICITADO">Solicitado</button>
        </div>
      </div>

      <div class="sheet-section">
        <p class="sheet-label">Selección</p>
        <div class="chips-grid" id="fSel">
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="LP">LP</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="SIE">SIE</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="RES">RES</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="CD">CD</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="CPS">CPS</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="CPA">CPA</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="CP">CP</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="CC">CC</button>
        </div>
      </div>
    </div>

    <div class="sheet-actions">
      <button id="btnLimpiar" class="btn-secondary" type="button">Limpiar</button>
      <button id="btnAplicar" class="btn-primary" type="button">Aplicar</button>
    </div>
  </div>

  <section class="lista-scroll">
    <section class="space-y-3" id="listaProcesos">
      <?php foreach ($pacs as $p): ?>
        <?php
        $obacLabel = badgeFromObac($p['obac'] ?? '');
        $estado = strtoupper(trim((string)($p['estado'] ?? '')));
        $sel = strtoupper(trim((string)($p['seleccion_abrev'] ?? '')));

        $haystack = strtoupper(trim(
          ((string)($p['nopac'] ?? '')) . ' ' .
            ((string)($p['obac'] ?? '')) . ' ' .
            ((string)($obacLabel ?? '')) . ' ' .
            ((string)($p['seleccion_nombre'] ?? '')) . ' ' .
            ((string)($p['seleccion_abrev'] ?? '')) . ' ' .
            ((string)($p['estado'] ?? '')) . ' ' .
            ((string)($p['descripcion'] ?? ''))
        ));
        ?>
        <div
          class="proc-item pac-item"
          data-obac="<?= htmlspecialchars($obacLabel) ?>"
          data-estado="<?= htmlspecialchars($estado) ?>"
          data-sel="<?= htmlspecialchars($sel) ?>"
          data-hay="<?= htmlspecialchars($haystack) ?>"
          data-open="javascript:void(0)">

          <a class="proc-open" href="javascript:void(0)" aria-label="Abrir PAC"></a>

          <div class="left">
            <div class="badge badge-vino"><?= htmlspecialchars($obacLabel) ?></div>

            <div class="info">
              <p class="title">PAC N° <?= htmlspecialchars($p['nopac'] ?? '') ?></p>

              <p class="sub">
                <span class="sel-badge <?= selClass($sel) ?>">
                  <?= htmlspecialchars($sel) ?>
                </span>
              </p>

              <p class="desc">
                <?= htmlspecialchars($p['descripcion'] ?? '') ?>
              </p>
            </div>
          </div>

          <div class="right">
            <div class="top-right">
              <span class="status <?= statusClass($p['estado'] ?? '') ?>">
                <?= htmlspecialchars($p['estado'] ?? '') ?>
              </span>
            </div>

            <p class="money"><?= fmt_money($p['estimado'] ?? 0) ?></p>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (empty($pacs)): ?>
        <div class="proc-item">
          <div class="info">
            <p class="title">No hay PAC registrados</p>
            <p class="desc">No se encontraron registros para mostrar.</p>
          </div>
        </div>
      <?php endif; ?>
    </section>

    <div class="count-text" id="countText">
      Mostrando <?= count($pacs) ?> de <?= count($pacs) ?> PAC
    </div>
  </section>
</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  html,
  body {
    overflow-x: hidden;
  }

  /* ===== CONTENEDOR DESKTOP ===== */
  .page-shell {
    width: 100%;
    max-width: 100%;
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

  .pill-amber {
    background: rgba(201, 162, 39, .18);
    color: #7A5B00;
  }

  .pill-emerald {
    background: rgba(16, 185, 129, .18);
    color: #065f46;
  }

  .pill-vino {
    background: rgba(107, 28, 38, .18);
    color: #6B1C26;
  }

  .pill-slate {
    background: rgba(148, 163, 184, .20);
    color: #475569;
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
    min-width: 0;
  }

  .sel-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 900;
    letter-spacing: .3px;
    background: #eef2f7;
    color: #475569;
    white-space: nowrap;
  }

  .search-ico {
    font-size: 1rem;
    opacity: .75;
    flex: 0 0 auto;
  }

  .search input {
    width: 100%;
    min-width: 0;
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
    overflow-x: hidden;
    overflow-y: hidden;
    min-width: 0;
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

  :root {
    --tabbar-h: 76px;
    --tabbar-gap: 12px;
    --tabbar-side-gap: 14px;
  }

  /* SOLO la lista scrollea */
  .lista-scroll {
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior-x: none;
    height: calc(100dvh - 290px - (var(--tabbar-h) + var(--tabbar-gap) + env(safe-area-inset-bottom)));
    padding-bottom: 18px;
    min-width: 0;
    width: 100%;
    max-width: 100%;
  }

  /* Desktop */
  @media (min-width: 1024px) {
    .lista-scroll {
      height: calc(100dvh - 260px - 40px);
      padding-bottom: 12px;
      overflow-x: hidden;
    }
  }

  /* ============================= */
  /*          CARD PROCESO         */
  /* ============================= */

  #listaProcesos {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
    padding-bottom: 12px;
  }

  .proc-item {
    position: relative;
    display: grid;
    grid-template-columns: 52px minmax(0, 1fr) auto;
    gap: 14px;
    align-items: center;
    background: #fff;
    padding: 18px 18px;
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
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
    min-width: 0;
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
    flex: 0 0 auto;
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
    overflow-wrap: anywhere;
    word-break: break-word;
  }

  .dot-sep {
    opacity: .4;
    padding: 0 6px;
  }

  .sub {
    font-size: .8rem;
    font-weight: 800;
    color: #64748b;
    min-width: 0;
    overflow-wrap: anywhere;
    word-break: break-word;
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
    min-width: 0;
    overflow-wrap: anywhere;
    word-break: break-word;
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
    min-width: 0;
  }

  .top-right {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
  }

  /* Status */
  .status {
    font-weight: 900;
    font-size: .7rem;
    padding: 6px 12px;
    border-radius: 999px;
    white-space: nowrap;
    flex: 0 0 auto;
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
  }

  /* Kebab “seguro” (siempre visible) */
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
    flex: 0 0 auto;
  }

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
    flex: 0 0 auto;
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
    flex: 0 0 auto;
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
    flex: 0 0 auto;
  }

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

  /* Desktop */
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

  const q = document.getElementById('q');
  const list = document.getElementById('listaProcesos');
  const countText = document.getElementById('countText');

  const btnFiltros = document.getElementById('btnFiltros');
  const overlay = document.getElementById('overlayFiltros');
  const sheet = document.getElementById('sheetFiltros');
  const btnCerrar = document.getElementById('btnCerrarSheet');
  const btnAplicar = document.getElementById('btnAplicar');
  const btnLimpiar = document.getElementById('btnLimpiar');

  const chipsActivos = document.getElementById('chipsActivos');
  const badgeCount = document.getElementById('badgeCount');

  let draft = {
    obac: 'ALL',
    estado: null,
    sel: null
  };

  let applied = {
    obac: 'ALL',
    estado: null,
    sel: null
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

  sheet?.addEventListener('click', (e) => {
    const b = e.target.closest('[data-filter]');
    if (!b) return;

    const filter = b.getAttribute('data-filter');
    const value = b.getAttribute('data-value');

    if (filter === 'obac') {
      draft.obac = value;
      document.querySelectorAll('#fObac .chip').forEach(x => x.classList.remove('chip-active'));
      b.classList.add('chip-active');
    }

    if (filter === 'estado') {
      const isSame = draft.estado === value;
      draft.estado = isSame ? null : value;

      document.querySelectorAll('#fEstado .chip').forEach(x => x.classList.remove('chip-active'));
      if (!isSame) b.classList.add('chip-active');
    }

    if (filter === 'sel') {
      const isSame = draft.sel === value;
      draft.sel = isSame ? null : value;

      document.querySelectorAll('#fSel .chip').forEach(x => x.classList.remove('chip-active'));
      if (!isSame) b.classList.add('chip-active');
    }
  });

  btnLimpiar?.addEventListener('click', () => {
    draft = {
      obac: 'ALL',
      estado: null,
      sel: null
    };

    applied = {
      obac: 'ALL',
      estado: null,
      sel: null
    };

    syncUIToDraft();
    renderActiveChips();

    if (q) q.value = '';
    applyFilters();
    closeSheet();
  });

  btnAplicar?.addEventListener('click', () => {
    applied = {
      ...draft
    };
    renderActiveChips();
    applyFilters();
    closeSheet();
  });

  const syncUIToDraft = () => {
    document.querySelectorAll('#fObac .chip').forEach(x => x.classList.remove('chip-active'));
    document.querySelectorAll(`#fObac .chip[data-value="${draft.obac}"]`).forEach(x => x.classList.add('chip-active'));

    document.querySelectorAll('#fEstado .chip').forEach(x => x.classList.remove('chip-active'));
    if (draft.estado) {
      document.querySelectorAll(`#fEstado .chip[data-value="${draft.estado}"]`).forEach(x => x.classList.add('chip-active'));
    }

    document.querySelectorAll('#fSel .chip').forEach(x => x.classList.remove('chip-active'));
    if (draft.sel) {
      document.querySelectorAll(`#fSel .chip[data-value="${draft.sel}"]`).forEach(x => x.classList.add('chip-active'));
    }
  };

  const renderActiveChips = () => {
    const items = [];

    if (applied.obac && applied.obac !== 'ALL') {
      items.push({
        k: 'obac',
        label: applied.obac
      });
    }

    if (applied.estado) {
      items.push({
        k: 'estado',
        label: applied.estado
      });
    }

    if (applied.sel) {
      items.push({
        k: 'sel',
        label: applied.sel
      });
    }

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

  chipsActivos?.addEventListener('click', (e) => {
    const x = e.target.closest('.chip-x');
    const btn = e.target.closest('button');
    if (!x || !btn) return;

    const k = x.getAttribute('data-k');

    if (k === 'obac') applied.obac = 'ALL';
    if (k === 'estado') applied.estado = null;
    if (k === 'sel') applied.sel = null;

    draft = {
      ...applied
    };
    renderActiveChips();
    syncUIToDraft();
    applyFilters();
  });

  const applyFilters = () => {
    const term = (q?.value || '').trim().toUpperCase();

    const cards = list ? Array.from(list.querySelectorAll('.pac-item')) : [];
    let visible = 0;

    cards.forEach(card => {
      const obac = (card.getAttribute('data-obac') || '').toUpperCase();
      const estado = (card.getAttribute('data-estado') || '').toUpperCase();
      const sel = (card.getAttribute('data-sel') || '').toUpperCase();
      const hay = (card.getAttribute('data-hay') || '').toUpperCase();

      const okSearch = !term || hay.includes(term);
      const okObac = !applied.obac || applied.obac === 'ALL' || obac === applied.obac;
      const okEstado = !applied.estado || estado === applied.estado;
      const okSel = !applied.sel || sel === applied.sel;

      const show = okSearch && okObac && okEstado && okSel;
      card.style.display = show ? '' : 'none';

      if (show) visible++;
    });

    if (countText) {
      countText.textContent = `Mostrando ${visible} de ${cards.length} PAC`;
    }
  };

  q?.addEventListener('input', applyFilters);

  renderActiveChips();
  applyFilters();
</script>