<?php
/* Vista/layout/header.php */
require_once __DIR__ . '/../../Config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title><?= $titulo ?? 'Seguimiento de procesos' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1, user-scalable=no">

  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <meta name="theme-color" content="#7A0C19">
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/icons/apple-touch-icon.png">

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Seguimiento de procesos">

  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root {
      --sat: env(safe-area-inset-top);
      --sar: env(safe-area-inset-right);
      --sab: env(safe-area-inset-bottom);
      --sal: env(safe-area-inset-left);

      --header-height: 92px;
      --tabbar-height: 78px;
      --tabbar-offset: 8px;
      --tabbar-total-space: calc(var(--tabbar-height) + var(--tabbar-offset) + var(--sab));
    }

    * {
      box-sizing: border-box;
    }

    html,
    body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      min-height: 100%;
      overflow: hidden;
      overscroll-behavior: none;
      -webkit-text-size-adjust: 100%;
      background: #F9FAFB;
      /* blanco elegante */
      font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", sans-serif;
    }

    body {
      color: #111827;
      position: relative;
    }

    .app-shell {
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      background: #F9FAFB;
    }

    .app-shell::after {
      display: none;
    }

    .page {
      opacity: 0;
      transform: translateY(12px);
      animation: pageEnter .32s ease-out forwards;
    }

    @keyframes pageEnter {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .appbar {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      z-index: 20;
      padding:
        calc(var(--sat) + 10px) calc(var(--sar) + 16px) 12px calc(var(--sal) + 16px);
      background: rgba(122, 12, 25, 0.92);
      /* guinda */
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    .appbar-title,
    .appbar-sub,
    .app-avatar,
    .app-ico {
      color: #fff;
    }

    .appbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      min-height: 48px;
    }

    .appbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }

    .app-avatar {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      font-weight: 800;
      color: #fff;
      flex-shrink: 0;
      background: rgba(255, 255, 255, .10);
      border: 1px solid rgba(255, 255, 255, .14);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .16);
    }

    .appbar-titles {
      min-width: 0;
      line-height: 1;
    }

    .appbar-title {
      font-size: 1.08rem;
      font-weight: 800;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      letter-spacing: -.2px;
    }

    .appbar-sub {
      margin-top: 4px;
      font-size: .78rem;
      color: rgba(255, 255, 255, .9);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .appbar-sub strong {
      font-weight: 800;
      color: #fff;
    }

    .appbar-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .appbar-btn {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      background: rgba(255, 255, 255, .10);
      border: 1px solid rgba(255, 255, 255, .12);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .12);
      -webkit-tap-highlight-color: transparent;
      transition: transform .14s ease, background .14s ease;
    }

    .appbar-btn:active {
      transform: scale(.94);
    }

    .app-ico {
      width: 19px;
      height: 19px;
      stroke: rgba(255, 255, 255, .94);
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .preloader {
      position: fixed;
      inset: 0;
      background: #F9FAFB;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      transition: opacity .22s ease;
    }

    .preloader.hide {
      opacity: 0;
      pointer-events: none;
    }

    .loader-card {
      background: #FFFFFF;
      color: #7A0C19;
      padding: 24px 28px;
      border-radius: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      box-shadow: 0 16px 38px rgba(0, 0, 0, .14);
      border: 1px solid #E5E7EB;
    }

    .spinner {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      border: 3px solid #E5E7EB;
      border-top-color: #7A0C19;
      animation: spin .9s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .loader-card p {
      margin: 0;
      font-size: .84rem;
      font-weight: 700;
    }

    input,
    select,
    textarea {
      font-size: 16px !important;
    }

    a,
    button,
    [role="button"] {
      touch-action: manipulation;
    }
  </style>

  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?= BASE_URL ?>/service-worker.js?v=2');
      });
    }
  </script>

  <script>
    document.addEventListener('gesturestart', e => e.preventDefault(), {
      passive: false
    });
    document.addEventListener('gesturechange', e => e.preventDefault(), {
      passive: false
    });
    document.addEventListener('gestureend', e => e.preventDefault(), {
      passive: false
    });

    document.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches.length > 1) e.preventDefault();
    }, {
      passive: false
    });
  </script>

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

<body>
  <div id="preloader" class="preloader">
    <div class="loader-card">
      <div class="spinner"></div>
      <p>Cargando…</p>
    </div>
  </div>

  <div class="app-shell">
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
              <path d="M12 22a2.2 2.2 0 0 0 2.2-2.2H9.8A2.2 2.2 0 0 0 12 22Z"></path>
              <path d="M18 16.6V11a6 6 0 1 0-12 0v5.6L4.6 18h14.8L18 16.6Z"></path>
            </svg>
          </a>

          <a class="appbar-btn" href="<?= BASE_URL ?>/perfil" aria-label="Perfil" title="Perfil">
            <svg viewBox="0 0 24 24" class="app-ico" aria-hidden="true">
              <circle cx="12" cy="8" r="4"></circle>
              <path d="M4 21a8 8 0 0 1 16 0"></path>
            </svg>
          </a>
        </div>
      </div>
    </header>