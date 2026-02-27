<?php
$titulo = 'PAC | Procesos';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

/* =========================
   DATA (PAC)
   Campos: nopac, estado, descripcion, obac, seleccion, estimado
   ========================= */
$pacs = [
  [
    'id' => 1,
    'nopac' => '0043',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE EMBARCACIONES MARÍTIMAS Y FLUVIALES /SERVICIO PP 0135/REQ.0009-2026-DIRECOMAR',
    'obac' => 'MGP',
    'seleccion' => 'CPA',
    'estimado' => 90000.0
  ],
  [
    'id' => 2,
    'nopac' => '0041',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE VEHÍCULOS /SERVICIO PP 0135/REQ.0007-2026-DIRECOMAR',
    'obac' => 'MGP',
    'seleccion' => 'CP',
    'estimado' => 570000.0
  ],
  [
    'id' => 3,
    'nopac' => '0040',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO OBLIGATORIO DE ACCIDENTES DE TRÁNSITO (SOAT) /SERVICIO PP 0135/REQ.0006-2026-DIRECOMAR',
    'obac' => 'MGP',
    'seleccion' => 'SIE',
    'estimado' => 44860.0
  ],
  [
    'id' => 4,
    'nopac' => '0028',
    'estado' => 'PUBLICADO',
    'descripcion' => 'SERVICIO DE MANTENIMIENTO Y REPARACIÓN DE VEHÍCULOS TÁCTICOS DE LA COMANDANCIA DE LA FUERZA DE INFANTERÍA DE MARINA /SERVICIO PP 0135/REQ.0008-2026-COMFUINMAR',
    'obac' => 'MGP',
    'seleccion' => 'CPS',
    'estimado' => 610000.0
  ],
  [
    'id' => 5,
    'nopac' => '0008',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICIÓN DE KITS DE 1500 HORAS PARA LOS DIÉSEL PROPULSORES Y DIÉSEL ALTERNADORES DE LAS UNIDADES DE LA FUERZA DE SUPERFICIE /BIEN PP0135/REQ.0007-2026-COMFAS',
    'obac' => 'MGP',
    'seleccion' => 'LP',
    'estimado' => 490000.0
  ],
  [
    'id' => 6,
    'nopac' => '0086',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICIÓN DE EXPLOSIVOS PARA EL SISTEMA DE EYECCIÓN MKPE-16LF KT-1P PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 5943990.63
  ],
  [
    'id' => 7,
    'nopac' => '0085',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICIÓN DE COMPONENTES PARA EL MANTENIMIENTO DEL SISTEMA DE EYECCIÓN MKPE-16LF KT-1P PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 5141785.0
  ],
  [
    'id' => 8,
    'nopac' => '0067',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICION DE KIT DE EQUIPAMIENTO PARA EXTINCION DE INCENDIOS APLICABLE A LOS HELICOPTEROS MI-17/171SH Y HELICOPTEROS BELL-212/412 PP-0135 CUI 2662544',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 5703825.12
  ],
  [
    'id' => 9,
    'nopac' => '0066',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE OVERHAUL DE MOTOR PT6A-34 APLICABLE A LA FLOTA DHC-6-400 PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 11333280.0
  ],
  [
    'id' => 10,
    'nopac' => '0062',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICION DE LUCES DE BALIZAMIENTO PORTATIL - EN EL GRUPO AEREO NUMERO 11 ... PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'LP',
    'estimado' => 3181162.0
  ],
  [
    'id' => 11,
    'nopac' => '0022',
    'estado' => 'PUBLICADO',
    'descripcion' => '0135 - SEGURO OBLIGATORIO DE ACCIDENTES DE TRANSITO - SOAT PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'CC',
    'estimado' => 29113.0
  ],
  [
    'id' => 12,
    'nopac' => '0005',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICIÓN DE EQUIPOS DE SEGURIDAD DE LAS COMUNICACIONES Y LICENCIA DE OPERACIÓN PARA EL SISTEMA CRIPTOLOGICO DE LA FAP PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 5240386.0
  ],
  [
    'id' => 13,
    'nopac' => '0004',
    'estado' => 'PUBLICADO',
    'descripcion' => 'ADQUISICION DE EQUIPAMIENTO DE COMUNICACIÓN SATELITAL TACTICO TIPO MANPACK PP-0135',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 1390640.0
  ],
  [
    'id' => 14,
    'nopac' => '0001',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE RASTREO SATELITAL UUDD FAP Y VRAEM PP-0135 PP-032',
    'obac' => 'FAP',
    'seleccion' => 'RES',
    'estimado' => 1230441.0
  ],
  [
    'id' => 15,
    'nopac' => '0136',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE EQUIPOS DE PARACAIDAS AF-2026',
    'obac' => 'EP',
    'seleccion' => 'RES',
    'estimado' => 5999999.99
  ],
  [
    'id' => 16,
    'nopac' => '0083',
    'estado' => 'SOLICITADO',
    'descripcion' => 'SERVICIO DE RENOVACIÓN DEL PROGRAMA PRO ADVANTAGE (POWER ADVANTAGE + PRO PARTS) PARA EL AVIÓN CESSNA CITATION XLS EP-861 DE LA AVIACIÓN DEL EJÉRCITO',
    'obac' => 'EP',
    'seleccion' => 'RES',
    'estimado' => 1275000.0
  ],
  [
    'id' => 17,
    'nopac' => '0076',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE CAMIONETAS PARA EL PROYECTO DE INVERSIÓN MI PERU',
    'obac' => 'EP',
    'seleccion' => 'RES',
    'estimado' => 5356144.0
  ],
  [
    'id' => 18,
    'nopac' => '0075',
    'estado' => 'SOLICITADO',
    'descripcion' => 'ADQUISICIÓN DE VEHÍCULOS DE APOYO DE COMBATE PARA EL PROYECTO DE INVERSIÓN PURISUNCHU',
    'obac' => 'EP',
    'seleccion' => 'RES',
    'estimado' => 14000000.0
  ],
  [
    'id' => 19,
    'nopac' => '0060',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE VEHÍCULOS E HIDROCARBUROS-135',
    'obac' => 'EP',
    'seleccion' => 'CPS',
    'estimado' => 1300000.0
  ],
  [
    'id' => 20,
    'nopac' => '0059',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO DE EMBARCACIONES FLUVIALES -PP 135',
    'obac' => 'EP',
    'seleccion' => 'CPS',
    'estimado' => 849963.0
  ],
  [
    'id' => 21,
    'nopac' => '0057',
    'estado' => 'PUBLICADO',
    'descripcion' => 'CONTRATACIÓN CORPORATIVA DEL SEGURO OBLIGATORIO DE ACCIDENTES DE TRÁNSITO (SOAT)-PP 135-',
    'obac' => 'EP',
    'seleccion' => 'SIE',
    'estimado' => 120867.0
  ],
  [
    'id' => 22,
    'nopac' => '0001',
    'estado' => 'PUBLICADO',
    'descripcion' => 'SERVICIO DE SOPORTE TECNICO PARA SISTEMA SATELITAL',
    'obac' => 'CONIDA',
    'seleccion' => 'RES',
    'estimado' => 8200000.0
  ],
];

