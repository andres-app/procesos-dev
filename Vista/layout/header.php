<?php
require_once __DIR__ . '/../../Config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title><?= $titulo ?? 'Seguimiento de procesos' ?></title>

  <!-- Viewport iOS PWA FIX -->
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />

  <!-- PWA -->
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <meta name="theme-color" content="#0F2F5A">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/icons/apple-touch-icon.png">

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

  <!-- GLOBAL STYLES -->
  <style>
    html,
    body {
      margin: 0;
      padding: 0;
      -webkit-tap-highlight-color: transparent;
      min-height: 100dvh;
    }

    /* ===== SAFE AREA iOS ===== */
    :root {
      --sat: env(safe-area-inset-top);
      --sar: env(safe-area-inset-right);
      --sab: env(safe-area-inset-bottom);
      --sal: env(safe-area-inset-left);
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

    /* ===== HEADER ===== */
    .app-header {
      padding-top: calc(env(safe-area-inset-top) + 12px);
      padding-left: calc(env(safe-area-inset-left) + 20px);
      padding-right: calc(env(safe-area-inset-right) + 20px);
    }

    /* ===== iOS TAB BAR ===== */
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
      padding-bottom: calc(100px + var(--sab));
    }

    /* ===== PRELOADER ===== */
    .preloader {
      position: fixed;
      inset: 0;
      background: rgba(107, 28, 38, 0.88);
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
      color: #6B1C26;
      padding: 28px 32px;
      border-radius: 1.75rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 14px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, .25);
    }

    .spinner {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      border: 3px solid #E5E7EB;
      border-top-color: #C9A227;
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

  <!-- PRELOADER CONTROL -->
  <script>
    window.addEventListener('load', () => {
      const preloader = document.getElementById('preloader');
      if (preloader) preloader.classList.add('hide');
    });

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        if (!href || !href.startsWith('<?= BASE_URL ?>')) return;

        link.addEventListener('click', () => {
          const preloader = document.getElementById('preloader');
          if (preloader) preloader.classList.remove('hide');
        });
      });
    });
  </script>

</head>

<body class="bg-gradient-to-br from-[#6B1C26] to-[#3E0F15] text-white flex flex-col min-h-[100dvh] has-bottom-nav">

  <!-- PRELOADER -->
  <div id="preloader" class="preloader">
    <div class="loader-card">
      <div class="spinner"></div>
      <p>Cargando…</p>
    </div>
  </div>

  <!-- HEADER -->
  <header class="app-header px-5 py-4 flex justify-between items-center">
    <div>
      <h1 class="text-xl font-semibold tracking-tight">
        <?= $appName ?? 'Seguimiento de procesos' ?>
      </h1>
      <span class="text-sm text-blue-200">
        Hola, <?= $usuario ?? 'Usuario' ?>
      </span>
    </div>
  </header>