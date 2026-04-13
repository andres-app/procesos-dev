<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
  <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-xs text-slate-500">PROCESOS-DEV</div>
      <h1 class="text-2xl font-semibold">Acceso Administrador</h1>
    </div>

    <?php if (!empty($err)): ?>
      <div class="mb-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-xl p-3">
        <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Usuario o correo</label>
        <input
          name="user"
          value="<?= htmlspecialchars($_POST['user'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
          class="w-full rounded-xl border border-slate-200 px-3 py-2"
          required>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
        <input
          type="password"
          name="pass"
          class="w-full rounded-xl border border-slate-200 px-3 py-2"
          required>
      </div>

      <button class="w-full rounded-xl bg-slate-900 text-white py-2.5 font-medium hover:bg-slate-800">
        Ingresar
      </button>

      <!-- BLOQUE 2FA -->
      <div class="mt-3 text-xs text-slate-500 text-center">
        ¿Primera vez?
        <a href="/public/admin/setup-2fa"
          class="font-medium text-blue-600 hover:underline">
          Configura tu autenticador
        </a>
      </div>
    </form>
  </div>
</body>
</html>