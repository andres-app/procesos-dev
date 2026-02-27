<?php
$titulo = 'Inicio | Procesos';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';
require __DIR__ . '/../layout/header.php';
?>

<!-- ✅ INICIO (AGREGA EL MÓDULO PAC CON EL MISMO ESTILO "APPLE-CARD") -->
<main class="page flex-1 px-5 pt-[calc(env(safe-area-inset-top)+12px)] flex items-center justify-center">
  <div class="grid grid-cols-2 gap-5 w-full max-w-md mx-auto">

    <a href="<?= BASE_URL ?>/procesos"
       class="apple-card bg-sky-100/80 text-sky-800">
      <div class="icon">📂</div>
      <span>Procesos</span>
    </a>

    <a href="<?= BASE_URL ?>/pac"
       class="apple-card bg-indigo-100/80 text-indigo-800">
      <div class="icon">🗂️</div>
      <span>PAC</span>
    </a>

    <a href="<?= BASE_URL ?>/indicadores"
       class="apple-card bg-emerald-100/80 text-emerald-800">
      <div class="icon">📊</div>
      <span>Indicadores</span>
    </a>

    <a href="<?= BASE_URL ?>/reportes"
       class="apple-card bg-violet-100/80 text-violet-800">
      <div class="icon">📈</div>
      <span>Reportes</span>
    </a>

    <a href="<?= BASE_URL ?>/presupuesto"
       class="apple-card bg-teal-100/80 text-teal-800">
      <div class="icon">💳</div>
      <span>Presupuesto</span>
    </a>

    <a href="<?= BASE_URL ?>/alertas"
       class="apple-card bg-rose-100/80 text-rose-800">
      <div class="icon">🔔</div>
      <span>Alertas</span>
    </a>

  </div>
</main>
<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>


<style>
  /* ===== KPI CARDS ===== */
  .kpi-card {
    border-radius: 1.5rem;
    padding: 14px 14px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .12),
      inset 0 1px 0 rgba(255, 255, 255, .55);
    backdrop-filter: blur(10px);
  }

  .kpi-label {
    font-size: .78rem;
    color: #64748b;
    margin: 0;
  }

  .kpi-value {
    font-size: 1.8rem;
    font-weight: 700;
    letter-spacing: -.5px;
    margin-top: 6px;
    line-height: 1.1;
  }

  .kpi-foot {
    font-size: .72rem;
    color: #94a3b8;
    margin-top: 8px;
    margin-bottom: 0;
  }

  /* ===== APPLE CARDS (igual a tu estilo) ===== */
  .apple-card {
    aspect-ratio: 1 / 1;
    border-radius: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-align: center;
    backdrop-filter: blur(8px);
    box-shadow:
      0 10px 30px rgba(0, 0, 0, .12),
      inset 0 1px 0 rgba(255, 255, 255, .6);
    transition: transform .2s ease, box-shadow .2s ease;
    gap: 2px;
  }

  .apple-card .icon {
    font-size: 3.9rem;
    margin-bottom: .35rem;
  }

  .apple-card span {
    font-size: 1.05rem;
  }

  .apple-card small {
    font-weight: 500;
    font-size: .78rem;
    opacity: .85;
  }

  .apple-card:active {
    transform: scale(.96);
  }
</style>