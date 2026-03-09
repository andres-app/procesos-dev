<?php
$titulo = 'Reportes | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';

$cards = [
  [
    'title' => 'Derivados',
    'desc'  => 'Contrataciones derivadas ACFFAA AF-2026',
    'icon'  => '📌',
    'href'  => BASE_URL . '/reportes/derivados',
    'tone'  => 'vino'
  ],
  [
    'title' => 'Procesos',
    'desc'  => 'Seguimiento general por estado y OBAC',
    'icon'  => '📊',
    'href'  => BASE_URL . '/reportes/procesos',
    'tone'  => 'azul'
  ],
  [
    'title' => 'Indicadores',
    'desc'  => 'KPIs, plazos, hitos, alertas',
    'icon'  => '⏱️',
    'href'  => BASE_URL . '/reportes/indicadores',
    'tone'  => 'verde'
  ],
  [
    'title' => 'Consolidado',
    'desc'  => 'Resumen global (S/, conteos, comparativos)',
    'icon'  => '🧾',
    'href'  => BASE_URL . '/reportes/consolidado',
    'tone'  => 'dorado'
  ],
];
?>

<main class="page page-shell flex-1 px-5 pt-4 overflow-y-auto">

  <section class="mb-5">
    <div class="bg-white/90 rounded-2xl p-4 shadow-lg border border-slate-200/40">
      <p class="text-sm text-slate-500">Panel de reportes</p>
      <h2 class="text-2xl font-black text-slate-900 mt-1">Reportes</h2>
      <p class="text-sm text-slate-600 mt-2">Elige un submódulo para ver el reporte.</p>
    </div>
  </section>

  <section class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-10">
    <?php foreach ($cards as $c): ?>
      <a href="<?= htmlspecialchars($c['href']) ?>" class="rep-card rep-<?= htmlspecialchars($c['tone']) ?>">
        <div class="rep-ico"><?= htmlspecialchars($c['icon']) ?></div>
        <div class="min-w-0">
          <p class="rep-title"><?= htmlspecialchars($c['title']) ?></p>
          <p class="rep-desc"><?= htmlspecialchars($c['desc']) ?></p>
        </div>
        <div class="rep-arrow" aria-hidden="true">›</div>
      </a>
    <?php endforeach; ?>
  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .page-shell { width: 100%; }
  @media (min-width: 1024px) {
    .page-shell { max-width: 1120px; margin: 0 auto; padding-left: 24px; padding-right: 24px; }
  }

  .rep-card{
    position:relative;
    display:flex;
    gap:14px;
    align-items:center;
    padding:18px;
    border-radius:22px;
    background:rgba(255,255,255,.96);
    border:1px solid rgba(148,163,184,.25);
    box-shadow:0 14px 28px rgba(0,0,0,.10);
    text-decoration:none;
    color:#0f172a;
    transition: transform .12s ease, box-shadow .12s ease;
  }
  .rep-card:active{ transform:scale(.985); }
  .rep-card:hover{ box-shadow:0 18px 40px rgba(0,0,0,.14); }

  .rep-ico{
    width:52px;height:52px;border-radius:999px;
    display:grid;place-items:center;
    font-size:1.4rem;
    background:rgba(15,47,90,.08);
    border:1px solid rgba(15,47,90,.10);
    flex:0 0 auto;
  }
  .rep-title{
    font-weight:900;
    font-size:1.05rem;
    line-height:1.1;
  }
  .rep-desc{
    font-weight:700;
    color:#64748b;
    font-size:.85rem;
    margin-top:4px;
    overflow:hidden;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
  }
  .rep-arrow{
    margin-left:auto;
    font-size:1.8rem;
    font-weight:900;
    color:rgba(15,23,42,.45);
  }

  /* Tonos */
  .rep-vino .rep-ico{ background:rgba(107,28,38,.10); border-color:rgba(107,28,38,.18); }
  .rep-verde .rep-ico{ background:rgba(22,163,74,.10); border-color:rgba(22,163,74,.18); }
  .rep-azul .rep-ico{ background:rgba(2,132,199,.10); border-color:rgba(2,132,199,.18); }
  .rep-dorado .rep-ico{ background:rgba(201,162,39,.14); border-color:rgba(201,162,39,.22); }
</style>