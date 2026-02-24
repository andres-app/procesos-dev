<?php
$titulo = 'Metas | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-4 overflow-y-auto">

  <!-- RESUMEN GENERAL -->
  <section class="mb-6">
    <div class="bg-white/90 text-primary rounded-3xl p-6 shadow-xl">
      <p class="text-sm text-slate-500 mb-1">Ahorro total en metas</p>
      <h2 class="text-3xl font-semibold">S/ 2,400.00</h2>
    </div>
  </section>

  <!-- BOTÓN NUEVA META -->
  <section class="mb-6">
    <button class="w-full bg-indigo-500/90 text-white py-3 rounded-xl font-medium shadow-md active:scale-95 transition">
      + Crear nueva meta
    </button>
  </section>

  <!-- LISTA DE METAS -->
  <section class="space-y-4">

    <!-- META 1 -->
    <div class="meta-card">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-lg">Viaje familiar</h3>
        <span class="text-sm text-slate-400">48%</span>
      </div>

      <p class="text-sm text-slate-500 mb-3">
        S/ 2,400 de S/ 5,000
      </p>

      <!-- PROGRESS BAR -->
      <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full bg-indigo-500 rounded-full" style="width:48%"></div>
      </div>
    </div>

    <!-- META 2 -->
    <div class="meta-card">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-lg">Fondo de emergencia</h3>
        <span class="text-sm text-slate-400">75%</span>
      </div>

      <p class="text-sm text-slate-500 mb-3">
        S/ 3,000 de S/ 4,000
      </p>

      <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full bg-emerald-500 rounded-full" style="width:75%"></div>
      </div>
    </div>

    <!-- META 3 -->
    <div class="meta-card">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-lg">Laptop nueva</h3>
        <span class="text-sm text-slate-400">20%</span>
      </div>

      <p class="text-sm text-slate-500 mb-3">
        S/ 800 de S/ 4,000
      </p>

      <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full bg-sky-500 rounded-full" style="width:20%"></div>
      </div>
    </div>

  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .meta-card {
    background: rgba(255,255,255,.95);
    color: #0F2F5A;
    padding: 18px;
    border-radius: 1.5rem;
    box-shadow: 0 10px 24px rgba(0,0,0,.12);
  }

  .meta-card:active {
    transform: scale(.98);
  }
</style>
