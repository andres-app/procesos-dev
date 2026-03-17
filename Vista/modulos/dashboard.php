<?php
$titulo = 'Inicio | Procesos';
$appName = 'Seguimiento de Procesos';
$usuario = 'Andres';
require __DIR__ . '/../layout/header.php';
?>

<main class="page dashboard-main">
  <section class="dashboard-grid">
    <a href="<?= BASE_URL ?>/procesos" class="apple-card card-procesos">
      <div class="icon">📂</div>
      <span>Procesos</span>
    </a>

    <a href="<?= BASE_URL ?>/pac" class="apple-card card-pac">
      <div class="icon">🗂️</div>
      <span>PAC</span>
    </a>

    <a href="<?= BASE_URL ?>/indicadores" class="apple-card card-indicadores">
      <div class="icon">📊</div>
      <span>Indicadores</span>
    </a>

    <a href="<?= BASE_URL ?>/reportes" class="apple-card card-reportes">
      <div class="icon">📈</div>
      <span>Reportes</span>
    </a>

    <a href="<?= BASE_URL ?>/presupuesto" class="apple-card card-presupuesto">
      <div class="icon">💳</div>
      <span>Presupuesto</span>
    </a>

    <a href="<?= BASE_URL ?>/alertas" class="apple-card card-alertas">
      <div class="icon">🔔</div>
      <span>Alertas</span>
    </a>
  </section>
</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .dashboard-main {
    position: absolute;
    top: calc(var(--sat) + 86px);
    left: 0;
    right: 0;
    bottom: calc(var(--tabbar-height) + var(--tabbar-offset) + var(--sab) + 10px);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding:
      8px calc(var(--sar) + 20px) 8px calc(var(--sal) + 20px);
    z-index: 1;
    background: transparent;
  }

  .dashboard-grid {
    width: 100%;
    max-width: 430px;
    margin: 0 auto;

    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;

    align-content: center;
    justify-content: center;
  }

  .apple-card {
    min-height: 158px;
    border-radius: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-align: center;
    text-decoration: none;
    position: relative;
    overflow: hidden;

    border: 1px solid rgba(255, 255, 255, .22);
    box-shadow:
      0 16px 34px rgba(0, 0, 0, .18),
      inset 0 1px 0 rgba(255, 255, 255, .36);

    backdrop-filter: blur(10px) saturate(130%);
    -webkit-backdrop-filter: blur(10px) saturate(130%);

    transition: transform .14s ease, box-shadow .14s ease;
    -webkit-tap-highlight-color: transparent;
  }

  .apple-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(255, 255, 255, .16), rgba(255, 255, 255, 0) 48%);
    pointer-events: none;
  }

  .apple-card:active {
    transform: scale(.965);
  }

  .apple-card .icon {
    font-size: 3.55rem;
    line-height: 1;
    filter: drop-shadow(0 6px 10px rgba(0, 0, 0, .12));
  }

  .apple-card span {
    font-size: .98rem;
    font-weight: 800;
    letter-spacing: -.2px;
    line-height: 1.1;
  }

  .card-procesos {
    background: linear-gradient(180deg, rgba(235, 238, 252, .98), rgba(220, 224, 245, .94));
    color: #2563eb;
  }

  .card-pac {
    background: linear-gradient(180deg, rgba(245, 241, 244, .98), rgba(235, 229, 233, .94));
    color: #4b5563;
  }

  .card-indicadores {
    background: linear-gradient(180deg, rgba(213, 239, 221, .98), rgba(188, 227, 201, .94));
    color: #0f8a6c;
  }

  .card-reportes {
    background: linear-gradient(180deg, rgba(235, 232, 250, .98), rgba(219, 214, 245, .94));
    color: #4f46e5;
  }

  .card-presupuesto {
    background: linear-gradient(180deg, rgba(245, 231, 190, .98), rgba(240, 220, 160, .94));
    color: #c77700;
  }

  .card-alertas {
    background: linear-gradient(180deg, rgba(158, 34, 52, .94), rgba(135, 8, 28, .96));
    color: #ff4d73;
    border: 1px solid rgba(255, 255, 255, .16);
  }

  @media (max-width: 390px) {
    .dashboard-main {
      padding-left: calc(var(--sal) + 16px);
      padding-right: calc(var(--sar) + 16px);
    }

    .dashboard-grid {
      gap: 14px;
    }

    .apple-card {
      min-height: 146px;
      border-radius: 26px;
    }

    .apple-card .icon {
      font-size: 3rem;
    }

    .apple-card span {
      font-size: .93rem;
    }
  }
</style>