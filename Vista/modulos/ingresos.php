<?php
$titulo = 'Ingresos | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-2">

  <!-- RESUMEN -->
  <section class="mb-6">
    <div class="bg-white/90 text-primary rounded-2xl p-5 shadow-lg">
      <p class="text-sm text-slate-500">Ingresos del mes</p>
      <h2 class="text-3xl font-semibold mt-1">S/ 3,200.00</h2>
    </div>
  </section>

  <!-- BOTÓN NUEVO INGRESO -->
  <section class="mb-4">
    <button
      id="btnNuevoIngreso"
      class="w-full bg-emerald-500 text-white py-3 rounded-xl font-medium shadow-md active:scale-95 transition">
      + Registrar ingreso
    </button>
  </section>

  <!-- MODAL NUEVO INGRESO -->
  <div id="modalIngreso" class="modal-overlay hidden">

    <div class="modal-sheet">

      <!-- HEADER MODAL -->
      <div class="modal-header">
        <h3>Nuevo ingreso</h3>
        <button id="cerrarModalIngreso">✕</button>
      </div>

      <!-- FORM -->
      <form class="modal-body space-y-4">
        <div>
          <label>Monto</label>
          <input
            type="number"
            id="montoIngreso"
            placeholder="S/ 0.00">

          <!-- MONTOS RÁPIDOS -->
          <div class="quick-amounts">
            <button type="button" data-value="20">+20</button>
            <button type="button" data-value="50">+50</button>
            <button type="button" data-value="100">+100</button>
            <button type="button" data-value="500">+500</button>
          </div>
        </div>


        <div>
          <label>Tipo de ingreso</label>
          <select>
            <option>Sueldo</option>
            <option>Freelance</option>
            <option>Ventas</option>
            <option>Otros</option>
          </select>
        </div>

        <div>
          <label>Fecha</label>
          <input type="date">
        </div>

        <div>
          <label>Descripción</label>
          <input type="text" placeholder="Opcional">
        </div>

        <button class="guardar-btn ingreso">
          Guardar ingreso
        </button>

      </form>

    </div>
  </div>



  <!-- LISTA DE INGRESOS -->
  <section class="space-y-3">

    <div class="ingreso-item">
      <div class="icon bg-emerald-100 text-emerald-600">💼</div>
      <div class="flex-1">
        <p class="font-medium">Sueldo</p>
        <p class="text-xs text-slate-400">Hoy · 09:00</p>
      </div>
      <span class="monto">+ S/ 2,500.00</span>
    </div>

    <div class="ingreso-item">
      <div class="icon bg-blue-100 text-blue-600">🧾</div>
      <div class="flex-1">
        <p class="font-medium">Freelance</p>
        <p class="text-xs text-slate-400">05 Feb · 18:30</p>
      </div>
      <span class="monto">+ S/ 700.00</span>
    </div>

  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .ingreso-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, .92);
    color: #0F2F5A;
    padding: 14px;
    border-radius: 1.25rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
  }

  .ingreso-item .icon {
    width: 42px;
    height: 42px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
  }

  .ingreso-item .monto {
    font-weight: 600;
    color: #16a34a;
    white-space: nowrap;
  }

  /* ===== MODAL BASE (REUTILIZABLE) ===== */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .35);
    backdrop-filter: blur(6px);
    z-index: 80;
    display: flex;
    justify-content: center;
    align-items: flex-end;
  }

  .modal-overlay.hidden {
    display: none;
  }

  .modal-sheet {
    width: 100%;
    max-width: 500px;
    background: #fff;
    color: #0F2F5A;
    border-radius: 1.75rem 1.75rem 0 0;
    padding: 20px;
    animation: slideUp .35s ease-out;
  }

  @keyframes slideUp {
    from {
      transform: translateY(100%);
    }

    to {
      transform: translateY(0);
    }
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
  }

  .modal-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
  }

  .modal-header button {
    font-size: 1.2rem;
  }

  .modal-body label {
    font-size: .85rem;
    color: #64748b;
  }

  .modal-body input,
  .modal-body select {
    width: 100%;
    padding: 12px;
    border-radius: .9rem;
    border: 1px solid #e2e8f0;
    margin-top: 4px;
  }

  /* BOTÓN GUARDAR */
  .guardar-btn {
    width: 100%;
    margin-top: 12px;
    padding: 14px;
    border-radius: 1.1rem;
    font-weight: 600;
    color: #fff;
  }

  /* VARIANTE INGRESO */
  .guardar-btn.ingreso {
    background: #16a34a;
  }

  /* ===== QUICK AMOUNTS ===== */
  .quick-amounts {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
  }

  .quick-amounts button {
    padding: 8px 14px;
    border-radius: 9999px;
    background: #ecfdf5;
    color: #16a34a;
    font-weight: 600;
    font-size: .85rem;
    border: 1px solid #bbf7d0;
    transition: all .15s ease;
  }

  .quick-amounts button:active {
    transform: scale(.95);
    background: #16a34a;
    color: white;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {

    const btnNuevoIngreso = document.getElementById('btnNuevoIngreso');
    const modalIngreso = document.getElementById('modalIngreso');
    const cerrarModalIngreso = document.getElementById('cerrarModalIngreso');

    btnNuevoIngreso.addEventListener('click', () => {
      modalIngreso.classList.remove('hidden');
    });

    cerrarModalIngreso.addEventListener('click', () => {
      modalIngreso.classList.add('hidden');
    });

    modalIngreso.addEventListener('click', e => {
      if (e.target === modalIngreso) {
        modalIngreso.classList.add('hidden');
      }
    });

  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {

    const montoInput = document.getElementById('montoIngreso');

    document.querySelectorAll('.quick-amounts button').forEach(btn => {
      btn.addEventListener('click', () => {
        const value = parseFloat(btn.dataset.value);
        const current = parseFloat(montoInput.value) || 0;
        montoInput.value = (current + value).toFixed(2);
      });
    });

  });
</script>