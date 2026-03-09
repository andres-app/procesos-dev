<?php
require_once __DIR__ . '/../../../../Config/config.php';

// viene desde router: $type = $parts[2] ?? 'estado';
$type = $type ?? ($_GET['type'] ?? 'estado');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte <?= htmlspecialchars($type) ?></title>
  <style>
    body{ font-family: Arial, Helvetica, sans-serif; margin:20px; color:#0f172a; }
    .box{ border:1px solid #e2e8f0; border-radius:12px; padding:14px; }
    @media print{ .no-print{ display:none; } body{ margin:0; } }
  </style>
</head>
<body>
  <div class="no-print" style="margin-bottom:12px;">
    <button onclick="window.print()">Imprimir / Guardar como PDF</button>
  </div>

  <h2>Reporte (maqueta)</h2>
  <div class="box">
    <div><b>Tipo:</b> <?= htmlspecialchars($type) ?></div>
    <div><b>Generado:</b> <?= date('d/m/Y H:i') ?></div>
    <div style="margin-top:10px; color:#64748b;">Aquí irá el contenido real cuando conectes BD.</div>
  </div>
</body>
</html>