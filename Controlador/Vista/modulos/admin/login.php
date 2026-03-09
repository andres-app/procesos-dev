<?php
// Vista/modulos/admin/login.php
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['user'] ?? '');
  $p = (string)($_POST['pass'] ?? '');

  // Recomendado: variables de entorno en servidor
  $ADMIN_USER = getenv('ADMIN_USER') ?: 'admin';
  $ADMIN_PASS = getenv('ADMIN_PASS') ?: 'admin123';

  if (hash_equals($ADMIN_USER, $u) && hash_equals($ADMIN_PASS, $p)) {
    $_SESSION['admin_user'] = $u;
    header("Location: /public/admin/dashboard");
    exit;
  }
  $err = 'Usuario o contraseña incorrectos.';
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

    <?php if ($err): ?>
      <div class="mb-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-xl p-3">
        <?= htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Usuario</label>
        <input name="user" value="admin" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
        <input type="password" value="admin123" name="pass" class="w-full rounded-xl border border-slate-200 px-3 py-2" required>
      </div>

      <button class="w-full rounded-xl bg-slate-900 text-white py-2.5 font-medium hover:bg-slate-800">
        Ingresar
      </button>
    </form>
  </div>
</body>
</html>