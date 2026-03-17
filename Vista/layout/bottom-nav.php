<?php
$path = $_GET['url'] ?? 'dashboard';
$moduloActual = explode('/', trim($path, '/'))[0];

function isActive($m, $actual)
{
  return $m === $actual ? 'is-active' : '';
}
?>

<nav class="ios-tabbar" role="navigation" aria-label="Navegación principal">
  <a href="<?= BASE_URL ?>/dashboard" class="ios-tab <?= isActive('dashboard', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <path class="stroke" d="M3 10.5L12 3l9 7.5M5 10v10h5v-6h4v6h5V10" />
      <path class="fill" d="M4 10.5L12 4l8 6.5V20H4z" />
    </svg>
    <span class="ios-label">Home</span>
  </a>

  <a href="<?= BASE_URL ?>/procesos" class="ios-tab <?= isActive('procesos', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <rect class="stroke" x="3" y="7" width="18" height="12" rx="3" />
      <rect class="fill" x="4.5" y="8.5" width="15" height="9" rx="2" />
      <path class="stroke" d="M9 11h6" />
    </svg>
    <span class="ios-label">Procesos</span>
  </a>

  <a href="<?= BASE_URL ?>/reportes" class="ios-tab <?= isActive('reportes', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <path class="stroke" d="M4 20V10M10 20V4M16 20v-6M2 20h20" />
      <path class="fill" d="M5 10h2v10H5zM11 4h2v16h-2zM17 14h2v6h-2z" />
    </svg>
    <span class="ios-label">Reportes</span>
  </a>

  <a href="<?= BASE_URL ?>/perfil" class="ios-tab <?= isActive('perfil', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="ios-ico">
      <circle class="stroke" cx="12" cy="8" r="4" />
      <circle class="fill" cx="12" cy="8" r="3" />
      <path class="stroke" d="M4 20a8 8 0 0 1 16 0" />
      <path class="fill" d="M4 20c1.5-4 6-6 8-6s6.5 2 8 6z" />
    </svg>
    <span class="ios-label">Perfil</span>
  </a>
</nav>

<style>
  .ios-tabbar {
    position: fixed;
    left: 18px;
    right: 18px;
    bottom: calc(var(--sab) + 6px);
    height: var(--tabbar-height);
    z-index: 30;

    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;

    padding: 8px;
    border-radius: 28px;

    background: rgba(250, 247, 248, .96);
    backdrop-filter: blur(18px) saturate(180%);
    -webkit-backdrop-filter: blur(18px) saturate(180%);

    border: 1px solid rgba(255,255,255,.7);
    box-shadow:
      0 14px 36px rgba(0,0,0,.22),
      inset 0 1px 0 rgba(255,255,255,.75);
  }

  .ios-tab {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 6px;
    border-radius: 22px;
    text-decoration: none;
    color: rgba(17,24,39,.42);
    transition: transform .14s ease, color .14s ease, background .14s ease;
    -webkit-tap-highlight-color: transparent;
  }

  .ios-tab:active {
    transform: scale(.95);
  }

  .ios-tab.is-active {
    color: #173f79;
    background: rgba(0,0,0,.04);
  }

  .ios-ico {
    width: 25px;
    height: 25px;
  }

  .ios-ico .stroke {
    fill: none;
    stroke: currentColor;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
    opacity: .95;
  }

  .ios-ico .fill {
    fill: currentColor;
    opacity: 0;
  }

  .ios-tab.is-active .stroke {
    opacity: 0;
  }

  .ios-tab.is-active .fill {
    opacity: 1;
  }

  .ios-label {
    font-size: .70rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: .15px;
  }
</style>

</div>
</body>
</html>