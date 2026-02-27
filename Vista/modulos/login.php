<?php
require_once __DIR__ . '/../../Config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header("Location: " . BASE_URL . "/dashboard");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login | ACFFAA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            vino: '#7A1E2C',
            azuloscuro: '#0B1F3A',
            dorado: '#C9A227',
            fondogris: '#F4F4F4'
          }
        }
      }
    }
  </script>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">

    <div class="bg-[#6B1C26] p-7 text-center">
      <h1 class="text-lg font-bold text-white tracking-wide">
        AGENCIA DE COMPRAS
      </h1>
      <p class="text-sm text-[#D4AF37] mt-1">
        Fuerzas Armadas del Perú
      </p>
    </div>

    <form method="POST" class="p-8 space-y-6">

      <div>
        <label class="text-sm text-gray-500 font-medium">
          Usuario
        </label>
        <input
          type="text"
          name="usuario"
          class="w-full mt-2 h-12 px-4 rounded-xl border border-gray-300 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#D4AF37] transition"
          required />
      </div>

      <div>
        <label class="text-sm text-gray-500 font-medium">
          Contraseña
        </label>
        <input
          type="password"
          name="password"
          class="w-full mt-2 h-12 px-4 rounded-xl border border-gray-300 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#D4AF37] transition"
          required />
      </div>

      <button
        type="submit"
        class="w-full h-12 rounded-xl bg-[#C9A227] text-gray-900 font-semibold text-lg hover:brightness-105 active:scale-95 transition shadow-md">
        Iniciar sesión
      </button>

    </form>

    <div class="bg-gray-50 text-center py-4 text-xs text-gray-400">
      © 2026 Agencia de Compras de las Fuerzas Armadas
    </div>

  </div>

</body>
</html>