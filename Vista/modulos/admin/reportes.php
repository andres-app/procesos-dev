<?php
// Vista/modulos/admin/reportes.php (MAQUETA / SIN BD)
// Módulo: Reportes (solo cards / sin data)
// Apple-like UI + responsive desktop
$titulo = 'Reportes';
$active = 'reportes';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/**
 * Reportes disponibles (SOLO CARDS)
 * - sin data
 * - cada card con acciones: Excel / PDF
 *
 * Nota de rutas:
 * - Admin usa: /admin/{accion}/{reporte}
 *   Ej: /admin/export_excel/estado
 */
$reportes = [
    [
        'key'   => 'estado',
        'title' => 'Reporte por Estado',
        'desc'  => 'Agrupa y exporta información por estado del proceso/PAC.',
        'icon'  => '📌',
        'tone'  => 'green',
    ],
    [
        'key'   => 'derivados',
        'title' => 'Reporte de Derivados (OBAC)',
        'desc'  => 'Resumen por OBAC (EP, FAP, MGP, CCFFAA, etc.).',
        'icon'  => '🧭',
        'tone'  => 'green',
    ],
    [
        'key'   => 'inversiones',
        'title' => 'Reporte de Inversiones',
        'desc'  => 'Filtra y exporta lo marcado como inversiones.',
        'icon'  => '🏗️',
        'tone'  => 'green',
    ],
    [
        'key'   => 'consolidado',
        'title' => 'Reporte Consolidado',
        'desc'  => 'Vista general para exportación rápida (resumen total).',
        'icon'  => '📊',
        'tone'  => 'green',
    ],
];

