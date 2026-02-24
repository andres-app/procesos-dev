<?php
$titulo = 'Reportes | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-4 overflow-y-auto">

  <!-- RESUMEN -->
  <section class="mb-6 grid grid-cols-2 gap-4">
    <div class="bg-white/90 rounded-2xl p-4 shadow-lg text-primary">
      <p class="text-xs text-slate-500">Gastos mes</p>
      <h3 class="text-lg font-semibold mt-1">S/ 1,245</h3>
    </div>

    <div class="bg-white/90 rounded-2xl p-4 shadow-lg text-primary">
      <p class="text-xs text-slate-500">Ingresos mes</p>
      <h3 class="text-lg font-semibold mt-1">S/ 3,200</h3>
    </div>
  </section>

  <!-- BALANCE -->
  <section class="mb-6">
    <div class="bg-emerald-100/80 text-emerald-700 rounded-2xl p-5 shadow-lg">
      <p class="text-sm">Balance del mes</p>
      <h2 class="text-3xl font-semibold mt-1">+ S/ 1,955</h2>
    </div>
  </section>

  <!-- GRAFICO INGRESOS VS GASTOS -->
  <section class="mb-8">
    <div class="bg-white/90 rounded-3xl p-5 shadow-xl">
      <p class="text-sm text-slate-500 mb-3">Ingresos vs Gastos</p>
      <canvas id="lineChart" height="180"></canvas>
    </div>
  </section>


  <!-- INSIGHTS -->
  <section class="space-y-3 mb-6">

    <div class="reporte-item">
      <span>📉</span>
      <p class="flex-1">Gastaste menos que el mes pasado</p>
    </div>

    <div class="reporte-item">
      <span>📈</span>
      <p class="flex-1">Tus ingresos aumentaron 12%</p>
    </div>

    <div class="reporte-item">
      <span>🏆</span>
      <p class="flex-1">Buen control financiero este mes</p>
    </div>

  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // LINE CHART (Ingresos vs Gastos)
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
      datasets: [
        {
          label: 'Ingresos',
          data: [2800, 3000, 3100, 3200, 3200],
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,.15)',
          tension: .4,
          fill: true
        },
        {
          label: 'Gastos',
          data: [1500, 1400, 1300, 1250, 1245],
          borderColor: '#dc2626',
          backgroundColor: 'rgba(220,38,38,.12)',
          tension: .4,
          fill: true
        }
      ]
    },
    options: {
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: { grid: { display: false } },
        y: { grid: { display: false } }
      }
    }
  });
</script>

<style>
  .reporte-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,.95);
    color: #0F2F5A;
    padding: 16px;
    border-radius: 1.25rem;
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
    font-weight: 500;
  }

  .reporte-item span {
    font-size: 1.4rem;
  }
</style>
