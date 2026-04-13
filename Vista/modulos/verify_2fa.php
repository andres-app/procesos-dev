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
  <title>Verificación 2FA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
  <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-xs text-slate-500">SEGURIDAD</div>
      <h1 class="text-2xl font-semibold">Verificación en dos pasos</h1>
      <p class="mt-1 text-sm text-slate-500">
        Ingrese el código de 6 dígitos de Google Authenticator.
      </p>
    </div>

    <?php if (!empty($err)): ?>
      <div class="mb-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-xl p-3">
        <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4" autocomplete="off">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Código 2FA</label>
        <input
          type="text"
          name="code"
          inputmode="numeric"
          maxlength="6"
          placeholder="123456"
          class="w-full rounded-xl border border-slate-200 px-3 py-2 text-center tracking-[0.35em] text-lg"
          required
        >
      </div>

      <button class="w-full rounded-xl bg-slate-900 text-white py-2.5 font-medium hover:bg-slate-800">
        Verificar
      </button>
    </form>
  </div>
</body>
</html>