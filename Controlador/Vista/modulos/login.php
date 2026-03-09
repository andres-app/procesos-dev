<?php
require_once __DIR__ . '/../../Config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // aquí iría tu validación real (sesión, etc.)
  header("Location: " . BASE_URL . "/dashboard");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login | ACFFAA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            vino: '#7A1E2C',
            azuloscuro: '#0B1F3A',
            dorado: '#C9A227',
            fondogris: '#F4F4F4'
          }
        }
      }
    }
  </script>

  <style>
    /* ===== PRELOADER (login) ===== */
    .preloader {
      position: fixed;
      inset: 0;
      background: rgba(107, 28, 38, 0.88);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      pointer-events: none;
      transition: opacity .20s ease;
    }
    .preloader.show {
      opacity: 1;
      pointer-events: auto;
    }
    .loader-card {
      background: rgba(255, 255, 255, .96);
      color: #6B1C26;
      padding: 28px 32px;
      border-radius: 1.75rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 14px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
      min-width: 220px;
    }
    .spinner {
      width: 38px;
      height: 38px;
      border-radius: 999px;
      border: 3px solid #E5E7EB;
      border-top-color: #C9A227;
      animation: spin .9s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loader-card p {
      font-size: .85rem;
      font-weight: 700;
      letter-spacing: .3px;
    }
  </style>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

  <!-- PRELOADER -->
  <div id="preloader" class="preloader" aria-hidden="true">
    <div class="loader-card" role="status" aria-live="polite">
      <div class="spinner"></div>
      <p>Ingresando…</p>
    </div>
  </div>

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">

    <div class="bg-[#6B1C26] p-7 text-center">
      <h1 class="text-lg font-bold text-white tracking-wide">
        AGENCIA DE COMPRAS
      </h1>
      <p class="text-sm text-[#D4AF37] mt-1">
        Fuerzas Armadas del Perú
      </p>
    </div>

    <form id="loginForm" method="POST" class="p-8 space-y-6" autocomplete="on">

      <div>
        <label class="text-sm text-gray-500 font-medium">Usuario</label>
        <input
          type="text"
          name="usuario"
          value="admin"
          class="w-full mt-2 h-12 px-4 rounded-xl border border-gray-300 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#D4AF37] transition"
          required />
      </div>

      <div>
        <label class="text-sm text-gray-500 font-medium">Contraseña</label>
        <input
          type="password"
          name="password"
          value="123123"
          class="w-full mt-2 h-12 px-4 rounded-xl border border-gray-300 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#D4AF37] transition"
          required />
      </div>

      <button
        id="btnLogin"
        type="submit"
        class="w-full h-12 rounded-xl bg-[#C9A227] text-gray-900 font-semibold text-lg hover:brightness-105 active:scale-95 transition shadow-md">
        Iniciar sesión
      </button>

    </form>

    <div class="bg-gray-50 text-center py-4 text-xs text-gray-400">
      © 2026 Agencia de Compras de las Fuerzas Armadas
    </div>

  </div>

  <script>
    (function () {
      const form = document.getElementById('loginForm');
      const preloader = document.getElementById('preloader');
      const btn = document.getElementById('btnLogin');

      if (!form || !preloader) return;

      form.addEventListener('submit', (e) => {
        // si el browser bloquea el submit por required, no mostramos loader
        if (!form.checkValidity()) return;

        // muestra preloader inmediatamente
        preloader.classList.add('show');
        preloader.setAttribute('aria-hidden', 'false');

        // evita doble submit
        if (btn) {
          btn.disabled = true;
          btn.classList.add('opacity-80', 'cursor-not-allowed');
        }

        // deja que pinte el overlay antes de navegar
        e.preventDefault();
        requestAnimationFrame(() => form.submit());
      });

      // por si el usuario vuelve con "Atrás", ocultar overlay
      window.addEventListener('pageshow', () => {
        preloader.classList.remove('show');
        preloader.setAttribute('aria-hidden', 'true');
        if (btn) {
          btn.disabled = false;
          btn.classList.remove('opacity-80', 'cursor-not-allowed');
        }
      });
    })();
  </script>

</body>
</html>