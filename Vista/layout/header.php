<?php
require_once __DIR__ . '/../../Config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title><?= $titulo ?? 'Seguimiento de procesos' ?></title>

  <!-- Viewport iOS PWA FIX -->
  <meta name="viewport"
    content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1, user-scalable=no">

  <!-- PWA -->
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <meta name="theme-color" content="#0F2F5A">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/icons/apple-touch-icon.png">

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
      height: 100%;
      background: #3E0F15;
      /* fallback */
    }

    /* Si usas gradient en Tailwind, asegura que el html también lo tenga */
    body {
      background: linear-gradient(135deg, #6B1C26 0%, #3E0F15 100%);
      background-attachment: fixed;
    }

    /* Rellena SIEMPRE la zona del status bar (la franja de arriba) */
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: env(safe-area-inset-top);
      background: linear-gradient(180deg, rgba(0, 0, 0, .18), rgba(0, 0, 0, 0));
      z-index: 9998;
      pointer-events: none;
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

    /* ===== HEADER (Premium / Apple-like) ===== */
    /* ===== HEADER (iOS clean / premium) ===== */
    .appbar {
      position: sticky;
      top: 0;
      z-index: 90;

      padding-top: calc(env(safe-area-inset-top) + 10px);
      padding-left: calc(env(safe-area-inset-left) + 16px);
      padding-right: calc(env(safe-area-inset-right) + 16px);
      padding-bottom: 10px;

      background: linear-gradient(to bottom, rgba(0, 0, 0, .18), rgba(0, 0, 0, .08));
      backdrop-filter: blur(18px) saturate(160%);
      -webkit-backdrop-filter: blur(18px) saturate(160%);
      border-bottom: 1px solid rgba(255, 255, 255, .08);
    }

    .appbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
    }

    .appbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }

    .app-avatar {
      width: 34px;
      height: 34px;
      border-radius: 12px;

      display: flex;
      align-items: center;
      justify-content: center;

      font-weight: 900;
      font-size: .90rem;

      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .14);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14);
    }

    .appbar-titles {
      min-width: 0;
    }

    .appbar-title {
      font-size: 1.02rem;
      font-weight: 800;
      letter-spacing: -.2px;
      line-height: 1.15;

      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .appbar-sub {
      margin-top: 2px;
      font-size: .80rem;
      color: rgba(219, 234, 254, .85);
      line-height: 1.1;

      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .appbar-sub strong {
      color: rgba(255, 255, 255, .92);
      font-weight: 800;
    }

    .appbar-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .appbar-btn {
      width: 34px;
      height: 34px;
      border-radius: 12px;

      display: flex;
      align-items: center;
      justify-content: center;

      text-decoration: none;

      background: rgba(255, 255, 255, .10);
      border: 1px solid rgba(255, 255, 255, .12);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .10);

      transition: transform .14s ease, background .14s ease;
      -webkit-tap-highlight-color: transparent;
      user-select: none;
    }

    .appbar-btn:active {
      transform: scale(.94);
    }

    @media (hover:hover) {
      .appbar-btn:hover {
        background: rgba(255, 255, 255, .14);
      }
    }

    .app-ico {
      width: 18px;
      height: 18px;
      stroke: rgba(255, 255, 255, .92);
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    /* ===== ESPACIO PARA BOTTOM NAV (FLOATING) ===== */
    .has-bottom-nav {
      padding-bottom: calc(118px + var(--sab));
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

    /* ===== ANTI-ZOOM iOS (inputs) ===== */
    input,
    select,
    textarea {
      font-size: 16px !important;
      /* evita zoom al enfocar */
    }

    html {
      -webkit-text-size-adjust: 100%;
    }

    /* reduce double-tap zoom en botones/links */
    a,
    button,
    [role="button"],
    .appbar-btn {
      touch-action: manipulation;
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

  <script>
    // Bloquea gesto de zoom (iOS Safari)
    document.addEventListener('gesturestart', e => e.preventDefault(), {
      passive: false
    });
    document.addEventListener('gesturechange', e => e.preventDefault(), {
      passive: false
    });
    document.addEventListener('gestureend', e => e.preventDefault(), {
      passive: false
    });

    // Bloquea pinch (2 dedos)
    document.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches.length > 1) e.preventDefault();
    }, {
      passive: false
    });
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
  <header class="appbar">
    <div class="appbar-inner">
      <div class="appbar-left">
        <div class="app-avatar" aria-hidden="true">
          <?= strtoupper(substr($usuario ?? 'U', 0, 1)) ?>
        </div>

        <div class="appbar-titles">
          <div class="appbar-title"><?= $appName ?? 'Seguimiento de procesos' ?></div>
          <div class="appbar-sub">Hola, <strong><?= $usuario ?? 'Usuario' ?></strong></div>
        </div>
      </div>

      <div class="appbar-actions">
        <a class="appbar-btn" href="<?= BASE_URL ?>/alertas" aria-label="Alertas" title="Alertas">
          <svg viewBox="0 0 24 24" class="app-ico" aria-hidden="true">
            <path d="M12 22a2.2 2.2 0 0 0 2.2-2.2H9.8A2.2 2.2 0 0 0 12 22Z" />
            <path d="M18 16.6V11a6 6 0 1 0-12 0v5.6L4.6 18h14.8L18 16.6Z" />
          </svg>
        </a>

        <a class="appbar-btn" href="<?= BASE_URL ?>/perfil" aria-label="Perfil" title="Perfil">
          <svg viewBox="0 0 24 24" class="app-ico" aria-hidden="true">
            <circle cx="12" cy="8" r="4" />
            <path d="M4 21a8 8 0 0 1 16 0" />
          </svg>
        </a>
      </div>
    </div>
  </header>