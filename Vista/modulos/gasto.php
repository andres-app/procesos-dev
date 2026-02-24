<?php
$titulo = 'Gastos | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-2">

  <section class="mb-6">
    <div class="bg-white/90 text-primary rounded-2xl p-5 shadow-lg">
      <p class="text-sm text-slate-500">Gastos del mes</p>
      <h2 class="text-3xl font-semibold mt-1">S/ 1,245.50</h2>
    </div>
  </section>

  <section class="mb-4">
    <button
      id="btnNuevoGasto"
      class="w-full bg-red-500/90 text-white py-3 rounded-xl font-medium shadow-md active:scale-95 transition">
      + Registrar gasto
    </button>

  </section>

  <!-- MODAL NUEVO GASTO -->
  <div id="modalGasto" class="modal-overlay hidden">

    <div class="modal-sheet">

      <!-- HEADER MODAL -->
      <div class="modal-header">
        <h3>Nuevo gasto</h3>
        <button id="cerrarModalGasto">✕</button>
      </div>

      <!-- FORM -->
      <form class="modal-body space-y-4">

        <div>
          <label>Monto</label>
          <input
            type="number"
            id="montoGasto"
            placeholder="S/ 0.00">

          <!-- MONTOS RÁPIDOS -->
          <div class="quick-amounts gasto">
            <button type="button" data-value="20">20</button>
            <button type="button" data-value="50">50</button>
            <button type="button" data-value="100">100</button>
            <button type="button" data-value="200">200</button>
          </div>
        </div>


        <div>
          <label>Categoría</label>
          <select>
            <option>Alimentación</option>
            <option>Transporte</option>
            <option>Servicios</option>
            <option>Otros</option>
          </select>
        </div>

        <div>
          <label>Fecha</label>
          <input type="date" />
        </div>

        <div>
          <label>Descripción</label>
          <input type="text" placeholder="Opcional" />
        </div>

        <button class="guardar-btn">
          Guardar gasto
        </button>

      </form>

    </div>
  </div>


  <section class="space-y-3">

    <div class="gasto-item">
      <div class="icon bg-red-100 text-red-500">🍔</div>
      <div class="flex-1">
        <p class="font-medium">Alimentación</p>
        <p class="text-xs text-slate-400">Hoy · 13:20</p>
      </div>
      <span class="monto">- S/ 25.00</span>
    </div>

    <div class="gasto-item">
      <div class="icon bg-blue-100 text-blue-500">🚕</div>
      <div class="flex-1">
        <p class="font-medium">Transporte</p>
        <p class="text-xs text-slate-400">Ayer · 08:10</p>
      </div>
      <span class="monto">- S/ 12.50</span>
    </div>

    <div class="gasto-item">
      <div class="icon bg-emerald-100 text-emerald-500">🏠</div>
      <div class="flex-1">
        <p class="font-medium">Servicios</p>
        <p class="text-xs text-slate-400">02 Feb · 18:00</p>
      </div>
      <span class="monto">- S/ 180.00</span>
    </div>

  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .gasto-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, .92);
    color: #0F2F5A;
    padding: 14px;
    border-radius: 1.25rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
  }

  .gasto-item .icon {
    width: 42px;
    height: 42px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
  }

  .gasto-item .monto {
    font-weight: 600;
    color: #ef4444;
    white-space: nowrap;
  }

  /* ===== MODAL GASTO ===== */
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

  .guardar-btn {
  width: 100%;
  margin-top: 16px;
  background: #ef4444;
  color: #fff;
  padding: 14px;
  border-radius: 1.1rem;
  font-weight: 600;

  position: sticky;   /* 👈 UX iOS */
  bottom: 0;
}


  /* ===== QUICK AMOUNTS GASTOS ===== */
  .quick-amounts.gasto button {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }

  .quick-amounts.gasto button:active {
    background: #dc2626;
    color: #fff;
  }

  /* ===== QUICK AMOUNTS (BASE) ===== */
.quick-amounts {
  display: flex;
  gap: 10px;
  margin-top: 10px;
  flex-wrap: wrap;
}

.quick-amounts button {
  padding: 8px 14px;
  border-radius: 9999px; /* 👈 CLAVE */
  font-weight: 600;
  font-size: .85rem;
  background: #f8fafc;
  color: #334155;
  border: 1px solid #e2e8f0;
  transition: all .15s ease;
}

/* feedback táctil */
.quick-amounts button:active {
  transform: scale(.95);
}

</style>

<script>
  const btnNuevoGasto = document.getElementById('btnNuevoGasto');
  const modalGasto = document.getElementById('modalGasto');
  const cerrarModal = document.getElementById('cerrarModalGasto');

  btnNuevoGasto.addEventListener('click', () => {
    modalGasto.classList.remove('hidden');
  });

  cerrarModal.addEventListener('click', () => {
    modalGasto.classList.add('hidden');
  });

  modalGasto.addEventListener('click', e => {
    if (e.target === modalGasto) {
      modalGasto.classList.add('hidden');
    }
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {

    const montoInput = document.getElementById('montoGasto');

    document.querySelectorAll('.quick-amounts.gasto button').forEach(btn => {
      btn.addEventListener('click', () => {
        const value = parseFloat(btn.dataset.value);
        const current = parseFloat(montoInput.value) || 0;
        montoInput.value = (current + value).toFixed(2);
      });
    });

  });
</script>