function pill($txt, $tone = 'slate')
{
    $map = [
        'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
        'green' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
        'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];
    $c = $map[$tone] ?? $map['slate'];
    return '<span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium ' . $c . '">' . h($txt) . '</span>';
}
?>

<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="text-xs text-slate-500">Administrador</div>
            <h1 class="text-xl font-semibold tracking-tight leading-tight">Reportes</h1>
            <div class="text-xs text-slate-500">Selecciona un reporte y exporta a Excel o PDF (maqueta)</div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative">
                <input
                    id="q"
                    placeholder="Buscar reporte…"
                    class="w-full sm:w-80 border border-slate-200 bg-white outline-none focus:ring-2 focus:ring-slate-200 inp" />
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">⌕</span>
            </div>

            <button class="btn btn-ghost border border-slate-200 bg-white" onclick="alert('Aquí puedes abrir filtros globales (maqueta).')">Filtros</button>
        </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3" id="gridReportes">

        <?php foreach ($reportes as $r): ?>
            <div class="rep-card border border-slate-200 bg-white shadow-soft overflow-hidden" data-card data-q="<?= h(strtoupper($r['title'] . ' ' . $r['desc'] . ' ' . $r['key'])) ?>">
                <div class="px-4 py-4 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="rep-ico"><?= h($r['icon']) ?></div>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-900 truncate"><?= h($r['title']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?= h($r['desc']) ?></div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <?= pill('Key: ' . $r['key'], $r['tone']) ?>
                            <?= pill('Exportable', 'slate') ?>
                        </div>
                    </div>

                    <button class="iconbtn" title="Ver" aria-label="Ver reporte <?= h($r['key']) ?>"
                        onclick="openPreview('<?= h($r['title']) ?>','<?= h($r['desc']) ?>','<?= h($r['key']) ?>')">
                        <svg viewBox="0 0 24 24" class="ico" aria-hidden="true">
                            <path fill="currentColor" d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2.5A2.5 2.5 0 1 0 12 9a2.5 2.5 0 0 0 0 5z" />
                        </svg>
                    </button>
                </div>

                <div class="px-4 pb-4 pt-0">
                    <div class="rep-actions">
                        <a class="btn btn-ghost border border-slate-200 bg-white w-full text-center"
                            href="<?= BASE_URL ?>/admin/export_excel/<?= h($r['key']) ?>">
                            Exportar Excel
                        </a>

                        <a class="btn btn-primary w-full text-center"
                            href="<?= BASE_URL ?>/admin/export_pdf/<?= h($r['key']) ?>"
                            target="_blank" rel="noopener">
                            Exportar PDF
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<!-- Modal Preview -->
<div id="modalPreview" class="fixed inset-0 hidden items-center justify-center p-4 z-50">
    <div class="absolute inset-0 bg-slate-900/30" onclick="closeModal('modalPreview')"></div>

    <div class="relative w-full max-w-xl rounded-3xl border border-slate-200 glass shadow-soft overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500">Reporte</div>
                <div id="pv_title" class="text-lg font-semibold">—</div>
            </div>
            <button
                type="button"
                class="rounded-2xl border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50"
                onclick="closeModal('modalPreview')">Cerrar</button>
        </div>

        <div class="p-5 space-y-3 text-sm text-slate-800">
            <div id="pv_desc" class="text-slate-700">—</div>

            <div class="mini">
                <div class="text-xs text-slate-500">Acciones</div>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <a id="pv_excel" class="btn btn-ghost border border-slate-200 bg-white w-full text-center" href="#">Exportar Excel</a>
                    <a id="pv_pdf" class="btn btn-primary w-full text-center" href="#" target="_blank" rel="noopener">Exportar PDF</a>
                </div>
            </div>

            <div class="text-[11px] text-slate-500">
                <span id="pv_key" class="font-semibold text-slate-700">—</span>
            </div>
        </div>
    </div>
</div>

<style>
    .glass {
        background: rgba(255, 255, 255, .78);
        backdrop-filter: blur(10px);
    }

    .shadow-soft {
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
    }

    .btn {
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background: #0f172a;
        color: #fff;
        border: 1px solid #0f172a;
    }

    .btn-primary:hover {
        background: #111827;
    }

    .btn-ghost:hover {
        background: #f8fafc;
    }

    .inp {
        border-radius: 14px;
        padding: 9px 12px;
        font-size: 13px;
    }

    .rep-card {
        border-radius: 18px;
    }

    .rep-ico {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: rgba(15, 23, 42, .06);
        font-size: 18px;
    }

    .rep-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    @media(min-width:640px) {
        .rep-actions {
            grid-template-columns: 1fr 1fr;
        }
    }

    .mini {
        border-radius: 16px;
        border: 1px solid rgb(241, 245, 249);
        background: rgba(248, 250, 252, .7);
        padding: 10px 12px;
    }

    /* Icon actions (sutil + compacto) */
    .iconbtn {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, .45);
        background: rgba(255, 255, 255, .9);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: rgb(71, 85, 105);
        transition: background .15s ease, border-color .15s ease, color .15s ease, transform .05s ease;
        flex: 0 0 auto;
    }

    .iconbtn:hover {
        background: rgb(248, 250, 252);
        border-color: rgba(148, 163, 184, .7);
        color: rgb(15, 23, 42);
    }

    .iconbtn:active {
        transform: scale(.98);
    }

    .iconbtn:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(148, 163, 184, .35);
    }

    .iconbtn .ico {
        width: 16px;
        height: 16px;
        display: block;
    }
</style>

<script>
    const $ = (id) => document.getElementById(id);

    function openModal(id) {
        const el = $(id);
        if (!el) return;
        el.classList.remove('hidden');
        el.classList.add('flex');
    }

    function closeModal(id) {
        const el = $(id);
        if (!el) return;
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    // Buscar cards
    $('q')?.addEventListener('input', () => {
        const term = ($('q').value || '').trim().toUpperCase();
        document.querySelectorAll('[data-card]').forEach(card => {
            const hay = (card.getAttribute('data-q') || '').toUpperCase();
            card.style.display = (!term || hay.includes(term)) ? '' : 'none';
        });
    });

    // Preview
    function openPreview(title, desc, key) {
        $('pv_title').textContent = title || '—';
        $('pv_desc').textContent = desc || '—';
        $('pv_key').textContent = key || '—';

        const excel = `<?= rtrim(BASE_URL, '/') ?>/admin/export_excel/${encodeURIComponent(key)}`;
        const pdf = `<?= rtrim(BASE_URL, '/') ?>/admin/export_pdf/${encodeURIComponent(key)}`;

        $('pv_excel').setAttribute('href', excel);
        $('pv_pdf').setAttribute('href', pdf);

        openModal('modalPreview');
    }
    window.openPreview = openPreview;
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>