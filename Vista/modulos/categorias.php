<?php
$titulo = 'Categorías | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-4 overflow-y-auto">

  <!-- RESUMEN -->
  <section class="mb-6">
    <div class="bg-white/90 text-primary rounded-3xl p-6 shadow-xl">
      <p class="text-sm text-slate-500">Categorías registradas</p>
      <h2 class="text-2xl font-semibold mt-1">6 categorías activas</h2>
    </div>
  </section>

  <!-- BOTÓN NUEVA CATEGORÍA -->
  <section class="mb-5">
    <button class="w-full bg-slate-800/90 text-white py-3 rounded-xl font-medium shadow-md active:scale-95 transition">
      + Nueva categoría
    </button>
  </section>

  <!-- LISTA DE CATEGORÍAS -->
  <section class="space-y-3">

    <div class="categoria-item">
      <div class="icon bg-emerald-100 text-emerald-600">🍔</div>
      <div class="flex-1">
        <p class="font-medium">Alimentación</p>
        <p class="text-xs text-slate-400">Gastos</p>
      </div>
      <span class="arrow">›</span>
    </div>

    <div class="categoria-item">
      <div class="icon bg-blue-100 text-blue-600">🚕</div>
      <div class="flex-1">
        <p class="font-medium">Transporte</p>
        <p class="text-xs text-slate-400">Gastos</p>
      </div>
      <span class="arrow">›</span>
    </div>

    <div class="categoria-item">
      <div class="icon bg-violet-100 text-violet-600">🏠</div>
      <div class="flex-1">
        <p class="font-medium">Servicios</p>
        <p class="text-xs text-slate-400">Gastos</p>
      </div>
      <span class="arrow">›</span>
    </div>

    <div class="categoria-item">
      <div class="icon bg-amber-100 text-amber-600">💼</div>
      <div class="flex-1">
        <p class="font-medium">Sueldo</p>
        <p class="text-xs text-slate-400">Ingresos</p>
      </div>
      <span class="arrow">›</span>
    </div>

    <div class="categoria-item">
      <div class="icon bg-sky-100 text-sky-600">🧾</div>
      <div class="flex-1">
        <p class="font-medium">Freelance</p>
        <p class="text-xs text-slate-400">Ingresos</p>
      </div>
      <span class="arrow">›</span>
    </div>

    <div class="categoria-item">
      <div class="icon bg-slate-100 text-slate-600">🏦</div>
      <div class="flex-1">
        <p class="font-medium">Ahorro</p>
        <p class="text-xs text-slate-400">Ahorro</p>
      </div>
      <span class="arrow">›</span>
    </div>

  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .categoria-item {
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(255,255,255,.95);
    color: #0F2F5A;
    padding: 16px;
    border-radius: 1.25rem;
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
    font-weight: 500;
  }

  .categoria-item .icon {
    width: 44px;
    height: 44px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
  }

  .categoria-item .arrow {
    color: #94a3b8;
    font-size: 1.2rem;
  }

  .categoria-item:active {
    transform: scale(.97);
  }
</style>
