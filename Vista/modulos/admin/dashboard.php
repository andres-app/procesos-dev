<?php
$titulo = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/../../layout/admin_header.php';
?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
  <div class="rounded-2xl bg-white border border-slate-200 p-4">
    <div class="text-sm text-slate-500">PAC</div>
    <div class="text-2xl font-semibold">—</div>
  </div>
  <div class="rounded-2xl bg-white border border-slate-200 p-4">
    <div class="text-sm text-slate-500">Procesos</div>
    <div class="text-2xl font-semibold">—</div>
  </div>
</div>
<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>