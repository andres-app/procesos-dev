<?php
/* Vista/modulos/dashboard.php */
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
    bottom: calc(78px + var(--sab) + 16px);
    overflow: auto;
    display: flex;
    flex-direction: column;
    z-index: 1;
    background: #F9FAFB;
    padding: 14px calc(var(--sar) + 20px) 10px calc(var(--sal) + 20px);
    -webkit-overflow-scrolling: touch;
  }

  .dashboard-grid {
    width: 100%;
    max-width: 430px;
    min-height: 100%;
    margin: 0 auto;

    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-auto-rows: 1fr;
    gap: 14px;

    flex: 1 1 auto;
  }

  .apple-card {
    height: 100%;
    min-height: 150px;
    border-radius: 24px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;

    text-align: center;
    text-decoration: none;

    position: relative;
    overflow: hidden;

    background: #FFFFFF;
    border: 1px solid #E5E7EB;

    box-shadow: 0 10px 25px rgba(0, 0, 0, .08);

    transition: transform .12s ease, box-shadow .12s ease;
    -webkit-tap-highlight-color: transparent;
  }

  .apple-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg,
        rgba(255, 255, 255, .16),
        rgba(255, 255, 255, 0) 48%);
    pointer-events: none;
  }

  .apple-card:active {
    transform: scale(.97);
    box-shadow: 0 6px 14px rgba(0, 0, 0, .08);
  }

  .apple-card .icon {
    font-size: 2.7rem;
    line-height: 1;
    filter: drop-shadow(0 6px 10px rgba(0, 0, 0, .12));
  }

  .apple-card span {
    font-size: 1rem;
    font-weight: 700;
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
    background: linear-gradient(180deg, #7A0C19, #5A0712);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, .16);
  }

  @media (max-width: 390px) {
    .dashboard-main {
      padding: 12px calc(var(--sar) + 16px) 8px calc(var(--sal) + 16px);
    }

    .dashboard-grid {
      gap: 12px;
    }

    .apple-card {
      min-height: 138px;
      border-radius: 22px;
    }

    .apple-card .icon {
      font-size: 2.45rem;
    }

    .apple-card span {
      font-size: .92rem;
    }
  }
</style>