<?php
$titulo = 'Seguimiento | Procesos';
$appName = 'Procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page page-shell flex-1 px-5 pt-4 overflow-y-auto">

  <!-- RESUMEN SEGUIMIENTO -->
  <!-- PANEL (SLIDE EN MÓVIL / GRID EN DESKTOP) -->
  <section class="mb-6">
    <div class="paneles flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2">

      <!-- PANEL 1 -->
      <div class="panel-slide snap-start">
        <p class="text-sm text-slate-500 mb-1">Fase</p>
        <h2 class="text-xl font-semibold">No recepcionados</h2>

        <div class="mt-4">
          <div class="flex justify-between text-sm mb-1">
            <span>21 procesos</span>
            <span class="text-slate-400">de 128</span>
          </div>

          <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-[#6B1C26] rounded-full" style="width:16%"></div>
          </div>
        </div>
      </div>

      <!-- PANEL 2 -->
      <div class="panel-slide snap-start">
        <p class="text-sm text-slate-500 mb-1">Estado</p>
        <h2 class="text-xl font-semibold">Convocado</h2>

        <div class="mt-4">
          <div class="flex justify-between text-sm mb-1">
            <span>34 procesos</span>
            <span class="text-slate-400">de 128</span>
          </div>

          <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-[#C9A227] rounded-full" style="width:27%"></div>
          </div>
        </div>
      </div>

      <!-- PANEL 3 -->
      <div class="panel-slide snap-start">
        <p class="text-sm text-slate-500 mb-1">OBAC</p>
        <h2 class="text-xl font-semibold">Ejército (EP)</h2>

        <div class="mt-4">
          <div class="flex justify-between text-sm mb-1">
            <span>41 procesos</span>
            <span class="text-slate-400">de 128</span>
          </div>

          <div class="h-3 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-slate-800 rounded-full" style="width:32%"></div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ACCIÓN PRINCIPAL -->
  <section class="mb-6">
    <button class="w-full bg-[#6B1C26]/95 text-white py-3 rounded-xl font-medium shadow-md active:scale-95 transition">
      + Registrar seguimiento
    </button>
  </section>

  <!-- ÚLTIMAS ACTUALIZACIONES -->
  <section class="space-y-3">

    <div class="seg-item">
      <div class="icon bg-[#6B1C26]/15 text-[#6B1C26]">📌</div>
      <div class="flex-1">
        <p class="font-medium">Proceso 0123 - Actualizado a “Convocado”</p>
        <p class="text-xs text-slate-400">Hoy · 10:15 · EP · AF-2026</p>
      </div>
      <span class="tag tag-vino">Convocado</span>
    </div>

    <div class="seg-item">
      <div class="icon bg-[#C9A227]/20 text-[#7A5B00]">📝</div>
      <div class="flex-1">
        <p class="font-medium">Exp. PAC 0456 - Observación registrada</p>
        <p class="text-xs text-slate-400">Ayer · 16:40 · FAP · AF-2026</p>
      </div>
      <span class="tag tag-dorado">Observado</span>
    </div>

    <div class="seg-item">
      <div class="icon bg-slate-200 text-slate-700">⏰</div>
      <div class="flex-1">
        <p class="font-medium">Proceso 0098 - Próximo a vencer (2 días)</p>
        <p class="text-xs text-slate-400">02 Feb · 09:00 · MGP · AF-2026</p>
      </div>
      <span class="tag tag-gris">Alerta</span>
    </div>

  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  /* =========================================================
     SEGUIMIENTO - Mobile first (respeta tu diseño)
     ========================================================= */

  .seg-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, .92);
    color: #111827;
    padding: 14px;
    border-radius: 1.25rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
  }

  .seg-item .icon {
    width: 42px;
    height: 42px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex: 0 0 auto;
  }

  .tag {
    font-weight: 700;
    font-size: .72rem;
    padding: 6px 10px;
    border-radius: 9999px;
    white-space: nowrap;
    flex: 0 0 auto;
  }

  .tag-vino {
    background: rgba(107, 28, 38, .12);
    color: #6B1C26;
    border: 1px solid rgba(107, 28, 38, .18);
  }

  .tag-dorado {
    background: rgba(201, 162, 39, .18);
    color: #7A5B00;
    border: 1px solid rgba(201, 162, 39, .25);
  }

  .tag-gris {
    background: rgba(148, 163, 184, .22);
    color: #334155;
    border: 1px solid rgba(148, 163, 184, .28);
  }

  .panel-slide {
    min-width: 85%;
    background: rgba(255, 255, 255, .95);
    color: #111827;
    padding: 20px;
    border-radius: 1.75rem;
    box-shadow: 0 12px 30px rgba(0, 0, 0, .15);
  }

  /* =========================================================
     DESKTOP MODEL (sin afectar móvil)
     ========================================================= */

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

    .paneles {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
      overflow: visible;
      padding-bottom: 0;
    }

    .paneles .panel-slide {
      min-width: 0;
    }
  }

  @media (min-width: 1280px) {
    .paneles {
      grid-template-columns: repeat(4, minmax(0, 1fr));
    }
  }
</style>