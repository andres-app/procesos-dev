<?php
$titulo = $titulo ?? 'Admin';
$active = $active ?? '';
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
  <div class="min-h-screen flex">

    <!-- Sidebar desktop -->
    <aside class="hidden lg:flex lg:w-72 lg:flex-col bg-white border-r border-slate-200">
      <div class="px-5 py-4">
        <div class="text-xs text-slate-500">PROCESOS-DEV</div>
        <div class="text-lg font-semibold">Administrador</div>
      </div>

      <nav class="px-3 py-2 space-y-1">
        <a class="block px-3 py-2 rounded-xl <?= $active==='dashboard'?'bg-slate-900 text-white':'hover:bg-slate-100' ?>" href="/public/admin/dashboard">Dashboard</a>
        <a class="block px-3 py-2 rounded-xl <?= $active==='pac'?'bg-slate-900 text-white':'hover:bg-slate-100' ?>" href="/public/admin/pac">PAC</a>
        <a class="block px-3 py-2 rounded-xl <?= $active==='procesos'?'bg-slate-900 text-white':'hover:bg-slate-100' ?>" href="/public/admin/procesos">Procesos</a>
      </nav>

      <div class="mt-auto p-4 border-t border-slate-200">
        <div class="text-sm text-slate-600">Sesión: <b><?= htmlspecialchars($adminUser) ?></b></div>
        <a class="inline-block mt-2 text-sm underline" href="/public/admin/logout">Salir</a>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col">
      <header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="px-4 lg:px-6 py-3 flex items-center gap-3">
          <button id="btnMenu" class="lg:hidden rounded-xl border border-slate-200 px-3 py-2 text-sm">Menú</button>
          <div class="flex-1">
            <div class="text-sm text-slate-500">Admin</div>
            <div class="text-lg font-semibold"><?= htmlspecialchars($titulo) ?></div>
          </div>
          <a class="rounded-xl border border-slate-200 px-3 py-2 text-sm" href="/public/admin/logout">Salir</a>
        </div>

        <div id="drawer" class="lg:hidden hidden border-t border-slate-200 bg-white">
          <div class="p-3 space-y-1">
            <a class="block px-3 py-2 rounded-xl <?= $active==='dashboard'?'bg-slate-900 text-white':'hover:bg-slate-100' ?>" href="/admin/dashboard">Dashboard</a>
            <a class="block px-3 py-2 rounded-xl <?= $active==='pac'?'bg-slate-900 text-white':'hover:bg-slate-100' ?>" href="/admin/pac">PAC</a>
            <a class="block px-3 py-2 rounded-xl <?= $active==='procesos'?'bg-slate-900 text-white':'hover:bg-slate-100' ?>" href="/admin/procesos">Procesos</a>
          </div>
        </div>
      </header>

      <main class="p-4 lg:p-6">