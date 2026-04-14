<?php
// Vista/modulos/admin/exports/export_pdf_view.php
require_once __DIR__ . '/../../../../Config/config.php';

$type = $type ?? ($_GET['type'] ?? 'estado');
$type = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$type);

if ($type === '') {
    $type = 'estado';
}

$titulos = [
    'estado'      => 'Reporte por Estado',
    'derivados'   => 'Reporte de Derivados (OBAC)',
    'inversiones' => 'Reporte de Inversiones',
    'consolidado' => 'Reporte Consolidado',
];

$tituloReporte = $titulos[$type] ?? ('Reporte ' . strtoupper($type));
$fechaGenerado = date('d/m/Y H:i');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($tituloReporte, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
        }

        .viewer {
            padding: 0;
            display: block;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            overflow: hidden;
        }

        .sheet-inner {
            padding: 16mm 15mm 18mm;
        }

        .doc-head {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .doc-kicker {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
        }

        .doc-title {
            margin: 0;
            font-size: 24px;
            line-height: 1.15;
            font-weight: 700;
            color: #0f172a;
        }

        .doc-meta {
            margin-top: 8px;
            font-size: 12px;
            color: #475569;
        }

        .box {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            background: #f8fafc;
        }

        .row {
            display: flex;
            gap: 10px;
            padding: 6px 0;
            font-size: 14px;
            line-height: 1.45;
        }

        .label {
            width: 120px;
            min-width: 120px;
            font-weight: 700;
            color: #0f172a;
        }

        .value {
            flex: 1;
            color: #334155;
        }

        .note {
            margin-top: 14px;
            font-size: 13px;
            color: #64748b;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="viewer">
        <main class="sheet">
            <div class="sheet-inner">
                <header class="doc-head">
                    <div class="doc-kicker">Reporte administrativo</div>
                    <h1 class="doc-title"><?= htmlspecialchars($tituloReporte, ENT_QUOTES, 'UTF-8') ?></h1>
                    <div class="doc-meta">Generado: <?= htmlspecialchars($fechaGenerado, ENT_QUOTES, 'UTF-8') ?></div>
                </header>

                <section class="box">
                    <div class="row">
                        <div class="label">Tipo</div>
                        <div class="value"><?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="row">
                        <div class="label">Estado</div>
                        <div class="value">Maqueta inicial</div>
                    </div>

                    <div class="row">
                        <div class="label">Formato</div>
                        <div class="value">A4 vertical</div>
                    </div>

                    <div class="note">
                        Aquí irá el contenido real del reporte cuando conectes la base de datos.
                    </div>
                </section>

                <div class="footer">
                    <?= defined('APP_NAME') ? htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') : 'Sistema' ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>