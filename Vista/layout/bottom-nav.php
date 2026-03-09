<?php
$path = $_GET['url'] ?? 'dashboard';
$moduloActual = explode('/', trim($path, '/'))[0];

function isActive($m, $actual)
{
  return $m === $actual ? 'is-active' : '';
}
?>

<nav class="ios-tabbar" role="navigation">

  <!-- HOME -->
  <a href="<?= BASE_URL ?>/dashboard" class="ios-tab <?= isActive('dashboard', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <path class="stroke" d="M3 10.5L12 3l9 7.5M5 10v10h5v-6h4v6h5V10" />
      <path class="fill" d="M4 10.5L12 4l8 6.5V20H4z" />
    </svg>
    <span class="ios-label">Home</span>
  </a>

  <!-- PROCESOS -->
  <a href="<?= BASE_URL ?>/procesos" class="ios-tab <?= isActive('procesos', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <rect class="stroke" x="3" y="7" width="18" height="12" rx="3" />
      <rect class="fill" x="4.5" y="8.5" width="15" height="9" rx="2" />
      <path class="stroke" d="M9 11h6" />
    </svg>
    <span class="ios-label">Procesos</span>
  </a>

  <!-- REPORTES -->
  <a href="<?= BASE_URL ?>/reportes" class="ios-tab <?= isActive('reportes', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <path class="stroke" d="M4 20V10M10 20V4M16 20v-6M2 20h20" />
      <path class="fill" d="M5 10h2v10H5zM11 4h2v16h-2zM17 14h2v6h-2z" />
    </svg>
    <span class="ios-label">Reportes</span>
  </a>

  <!-- PERFIL -->
  <a href="<?= BASE_URL ?>/perfil" class="ios-tab <?= isActive('perfil', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <circle class="stroke" cx="12" cy="8" r="4" />
      <circle class="fill" cx="12" cy="8" r="3" />
      <path class="stroke" d="M4 20a8 8 0 0116 0" />
      <path class="fill" d="M4 20c1.5-4 6-6 8-6s6.5 2 8 6z" />
    </svg>
    <span class="ios-label">Perfil</span>
  </a>

</nav>

<div class="bottom-nav-spacer" aria-hidden="true"></div>

<style>
  :root {
    --bn-h: 76px;
    --bn-gap: 12px;
    --bn-pad: calc(var(--bn-h) + var(--bn-gap) + env(safe-area-inset-bottom) + 18px);
  }

  /* TABBAR FIXED (iOS) */
  .ios-tabbar {
    position: fixed;
    left: 14px;
    right: 14px;
    bottom: calc(var(--bn-gap) + env(safe-area-inset-bottom));
    height: var(--bn-h);

    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;

    padding: 8px 6px;

    /* CLAVE: opaco para que NO se vea el contenido debajo */
    background: rgba(255, 255, 255, 1);

    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, .25);

    box-shadow:
      0 18px 50px rgba(0, 0, 0, .35),
      inset 0 1px 0 rgba(255, 255, 255, .65);

    z-index: 50;
  }

  /* ESPACIO REAL PARA QUE EL CONTENIDO TERMINE ARRIBA DEL TABBAR */
  body.has-bottom-nav {
    padding-bottom: 0 !important;
  }

  /* TAB ITEM */
  .ios-tab {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 6px;

    text-decoration: none;
    border-radius: 18px;
    color: rgba(15, 23, 42, .45);

    transition: transform .14s ease, color .18s ease;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
  }

  .ios-tab:active {
    transform: scale(.94);
  }

  /* ICONS */
  .ios-ico {
    width: 26px;
    height: 26px;
  }

  .ios-ico .stroke {
    fill: none;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
    opacity: .9;
  }

  .ios-ico .fill {
    fill: currentColor;
    opacity: 0;
  }

  /* LABEL */
  .ios-label {
    font-size: .70rem;
    font-weight: 700;
    letter-spacing: .2px;
    line-height: 1;
  }

  /* ACTIVE */
  .ios-tab.is-active {
    color: #0F2F5A;
  }

  .ios-tab.is-active .stroke {
    opacity: 0;
  }

  .ios-tab.is-active .fill {
    opacity: 1;
  }

  @media (hover:hover) {
    .ios-tab:hover {
      color: #0F2F5A;
    }
  }
</style>

<script>
  document.documentElement.classList.add('has-bottom-nav');
  document.body.classList.add('has-bottom-nav');
</script>