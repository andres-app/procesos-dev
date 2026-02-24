<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title><?= $titulo ?? 'Seguimiento de procesos' ?></title>

  <!-- Viewport / PWA -->
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<!-- PWA -->
<link rel="manifest" href="/public/manifest.json">
<meta name="theme-color" content="#0F2F5A">

<!-- iOS PWA ICON (ESTA ERA LA QUE FALTABA) -->
<link rel="apple-touch-icon" href="<?= BASE_URL ?>/icons/apple-touch-icon.png">

<!-- iOS PWA -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Seguimiento de procesos">

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#0F2F5A'
          }
        }
      }
    }
  </script>

  <!-- GLOBAL STYLES + TRANSITIONS -->
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      -webkit-tap-highlight-color: transparent;
    }

    /* ===== PAGE TRANSITION ===== */
    .page {
      opacity: 0;
      transform: translateY(12px);
      animation: pageEnter .35s ease-out forwards;
    }

    @keyframes pageEnter {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ===== iOS BOTTOM TAB BAR ===== */
    .ios-tabbar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 84px;
      background: rgba(255, 255, 255, .95);
      backdrop-filter: blur(14px);
      border-top: 1px solid rgba(0, 0, 0, .08);
      display: flex;
      justify-content: space-around;
      align-items: center;
      z-index: 50;
    }

    .tab {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      font-size: 0.7rem;
      color: #94a3b8;
      text-decoration: none;
      transition: transform .15s ease;
    }

    .tab .icon {
      width: 24px;
      height: 24px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.8;
    }

    .tab.active {
      color: #0F2F5A;
    }

    .tab.active .icon {
      stroke-width: 2.2;
    }

    .tab:active {
      transform: scale(.92);
    }

    .has-bottom-nav {
      padding-bottom: 100px;
    }

/* ===== PRELOADER ===== */
.preloader {
  position: fixed;
  inset: 0;
  background: rgba(107, 28, 38, 0.88); /* vino institucional */
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  opacity: 1;
  transition: opacity .35s ease;
}

.preloader.hide {
  opacity: 0;
  pointer-events: none;
}

.loader-card {
  background: rgba(255, 255, 255, .96);
  color: #6B1C26; /* vino */
  padding: 28px 32px;
  border-radius: 1.75rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
}

/* Spinner moderno */
.spinner {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 3px solid #E5E7EB; /* gris suave */
  border-top-color: #C9A227; /* dorado institucional */
  animation: spin 0.9s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loader-card p {
  font-size: .85rem;
  font-weight: 600;
  letter-spacing: .3px;
}
  </style>

  <!-- SERVICE WORKER -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?= BASE_URL ?>/service-worker.js');
      });
    }
  </script>

  <!-- PAGE EXIT TRANSITION -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        if (!href || !href.startsWith('<?= BASE_URL ?>')) return;

        link.addEventListener('click', e => {
          e.preventDefault();
          const page = document.querySelector('.page');
          if (!page) {
            window.location = href;
            return;
          }

          page.style.opacity = '0';
          page.style.transform = 'translateY(-12px)';
          page.style.transition = 'all .25s ease-in';

          setTimeout(() => {
            window.location = href;
          }, 250);
        });
      });
    });
  </script>

  <script>
    // Ocultar preloader cuando la página termina de cargar
    window.addEventListener('load', () => {
      const preloader = document.getElementById('preloader');
      if (preloader) {
        preloader.classList.add('hide');
      }
    });

    // Mostrar preloader al navegar entre páginas
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        if (!href || !href.startsWith('<?= BASE_URL ?>')) return;

        link.addEventListener('click', () => {
          const preloader = document.getElementById('preloader');
          if (preloader) {
            preloader.classList.remove('hide');
          }
        });
      });
    });
  </script>


</head>

<body class="bg-gradient-to-br from-[#6B1C26] to-[#3E0F15] text-white flex flex-col min-h-screen has-bottom-nav overflow-hidden">

  <!-- PRELOADER -->
  <div id="preloader" class="preloader">
    <div class="loader-card">
      <div class="spinner"></div>
      <p>Cargando…</p>
    </div>
  </div>


  <!-- HEADER -->
  <header class="app-header px-5 py-4 flex justify-between items-center">
    <!-- CONTENT WRAPPER -->
    <div class="flex-1 overflow-y-auto">

      <h1 class="text-xl font-semibold tracking-tight">
        <?= $appName ?? 'Seguimiento de procesos' ?>
      </h1>
      <span class="text-sm text-blue-200">
        Hola, <?= $usuario ?? 'Usuario' ?>
      </span>
  </header>