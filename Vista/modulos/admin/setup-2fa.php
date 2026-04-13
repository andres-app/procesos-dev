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
  <title>Configurar 2FA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
  <div class="w-full max-w-xl bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
    <div class="mb-6">
      <div class="text-xs text-slate-500">SEGURIDAD</div>
      <h1 class="text-2xl font-semibold">Configurar Google Authenticator</h1>
      <p class="mt-1 text-sm text-slate-500">
        Escanea el QR con Google Authenticator y luego ingresa el código de 6 dígitos.
      </p>
    </div>

    <?php if (!empty($err)): ?>
      <div class="mb-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-xl p-3">
        <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($ok)): ?>
      <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">
        <?= htmlspecialchars($ok, ENT_QUOTES, 'UTF-8') ?>
      </div>

      <div class="mt-4">
        <a href="/public/admin/dashboard" class="inline-flex rounded-xl bg-slate-900 text-white px-4 py-2.5">
          Ir al dashboard
        </a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <div>
          <div id="qrcode" class="rounded-2xl border border-slate-200 p-4 inline-block bg-white"></div>
        </div>

        <div class="space-y-4">
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
            <div class="text-xs text-slate-500 mb-1">Clave manual</div>
            <div class="font-mono text-sm break-all">
              <?= htmlspecialchars($secret, ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>

          <form method="post" class="space-y-4" autocomplete="off">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Código de 6 dígitos</label>
              <input
                type="text"
                name="code"
                inputmode="numeric"
                maxlength="6"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-center tracking-[0.35em] text-lg"
                required
              >
            </div>

            <button class="w-full rounded-xl bg-slate-900 text-white py-2.5 font-medium hover:bg-slate-800">
              Activar doble factor
            </button>
          </form>
        </div>
      </div>

      <script>
        QRCode.toCanvas(
          document.createElement('canvas'),
          <?= json_encode($otpAuthUri) ?>,
          function (err, canvas) {
            if (!err) {
              const box = document.getElementById('qrcode');
              box.innerHTML = '';
              box.appendChild(canvas);
            }
          }
        );
      </script>
    <?php endif; ?>
  </div>
</body>
</html>