function fmt_money($n)
{
  return 'S/ ' . number_format((float)$n, 2, '.', ',');
}
function badgeFromObac($obac)
{
  return strtoupper(trim($obac));
}

function statusClass($estado)
{
  $e = strtoupper(trim($estado));
  return match ($e) {
    'PUBLICADO'  => 'status-gris',
    'SOLICITADO' => 'status-vino',
    default      => 'status-gris',
  };
}

function selClass($sel)
{
  $s = strtoupper(trim($sel));
  return match ($s) {
    'LP'  => 'pill-amber',
    'SIE' => 'pill-emerald',
    'RES' => 'pill-vino',
    'CPS' => 'pill-slate',
    'CPA' => 'pill-slate',
    'CP'  => 'pill-slate',
    'CC'  => 'pill-slate',
    default => 'pill-slate',
  };
}
?>

<main class="page page-shell flex-1 px-5 pt-4 main-procesos">

  <!-- HEADER / FILTROS (COMPACTO + SHEET) -->
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

      <!-- BAR COMPACTA -->
      <div class="mt-4 flex items-center gap-2">
        <div class="search flex-1">
          <span class="search-ico">🔎</span>
          <input id="q" type="text" placeholder="Buscar N° PAC, OBAC o descripción..." />
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
      <!-- OBAC -->
      <div class="sheet-section" id="fObac">
        <p class="sheet-label">OBAC</p>
        <div class="chips-grid">
          <button class="chip chip-active" type="button" data-filter="obac" data-value="">Todos</button>
          <button class="chip" type="button" data-filter="obac" data-value="EP">EP</button>
          <button class="chip" type="button" data-filter="obac" data-value="FAP">FAP</button>
          <button class="chip" type="button" data-filter="obac" data-value="MGP">MGP</button>
          <button class="chip" type="button" data-filter="obac" data-value="CONIDA">CONIDA</button>
        </div>
      </div>

      <!-- ESTADO -->
      <div class="sheet-section" id="fEstado">
        <p class="sheet-label">Estado</p>
        <div class="chips-grid">
          <button class="chip chip-soft chip-active" type="button" data-filter="estado" data-value="">Todos</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="PUBLICADO">Publicado</button>
          <button class="chip chip-soft" type="button" data-filter="estado" data-value="SOLICITADO">Solicitado</button>
        </div>
      </div>

      <!-- SELECCIÓN -->
      <div class="sheet-section" id="fSel">
        <p class="sheet-label">Selección</p>
        <div class="chips-grid">
          <button class="chip chip-soft chip-active" type="button" data-filter="sel" data-value="">Todos</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="RES">RES</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="LP">LP</button>
          <button class="chip chip-soft" type="button" data-filter="sel" data-value="SIE">SIE</button>
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

  <!-- LISTA -->
  <section class="lista-scroll">
    <section class="space-y-3" id="listaProcesos">
      <?php foreach ($pacs as $p): ?>
        <?php
        $obac = strtoupper(trim($p['obac']));
        $estado = strtoupper(trim($p['estado']));
        $sel = strtoupper(trim($p['seleccion']));
        $haystack = strtoupper(trim(
          $p['nopac'] . ' ' . $p['obac'] . ' ' . $p['seleccion'] . ' ' . $p['estado'] . ' ' . $p['descripcion']
        ));
        ?>
        <div class="proc-item pac-item"
          data-obac="<?= htmlspecialchars($obac) ?>"
          data-estado="<?= htmlspecialchars($estado) ?>"
          data-sel="<?= htmlspecialchars($sel) ?>"
          data-hay="<?= htmlspecialchars($haystack) ?>"
          data-open="<?= BASE_URL ?>/pac/ver?id=<?= (int)$p['id'] ?>">
          <a class="proc-open" href="<?= BASE_URL ?>/pac/ver?id=<?= (int)$p['id'] ?>" aria-label="Abrir PAC"></a>

          <div class="left">
            <div class="badge badge-vino"><?= htmlspecialchars(badgeFromObac($p['obac'])) ?></div>

            <div class="info">
              <p class="title">
                PAC N° <?= htmlspecialchars($p['nopac']) ?>
              </p>

              <p class="sub">
                <span class="sel-pill <?= selClass($p['seleccion']) ?>"><?= htmlspecialchars($p['seleccion']) ?></span>
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
                  <a class="menu-item" href="<?= BASE_URL ?>/pac/ver?id=<?= (int)$p['id'] ?>">
                    <span class="mi">👁️</span> Ver
                  </a>

                  <a class="menu-item" href="<?= BASE_URL ?>/pac/editar?id=<?= (int)$p['id'] ?>">
                    <span class="mi">✏️</span> Editar
                  </a>

                  <button class="menu-item danger" type="button"
                    data-delete
                    data-id="<?= (int)$p['id'] ?>"
                    data-name="PAC <?= htmlspecialchars($p['nopac']) ?>">
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

    <div class="mt-4 text-xs text-white/70" id="countText">
      Mostrando un total de <?= count($pacs) ?> PAC
    </div>
  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  /* ===== CONTENEDOR DESKTOP (IGUAL QUE PROCESOS) ===== */
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

  /* ===== YEAR PILL (IGUAL) ===== */
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

  /* ===== CHIPS (IGUAL) ===== */
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

  /* ===== SEARCH (IGUAL) ===== */
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

  /* ===== LAYOUT (IGUAL) ===== */
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

  .lista-scroll {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    height: calc(100vh - 335px);
    padding-bottom: calc(140px + env(safe-area-inset-bottom));
  }

  @media (min-width:1024px) {
    .lista-scroll {
      height: calc(100vh - 300px);
      padding-bottom: 40px;
    }
  }

  /* ===== CARD (REUSAMOS PROC-ITEM TAL CUAL) ===== */
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

  .left {
    display: contents;
  }

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
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
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

  @media (max-width:640px) {
    .desc {
      -webkit-line-clamp: 3;
    }
  }

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

  .status {
    font-weight: 900;
    font-size: .7rem;
    padding: 6px 12px;
    border-radius: 999px;
    white-space: nowrap;
  }

  .status-vino {
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
  }

  .status-gris {
    background: rgba(148, 163, 184, .15);
    color: #475569;
  }

  .money {
    font-weight: 900;
    font-size: 1rem;
    color: #0f172a;
    white-space: nowrap;
  }

  /* Selección pill */
  .sel-pill {
    padding: 5px 10px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 900;
    border: 1px solid rgba(148, 163, 184, .25);
    white-space: nowrap;
  }

  .pill-vino {
    background: rgba(107, 28, 38, .10);
    color: #6B1C26;
  }

  .pill-amber {
    background: rgba(245, 158, 11, .14);
    color: #7A4A00;
  }

  .pill-emerald {
    background: rgba(16, 185, 129, .14);
    color: #065f46;
  }

  .pill-slate {
    background: rgba(148, 163, 184, .14);
    color: #334155;
  }

  /* Acciones (IGUAL) */
  .actions {
    position: relative;
    z-index: 3;
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

  .kebab::before {
    content: "⋯";
    font-weight: 900;
    font-size: 1.3rem;
    transform: translateY(-1px);
  }

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
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
    border: 1px solid rgba(107, 28, 38, .22);
    font-size: .75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
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

  /* Botón cerrar con X dibujada (consistente) */
  .sheet-close {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .35);
    background: #f8fafc;
    cursor: pointer;
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

  .hidden {
    display: none !important;
  }

  /* Desktop: no mostrar sheet */
  @media (min-width: 1024px) {

    .overlay,
    .sheet {
      display: none !important;
    }
  }

  /* iOS: evitar zoom al enfocar input (font-size >= 16px) */
  @media (max-width: 1024px) {
    .search input {
      font-size: 16px !important;
    }
  }

  .search input {
    -webkit-text-size-adjust: 100%;
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

  // Eliminar
  document.addEventListener('click', (e) => {
    const del = e.target.closest('[data-delete]');
    if (!del) return;

    e.preventDefault();
    e.stopPropagation();

    const id = del.getAttribute('data-id');
    const name = del.getAttribute('data-name') || 'este registro';

    if (!confirm(`¿Eliminar ${name}? Esta acción no se puede deshacer.`)) return;
    window.location.href = `<?= BASE_URL ?>/pac/eliminar?id=${id}`;
  });

  // ===== FILTROS + BUSCADOR =====
  const q = document.getElementById('q');
  const list = document.getElementById('listaProcesos');
  const countText = document.getElementById('countText');

  // estado aplicado
  const state = {
    obac: '',
    estado: '',
    sel: '',
    q: ''
  };

  const applyFilters = () => {
    const cards = list ? Array.from(list.querySelectorAll('.pac-item')) : [];
    let visible = 0;

    const term = (state.q || '').trim().toUpperCase();

    cards.forEach(c => {
      const obac = (c.getAttribute('data-obac') || '').toUpperCase();
      const estado = (c.getAttribute('data-estado') || '').toUpperCase();
      const sel = (c.getAttribute('data-sel') || '').toUpperCase();
      const hay = (c.getAttribute('data-hay') || '').toUpperCase();

      const okObac = !state.obac || obac === state.obac;
      const okEstado = !state.estado || estado === state.estado;
      const okSel = !state.sel || sel === state.sel;
      const okQ = !term || hay.includes(term);

      const ok = okObac && okEstado && okSel && okQ;

      c.style.display = ok ? '' : 'none';
      if (ok) visible++;
    });

    if (countText) countText.textContent = `Mostrando ${visible} de ${cards.length} PAC`;
  };

  // ===== SHEET =====
  const btnFiltros = document.getElementById('btnFiltros');
  const overlay = document.getElementById('overlayFiltros');
  const sheet = document.getElementById('sheetFiltros');
  const btnCerrar = document.getElementById('btnCerrarSheet');
  const btnAplicar = document.getElementById('btnAplicar');
  const btnLimpiar = document.getElementById('btnLimpiar');

  const chipsActivos = document.getElementById('chipsActivos');
  const badgeCount = document.getElementById('badgeCount');

  // estado temporal (draft) y aplicado (applied)
  let draft = {
    obac: '',
    estado: '',
    sel: ''
  };
  let applied = {
    obac: '',
    estado: '',
    sel: ''
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

  const esc = (v) => (window.CSS && CSS.escape) ? CSS.escape(v) : (v || '').replace(/"/g, '\\"');

  const setActiveInGroup = (groupId, value) => {
    const group = document.getElementById(groupId);
    if (!group) return;

    group.querySelectorAll('button[data-filter]').forEach(b => b.classList.remove('chip-active'));

    const btn = group.querySelector(`button[data-value="${esc(value)}"]`);
    if (btn) btn.classList.add('chip-active');
  };

  const syncUIToDraft = () => {
    setActiveInGroup('fObac', draft.obac);
    setActiveInGroup('fEstado', draft.estado);
    setActiveInGroup('fSel', draft.sel);
  };

  const renderActiveChips = () => {
    const items = [];
    if (applied.obac) items.push({
      k: 'obac',
      label: `OBAC: ${applied.obac}`
    });
    if (applied.estado) items.push({
      k: 'estado',
      label: `Estado: ${applied.estado}`
    });
    if (applied.sel) items.push({
      k: 'sel',
      label: `Selección: ${applied.sel}`
    });

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

  // Abrir/cerrar
  btnFiltros?.addEventListener('click', openSheet);
  overlay?.addEventListener('click', closeSheet);
  btnCerrar?.addEventListener('click', closeSheet);

  // Click en chips del sheet
  sheet?.addEventListener('click', (e) => {
    const b = e.target.closest('button[data-filter]');
    if (!b) return;

    const filter = b.getAttribute('data-filter');
    const value = (b.getAttribute('data-value') || '').toUpperCase();

    if (filter === 'obac') draft.obac = value;
    if (filter === 'estado') draft.estado = value;
    if (filter === 'sel') draft.sel = value;

    syncUIToDraft();
  });

  // Limpiar
  btnLimpiar?.addEventListener('click', () => {
    draft = {
      obac: '',
      estado: '',
      sel: ''
    };
    applied = {
      obac: '',
      estado: '',
      sel: ''
    };

    state.obac = '';
    state.estado = '';
    state.sel = '';

    if (q) q.value = '';
    state.q = '';

    syncUIToDraft();
    renderActiveChips();
    applyFilters();
    closeSheet();
  });

  // Aplicar
  btnAplicar?.addEventListener('click', () => {
    applied = {
      ...draft
    };

    state.obac = applied.obac;
    state.estado = applied.estado;
    state.sel = applied.sel;

    renderActiveChips();
    applyFilters();
    closeSheet();
  });

  // Quitar chip desde resumen
  chipsActivos?.addEventListener('click', (e) => {
    const wrap = e.target.closest('.chip-x');
    const btn = e.target.closest('button');
    if (!wrap || !btn) return;

    const k = wrap.getAttribute('data-k');

    applied[k] = '';
    draft = {
      ...applied
    };
    state[k] = '';

    syncUIToDraft();
    renderActiveChips();
    applyFilters();
  });

  // Buscador
  q?.addEventListener('input', (e) => {
    state.q = (e.target.value || '').trim();
    applyFilters();
  });

  // Inicial
  renderActiveChips();
  applyFilters();
</script>