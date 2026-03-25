<?php
/* Vista/modulos/pac.php */
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

$f = $_GET['f'] ?? 'acffaa';
?>

<main class="page page-shell main-pac">
  <div class="page-inner">
    <section class="filtros-sticky">
      <div class="filtros-wrap">
        <div class="head-row">
          <div class="head-copy">
            <p class="eyebrow">Programación y control</p>
            <h2 class="title-view">PAC</h2>
          </div>

          <div class="year-pill">
            <span class="dot"></span>
            <span>AF-2026</span>
          </div>
        </div>

        <div class="tools-row">
          <div class="search-box">
            <span class="search-ico">🔎</span>
            <input id="q" type="text" placeholder="Buscar N° PAC, OBAC o descripción..." />
          </div>

          <button id="btnFiltros" type="button" class="btn-filtros" aria-haspopup="dialog" aria-controls="sheetFiltros">
            <span class="ico">⚙️</span>
            <span class="txt">Filtros</span>
            <span id="badgeCount" class="badge-count hidden">0</span>
          </button>
        </div>

        <div class="top-chips">
          <a href="?f=acffaa" class="chip <?= $f === 'acffaa' ? 'chip-active' : '' ?>">ACFFAA</a>
          <a href="?f=inversiones" class="chip <?= $f === 'inversiones' ? 'chip-active' : '' ?>">Inversiones</a>
          <a href="?f=todos" class="chip <?= $f === 'todos' ? 'chip-active' : '' ?>">Todos</a>
        </div>

        <div id="chipsActivos" class="chips-activos hidden"></div>

        <p class="results-text" id="countText">
          Mostrando <?= count($pacs) ?> de <?= count($pacs) ?> PAC
        </p>
      </div>
    </section>

    <section class="lista-pac" id="listaProcesos">
      <?php foreach ($pacs as $p): ?>
        <?php
        $obacLabel = badgeFromObac($p['obac'] ?? '');
        $estado = strtoupper(trim((string)($p['estado'] ?? '')));
        $sel = selCode($p['seleccion_abrev'] ?? '');

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
        <article
          class="proc-item pac-item"
          data-obac="<?= htmlspecialchars($obacLabel, ENT_QUOTES, 'UTF-8') ?>"
          data-estado="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>"
          data-sel="<?= htmlspecialchars($sel, ENT_QUOTES, 'UTF-8') ?>"
          data-hay="<?= htmlspecialchars($haystack, ENT_QUOTES, 'UTF-8') ?>"
          data-open="javascript:void(0)">

          <a class="proc-open" href="javascript:void(0)" aria-label="Abrir PAC"></a>

          <div class="proc-badge">
            <?= htmlspecialchars($obacLabel, ENT_QUOTES, 'UTF-8') ?>
          </div>

          <div class="proc-info">
            <p class="proc-title">
              PAC N° <?= htmlspecialchars($p['nopac'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p class="proc-sub">
              <span class="sel-badge <?= selClass($sel) ?>">
                <?= htmlspecialchars($sel, ENT_QUOTES, 'UTF-8') ?>
              </span>
            </p>

            <p class="proc-desc">
              <?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>
          </div>

          <div class="proc-right">
            <span class="status <?= statusClass($p['estado'] ?? '') ?>">
              <?= htmlspecialchars($p['estado'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </span>

            <p class="money">
              <?= fmt_money($p['estimado'] ?? 0) ?>
            </p>
          </div>
        </article>
      <?php endforeach; ?>

      <?php if (empty($pacs)): ?>
        <article class="proc-item proc-empty">
          <div class="proc-info">
            <p class="proc-title">No hay PAC registrados</p>
            <p class="proc-desc">No se encontraron registros para mostrar.</p>
          </div>
        </article>
      <?php endif; ?>
    </section>
  </div>

  <div id="overlayFiltros" class="overlay hidden" aria-hidden="true"></div>

  <div id="sheetFiltros" class="sheet hidden" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
    <div class="sheet-handle" aria-hidden="true"></div>

    <div class="sheet-head">
      <div>
        <p class="sheet-mini">Filtra y encuentra rápido</p>
        <h3 id="sheetTitle" class="sheet-title">Filtros</h3>
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
</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .main-pac {
    position: relative;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    padding-top: calc(var(--sat) + var(--header-height) + 10px);
    padding-bottom: calc(var(--tabbar-total-space) + 20px);
  }

  .main-pac::-webkit-scrollbar {
    width: 0;
    height: 0;
  }

  .page-inner {
    width: 100%;
    max-width: 1120px;
    margin: 0 auto;
    padding: 0 16px 20px;
  }

  .filtros-sticky {
    position: sticky;
    top: 0;
    z-index: 12;
    padding-bottom: 14px;
    background: linear-gradient(to bottom, #F9FAFB 0%, #F9FAFB 78%, rgba(249,250,251,0) 100%);
  }

  .filtros-wrap {
    background: rgba(255, 255, 255, .96);
    border: 1px solid #E5E7EB;
    border-radius: 24px;
    box-shadow: 0 14px 34px rgba(17, 24, 39, .10);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 16px;
  }

  .head-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
  }

  .head-copy {
    min-width: 0;
  }

  .eyebrow {
    margin: 0;
    font-size: .82rem;
    font-weight: 700;
    color: #6B7280;
  }

  .title-view {
    margin: 4px 0 0;
    font-size: 1.7rem;
    line-height: 1.1;
    font-weight: 800;
    color: #111827;
    letter-spacing: -.03em;
  }

  .year-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border-radius: 999px;
    background: rgba(122, 12, 25, .08);
    border: 1px solid rgba(122, 12, 25, .12);
    color: #7A0C19;
    font-size: .84rem;
    font-weight: 800;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .year-pill .dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #C9A227;
    box-shadow: 0 0 0 3px rgba(201, 162, 39, .18);
  }

  .tools-row {
    margin-top: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .search-box {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    height: 48px;
    padding: 0 14px;
    border-radius: 16px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
  }

  .search-ico {
    opacity: .72;
    flex-shrink: 0;
  }

  .search-box input {
    width: 100%;
    border: 0;
    outline: 0;
    background: transparent;
    color: #111827;
    font-weight: 700;
  }

  .search-box input::placeholder {
    color: #9CA3AF;
    font-weight: 700;
  }

  .btn-filtros {
    height: 48px;
    padding: 0 14px;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    background: #fff;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #111827;
    font-weight: 800;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .btn-filtros .txt {
    display: none;
  }

  @media (min-width: 480px) {
    .btn-filtros .txt {
      display: inline;
    }
  }

  .badge-count {
    min-width: 22px;
    height: 22px;
    padding: 0 7px;
    border-radius: 999px;
    background: rgba(122, 12, 25, .10);
    color: #7A0C19;
    border: 1px solid rgba(122, 12, 25, .14);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .74rem;
    font-weight: 800;
  }

  .top-chips {
    margin-top: 12px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 2px;
  }

  .top-chips::-webkit-scrollbar,
  .chips-activos::-webkit-scrollbar {
    width: 0;
    height: 0;
  }

  .chip {
    padding: 10px 14px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: rgba(248, 250, 252, .9);
    color: #334155;
    font-weight: 800;
    font-size: .82rem;
    flex: 0 0 auto;
    transition: transform .12s ease;
  }

  .chip:active {
    transform: scale(.96);
  }

  .chip-active {
    background: rgba(122, 12, 25, .10);
    color: #7A0C19;
    border-color: rgba(122, 12, 25, .16);
  }

  .chip-soft {
    background: #fff;
  }

  .chips-activos {
    margin-top: 12px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 2px;
  }

  .chip-x {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #E5E7EB;
    color: #111827;
    font-size: .78rem;
    font-weight: 800;
  }

  .chip-x button {
    width: 20px;
    height: 20px;
    border: 1px solid #E5E7EB;
    border-radius: 999px;
    background: #F9FAFB;
    color: #111827;
    font-weight: 900;
    line-height: 1;
    cursor: pointer;
  }

  .results-text {
    margin: 12px 0 0;
    font-size: .78rem;
    font-weight: 700;
    color: #6B7280;
  }

  .lista-pac {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .proc-item {
    position: relative;
    display: grid;
    grid-template-columns: 52px minmax(0, 1fr) auto;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 22px;
    box-shadow: 0 10px 28px rgba(17, 24, 39, .08);
  }

  .proc-open {
    position: absolute;
    inset: 0;
    z-index: 1;
    border-radius: 22px;
  }

  .proc-badge,
  .proc-info,
  .proc-right {
    position: relative;
    z-index: 2;
  }

  .proc-badge {
    width: 48px;
    height: 48px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(122, 12, 25, .08);
    color: #7A0C19;
    font-size: .84rem;
    font-weight: 900;
    flex-shrink: 0;
  }

  .proc-info {
    min-width: 0;
  }

  .proc-title {
    margin: 0;
    font-size: 1rem;
    line-height: 1.18;
    font-weight: 800;
    color: #111827;
    overflow-wrap: anywhere;
  }

  .proc-sub {
    margin: 5px 0 0;
    font-size: .8rem;
    font-weight: 800;
    color: #6B7280;
  }

  .proc-desc {
    margin: 6px 0 0;
    font-size: .86rem;
    line-height: 1.32;
    font-weight: 700;
    color: #374151;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    overflow-wrap: anywhere;
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
    background: rgba(122, 12, 25, .16);
    color: #7A0C19;
  }

  .pill-slate {
    background: rgba(148, 163, 184, .20);
    color: #475569;
  }

  .proc-right {
    min-width: 132px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
  }

  .status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 900;
    white-space: nowrap;
  }

  .status-vino {
    background: rgba(122, 12, 25, .10);
    color: #7A0C19;
  }

  .status-gris {
    background: rgba(107, 114, 128, .12);
    color: #4B5563;
  }

  .money {
    margin: 0;
    font-size: 1rem;
    font-weight: 900;
    color: #111827;
    white-space: nowrap;
  }

  .proc-empty {
    grid-template-columns: 1fr;
  }

  .overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, .42);
    z-index: 200;
  }

  .sheet {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 220;
    background: #fff;
    border-top-left-radius: 22px;
    border-top-right-radius: 22px;
    box-shadow: 0 -20px 50px rgba(0, 0, 0, .22);
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
    border-bottom: 1px solid #E5E7EB;
  }

  .sheet-mini {
    margin: 0;
    font-size: .76rem;
    font-weight: 700;
    color: #6B7280;
  }

  .sheet-title {
    margin: 2px 0 0;
    font-size: 1.08rem;
    font-weight: 900;
    color: #111827;
  }

  .sheet-close {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 1px solid #E5E7EB;
    background: #F9FAFB;
    cursor: pointer;
    flex-shrink: 0;
  }

  .sheet-close::before,
  .sheet-close::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 18px;
    height: 2px;
    background: #111827;
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
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  .sheet-section {
    margin-bottom: 14px;
  }

  .sheet-label {
    margin: 0 0 8px;
    font-size: .8rem;
    font-weight: 900;
    color: #334155;
  }

  .chips-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .sheet-actions {
    padding: 12px 14px calc(12px + env(safe-area-inset-bottom));
    display: flex;
    gap: 10px;
    border-top: 1px solid #E5E7EB;
  }

  .btn-primary,
  .btn-secondary {
    height: 46px;
    border-radius: 14px;
    font-weight: 900;
    flex: 1;
    border: 0;
    cursor: pointer;
  }

  .btn-primary {
    background: #7A0C19;
    color: #fff;
  }

  .btn-secondary {
    background: #F3F4F6;
    border: 1px solid #E5E7EB;
    color: #111827;
  }

  .hidden {
    display: none !important;
  }

  @media (max-width: 720px) {
    .proc-item {
      grid-template-columns: 48px minmax(0, 1fr);
      align-items: flex-start;
    }

    .proc-right {
      grid-column: 2 / 3;
      min-width: 0;
      width: 100%;
      margin-top: 4px;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }

    .proc-desc {
      -webkit-line-clamp: 3;
    }
  }

  @media (min-width: 1024px) {
    .page-inner {
      padding-left: 24px;
      padding-right: 24px;
    }

    .sheet,
    .overlay {
      display: none !important;
    }
  }
</style>

<script>
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
    document.body.classList.add('lock-scroll');
  };

  const closeSheet = () => {
    overlay.classList.add('hidden');
    sheet.classList.add('hidden');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('lock-scroll');
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
      items.push({ k: 'obac', label: applied.obac });
    }

    if (applied.estado) {
      items.push({ k: 'estado', label: applied.estado });
    }

    if (applied.sel) {
      items.push({ k: 'sel', label: applied.sel });
    }

    if (items.length) {
      chipsActivos.classList.remove('hidden');
      badgeCount.classList.remove('hidden');
      badgeCount.textContent = String(items.length);
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

  btnAplicar?.addEventListener('click', () => {
    applied = { ...draft };
    renderActiveChips();
    applyFilters();
    closeSheet();
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

    if (q) q.value = '';

    syncUIToDraft();
    renderActiveChips();
    applyFilters();
    closeSheet();
  });

  chipsActivos?.addEventListener('click', (e) => {
    const wrap = e.target.closest('.chip-x');
    const btn = e.target.closest('button');
    if (!wrap || !btn) return;

    const k = wrap.getAttribute('data-k');

    if (k === 'obac') applied.obac = 'ALL';
    if (k === 'estado') applied.estado = null;
    if (k === 'sel') applied.sel = null;

    draft = { ...applied };
    syncUIToDraft();
    renderActiveChips();
    applyFilters();
  });

  q?.addEventListener('input', applyFilters);

  renderActiveChips();
  applyFilters();
</script>