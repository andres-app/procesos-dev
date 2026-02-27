<?php
$titulo = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/../../layout/admin_layout.php';
?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
  <div class="rounded-xl bg-white border border-slate-200 p-3">
    <div class="text-xs text-slate-500">PAC</div>
    <div class="text-xl font-semibold">—</div>
  </div>
  <div class="rounded-xl bg-white border border-slate-200 p-3">
    <div class="text-xs text-slate-500">Procesos</div>
    <div class="text-xl font-semibold">—</div>
  </div>
</div>
<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>