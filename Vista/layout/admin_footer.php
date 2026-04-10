</main>
</div>
</div>

<!-- Toast container global -->
<div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- DataTables Responsive -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
  /* ===== TOASTER GLOBAL ===== */

  function getToastIcon(type) {
    if (type === 'success') {
      return `
      <svg viewBox="0 0 24 24" class="toast__icon" aria-hidden="true">
        <path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm-1 14-4-4 1.41-1.41L11 13.17l5.59-5.58L18 9Z"/>
      </svg>
    `;
    }

    if (type === 'error') {
      return `
      <svg viewBox="0 0 24 24" class="toast__icon" aria-hidden="true">
        <path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm4.24 12.83L14.83 16.24 12 13.41l-2.83 2.83-1.41-1.41L10.59 12 7.76 9.17l1.41-1.41L12 10.59l2.83-2.83 1.41 1.41L13.41 12Z"/>
      </svg>
    `;
    }

    if (type === 'warning') {
      return `
      <svg viewBox="0 0 24 24" class="toast__icon" aria-hidden="true">
        <path fill="currentColor" d="M1 21h22L12 2 1 21Zm12-3h-2v-2h2Zm0-4h-2v-4h2Z"/>
      </svg>
    `;
    }

    return `
    <svg viewBox="0 0 24 24" class="toast__icon" aria-hidden="true">
      <path fill="currentColor" d="M11 17h2v-6h-2Zm0-8h2V7h-2Zm1-7a10 10 0 1 0 10 10A10 10 0 0 0 12 2Z"/>
    </svg>
  `;
  }

  function showToast(message, type = 'info', title = '') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const duration = type === 'error' ? 5000 : 3200;

    const wrap = document.createElement('div');
    wrap.className = 'toast-wrap';

    wrap.innerHTML = `
    <div class="toast toast--${type}">
      ${getToastIcon(type)}

      <div class="toast__body">
        ${title ? `<div class="toast__title">${title}</div>` : ''}
        <div class="toast__text">${message}</div>
      </div>

      <button type="button" class="toast__close" aria-label="Cerrar notificación">×</button>
      <div class="toast__progress" style="animation-duration:${duration}ms"></div>
    </div>
  `;

    const toast = wrap.querySelector('.toast');
    const btnClose = wrap.querySelector('.toast__close');

    function closeToast() {
      if (!toast) return;
      toast.classList.add('toast--closing');
      setTimeout(() => wrap.remove(), 180);
    }

    btnClose?.addEventListener('click', closeToast);

    container.appendChild(wrap);
    setTimeout(closeToast, duration);
  }

  window.showToast = showToast;

  /* ===== SIDEBAR ===== */

  (function() {
    const btn = document.getElementById('btnToggle');
    const overlay = document.getElementById('overlay');

    const isDesktop = () => window.innerWidth >= 1024;

    if (!btn) return;

    btn.addEventListener('click', () => {
      if (isDesktop()) {
        document.body.classList.toggle('sb-rail');
      } else {
        document.body.classList.toggle('sb-open');
        overlay?.classList.toggle('hidden');
      }
    });

    overlay?.addEventListener('click', () => {
      document.body.classList.remove('sb-open');
      overlay?.classList.add('hidden');
    });

    window.addEventListener('resize', () => {
      if (isDesktop()) {
        document.body.classList.remove('sb-open');
        overlay?.classList.add('hidden');
      } else {
        document.body.classList.remove('sb-rail');
      }
    });
  })();

  /* ===== DATATABLE GLOBAL ===== */

  document.addEventListener('DOMContentLoaded', function() {
    if (!window.jQuery || !jQuery.fn.DataTable) return;

    const tablas = document.querySelectorAll('table[data-dt-global]');
    if (!tablas.length) return;

    tablas.forEach((tabla) => {
      let dt;

      if (jQuery.fn.DataTable.isDataTable(tabla)) {
        dt = jQuery(tabla).DataTable();
      } else {
        dt = jQuery(tabla).DataTable({
          pageLength: 10,
          lengthMenu: [10, 25, 50, 100],
          paging: true,
          info: true,
          ordering: true,
          order: [],
          searching: true,
          autoWidth: false,
          scrollX: true,
          scrollCollapse: true,
          dom: '<"top"lfr>rt<"bottom"ip>',
          language: {
            lengthMenu: "Ver _MENU_",
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty: "Mostrando 0 a 0 de 0",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay registros",
            search: "Buscar:",
            paginate: {
              previous: "‹",
              next: "›"
            }
          },
          initComplete: function() {
            tabla.classList.remove('opacity-0');
            tabla.classList.add('dt-ready');
            this.api().columns.adjust();
          }
        });
      }

      tabla.classList.remove('opacity-0');
      tabla.classList.add('dt-ready');
      dt.columns.adjust();

      window.addEventListener('resize', function() {
        dt.columns.adjust();
      });
    });
  });
</script>

</body>

</html>