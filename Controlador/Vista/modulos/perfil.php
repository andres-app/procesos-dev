<?php
$titulo = 'Perfil | Seguimiento de procesos';
$appName = 'Seguimiento de procesos';
$usuario = 'Andres';

require __DIR__ . '/../layout/header.php';
?>

<main class="page flex-1 px-5 pt-4 overflow-y-auto">

  <!-- CARD PERFIL -->
  <section class="mb-6">
    <div class="bg-white/90 text-primary rounded-3xl p-6 shadow-xl text-center">
      <div class="avatar mx-auto mb-4">AS</div>
      <h2 class="text-xl font-semibold">Andres Silva</h2>
      <p class="text-sm text-slate-500">andres@correo.com</p>
    </div>
  </section>

  <!-- OPCIONES -->
  <section class="space-y-3 mb-8">

    <a href="#" class="perfil-item">
      <span>👤</span>
      <p class="flex-1">Editar perfil</p>
      <span class="arrow">›</span>
    </a>

    <a href="#" class="perfil-item">
      <span>🔒</span>
      <p class="flex-1">Cambiar contraseña</p>
      <span class="arrow">›</span>
    </a>

    <a href="#" class="perfil-item">
      <span>⚙️</span>
      <p class="flex-1">Configuración</p>
      <span class="arrow">›</span>
    </a>

  </section>

  <!-- CERRAR SESIÓN (SEPARADO) -->
  <section>
    <a href="<?= BASE_URL ?>/logout" class="logout-btn">
      Cerrar sesión
    </a>
  </section>

</main>

<?php require __DIR__ . '/../layout/bottom-nav.php'; ?>

<style>
  .avatar {
    width: 72px;
    height: 72px;
    border-radius: 9999px;
    background: #0F2F5A;
    color: #fff;
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .perfil-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,.95);
    color: #0F2F5A;
    padding: 16px;
    border-radius: 1.25rem;
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
    font-weight: 500;
  }

  .perfil-item .arrow {
    color: #94a3b8;
  }

  .perfil-item:active {
    transform: scale(.97);
  }

  /* BOTÓN CERRAR SESIÓN */
  .logout-btn {
    display: block;
    width: 100%;
    text-align: center;
    padding: 14px;
    border-radius: 1.25rem;
    background: #fee2e2;
    color: #dc2626;
    font-weight: 600;
    box-shadow: 0 6px 16px rgba(0,0,0,.1);
  }

  .logout-btn:active {
    transform: scale(.97);
    background: #fecaca;
  }
</style>
