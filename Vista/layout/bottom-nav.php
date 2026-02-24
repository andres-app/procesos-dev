<?php
// Detecta módulo actual desde el router (?url=...)
$path = $_GET['url'] ?? 'dashboard';
$moduloActual = explode('/', trim($path, '/'))[0];

// Helper: devuelve "active" si coincide
function isActive($m, $actual)
{
  return $m === $actual ? 'active' : '';
}
?>

<nav class="ios-tabbar">

  <!-- HOME -->
  <a href="<?= BASE_URL ?>/dashboard" class="tab <?= isActive('dashboard', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="icon">
      <path d="M3 10.5L12 3l9 7.5" />
      <path d="M5 10v10h5v-6h4v6h5V10" />
    </svg>
    <span>Home</span>
  </a>

  <!-- AHORRO -->
  <a href="<?= BASE_URL ?>/ahorro" class="tab <?= isActive('ahorro', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="icon">
      <path d="M3 10h18v6a4 4 0 01-4 4H7a4 4 0 01-4-4v-6z" />
      <path d="M12 6v8" />
      <path d="M9 9h6" />
    </svg>
    <span>Procesos</span>
  </a>

  <!-- REPORTES -->
  <a href="<?= BASE_URL ?>/reportes" class="tab <?= isActive('reportes', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="icon">
      <path d="M4 20V10" />
      <path d="M10 20V4" />
      <path d="M16 20v-6" />
      <path d="M2 20h20" />
    </svg>
    <span>Reportes</span>
  </a>

  <!-- PERFIL -->
  <a href="<?= BASE_URL ?>/perfil" class="tab <?= isActive('perfil', $moduloActual) ?>">
    <svg viewBox="0 0 24 24" class="icon">
      <circle cx="12" cy="8" r="4" />
      <path d="M4 20a8 8 0 0116 0" />
    </svg>
    <span>Perfil</span>
  </a>

</nav>