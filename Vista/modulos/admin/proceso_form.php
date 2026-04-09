<?php
$titulo = !empty($proceso['id']) ? 'Editar proceso' : 'Nuevo proceso';
$active = 'procesos';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$esEdicion = !empty($proceso['id']);
$procesoId = (int)($proceso['id'] ?? 0);

$valCodigo            = $proceso['codigo_proceso'] ?? '';
$valTipoProceso       = strtoupper((string)($proceso['tipo_proceso'] ?? 'INDIVIDUAL'));
$valExpediente        = $proceso['expediente'] ?? '';
$valDescripcion       = $proceso['descripcion'] ?? '';
$valConvocatoria      = !empty($proceso['convocatoria']) ? date('Y-m-d', strtotime((string)$proceso['convocatoria'])) : date('Y-m-d');
$valAnioConvocatoria  = $proceso['anio_convocatoria'] ?? date('Y');
$valPeriodo           = $proceso['periodo'] ?? date('Y');
$valMoneda            = $proceso['moneda'] ?? 'PEN';
$valFechaRegistro     = !empty($proceso['fecha_registro']) ? date('Y-m-d', strtotime((string)$proceso['fecha_registro'])) : date('Y-m-d');
$valEstimado          = (float)($proceso['estimado'] ?? 0);

$pacIdsSeleccionados = $pacIdsSeleccionados ?? [];
$pacsVinculados = $pacsVinculados ?? [];

$pacsIniciales = [];
if (!empty($pacsVinculados)) {
    foreach ($pacsVinculados as $pac) {
        $idPac = (int)($pac['id'] ?? 0);
        if ($idPac <= 0) continue;

        $pacsIniciales[] = [
            'id'          => (string)$idPac,
            'nopac'       => (string)($pac['nopac'] ?? ''),
            'pn'          => (string)($pac['pn'] ?? ''),
            'descripcion' => (string)($pac['descripcion'] ?? ''),
            'obac'        => (string)($pac['obac_nombre'] ?? ''),
            'estado'      => (string)($pac['estado_nombre'] ?? ''),
            'estimado'    => (float)($pac['estimado'] ?? 0),
        ];
    }
}
?>

<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-xs text-slate-500">Mantenimiento</div>
            <h1 class="text-xl font-semibold text-slate-900">
                <?= $esEdicion ? 'Editar proceso' : 'Nuevo proceso' ?>
            </h1>
        </div>

        <a
            href="<?= BASE_URL ?>/admin/procesos"
            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Volver
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-8">
            <div id="step1" class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Asistente de registro
                    </div>
                    <h2 class="mt-1 text-lg font-semibold text-slate-900">
                        Paso 1. Selecciona el tipo y los PAC
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Primero define si el proceso será individual o corporativo. Luego selecciona los PAC que se vincularán.
                    </p>
                </div>

                <input type="hidden" id="tipo_proceso" value="<?= h($valTipoProceso) ?>">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <button
                        type="button"
                        id="cardTipoIndividual"
                        class="tipo-card rounded-2xl border border-slate-200 bg-white p-5 text-left transition hover:shadow-sm"
                        onclick="setTipoProceso('INDIVIDUAL')">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Individual</div>
                        <div class="mt-1 text-base font-semibold text-slate-900">1 PAC</div>
                        <div class="mt-2 text-sm text-slate-500">
                            Proceso asociado a un único PAC.
                        </div>
                    </button>

                    <button
                        type="button"
                        id="cardTipoCorporativo"
                        class="tipo-card rounded-2xl border border-slate-200 bg-white p-5 text-left transition hover:shadow-sm"
                        onclick="setTipoProceso('CORPORATIVO')">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Corporativo</div>
                        <div class="mt-1 text-base font-semibold text-slate-900">2 o más PAC</div>
                        <div class="mt-2 text-sm text-slate-500">
                            Proceso que agrupa varios PAC.
                        </div>
                    </button>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">PAC vinculados</div>
                            <div class="mt-1 text-xs text-slate-500">
                                Selecciona los PAC que formarán parte del proceso.
                            </div>
                        </div>

                        <button
                            type="button"
                            id="btnAbrirModalPac"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Seleccionar PAC
                        </button>
                    </div>

                    <div id="pacsChips" class="mt-4 flex flex-wrap gap-2"></div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                    <table class="min-w-full text-left">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                <th class="px-4 py-3">N° PAC</th>
                                <th class="px-4 py-3">Descripción</th>
                                <th class="px-4 py-3">OBAC</th>
                                <th class="px-4 py-3 text-right">Estimado</th>
                                <th class="px-4 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPacsSeleccionados" class="divide-y divide-slate-100">
                            <tr id="filaSinPac">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Aún no has agregado PAC al proceso.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <button
                        type="button"
                        id="btnIrPaso2"
                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                        Continuar
                    </button>
                </div>
            </div>

            <div id="step2" class="hidden space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Paso 2
                    </div>
                    <h2 class="mt-1 text-lg font-semibold text-slate-900">
                        Completa los datos del proceso
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Registra la información general del proceso en base a los PAC seleccionados.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <form id="procesoForm" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                        <input type="hidden" id="proceso_id" value="<?= $procesoId ?>">

                        <div class="md:col-span-4">
                            <label class="mb-1.5 block text-xs text-slate-500">Código proceso</label>
                            <input
                                id="codigo_proceso"
                                type="text"
                                value="<?= h($valCodigo) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                                placeholder="Ej: PROC-2026-001">
                        </div>

                        <div class="md:col-span-4">
                            <label class="mb-1.5 block text-xs text-slate-500">Expediente</label>
                            <input
                                id="expediente"
                                type="text"
                                value="<?= h($valExpediente) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                                placeholder="Expediente">
                        </div>

                        <div class="md:col-span-4">
                            <label class="mb-1.5 block text-xs text-slate-500">Fecha convocatoria</label>
                            <input
                                id="convocatoria"
                                type="date"
                                value="<?= h($valConvocatoria) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs text-slate-500">Año convocatoria</label>
                            <input
                                id="anio_convocatoria"
                                type="number"
                                value="<?= h((string)$valAnioConvocatoria) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                                placeholder="2026">
                        </div>

                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs text-slate-500">Periodo</label>
                            <input
                                id="periodo"
                                type="number"
                                value="<?= h((string)$valPeriodo) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                                placeholder="2026">
                        </div>

                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs text-slate-500">Moneda</label>
                            <input
                                id="moneda"
                                type="text"
                                value="<?= h($valMoneda) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                        </div>

                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs text-slate-500">Fecha registro</label>
                            <input
                                id="fecha_registro"
                                type="date"
                                value="<?= h($valFechaRegistro) ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                        </div>

                        <div class="md:col-span-4">
                            <label class="mb-1.5 block text-xs text-slate-500">Estimado total</label>
                            <input
                                id="estimado"
                                type="number"
                                step="0.01"
                                min="0"
                                readonly
                                value="<?= number_format($valEstimado, 2, '.', '') ?>"
                                class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700"
                                placeholder="0.00">
                        </div>

                        <div class="md:col-span-12">
                            <label class="mb-1.5 block text-xs text-slate-500">Descripción</label>
                            <textarea
                                id="descripcion"
                                rows="4"
                                class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm"
                                placeholder="Descripción del proceso"><?= h($valDescripcion) ?></textarea>
                        </div>
                    </form>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <button
                        type="button"
                        id="btnVolverPaso1"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Volver
                    </button>

                    <div class="flex items-center gap-2">
                        <a
                            href="<?= BASE_URL ?>/admin/procesos"
                            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Cancelar
                        </a>

                        <button
                            type="button"
                            id="btnGuardarProceso"
                            class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                            <?= $esEdicion ? 'Actualizar proceso' : 'Guardar proceso' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="text-sm font-semibold text-slate-900">Resumen del proceso</div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500">Tipo</div>
                            <div id="resumenTipoProceso" class="mt-1 text-sm font-semibold text-slate-900">
                                <?= h($valTipoProceso) ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500">PAC seleccionados</div>
                            <div id="pacSeleccionadosInfo" class="mt-1 text-sm font-semibold text-slate-900">0</div>
                        </div>

                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500">Estimado acumulado</div>
                            <div id="estimadoResumen" class="mt-1 text-sm font-semibold text-slate-900">S/ 0.00</div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-slate-500">Regla actual</div>
                        <div id="reglaProceso" class="mt-1 text-sm font-medium text-slate-700">
                            Solo 1 PAC permitido
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalPac" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40" id="overlayModalPac"></div>

    <div class="relative flex h-[88vh] w-full max-w-7xl flex-col overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl">
        <div class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400">Proceso</div>
                <div class="mt-1 text-[28px] font-semibold leading-none text-slate-900">Seleccionar PAC</div>
                <div class="mt-2 text-sm text-slate-500">
                    Marca los PAC que deseas asociar al proceso.
                </div>
            </div>

            <button
                type="button"
                id="btnCerrarModalPacTop"
                class="rounded-full border border-slate-200 bg-white px-5 py-2 text-sm text-slate-700 transition hover:bg-slate-50">
                Cerrar
            </button>
        </div>

        <div class="shrink-0 border-b border-slate-200 bg-slate-50 px-5 py-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="md:col-span-5">
                    <label class="mb-1.5 block text-xs text-slate-500">Buscar</label>
                    <input
                        id="filtroPacBuscar"
                        type="text"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm"
                        placeholder="N° PAC, descripción u OBAC">
                </div>

                <div class="md:col-span-3">
                    <label class="mb-1.5 block text-xs text-slate-500">OBAC</label>
                    <input
                        id="filtroPacObac"
                        type="text"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm"
                        placeholder="Ej: FAP">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-xs text-slate-500">Periodo</label>
                    <input
                        id="filtroPacPeriodo"
                        type="text"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm"
                        placeholder="2026">
                </div>

                <div class="md:col-span-2 flex items-end">
                    <button
                        type="button"
                        id="btnLimpiarFiltroPac"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-auto">
            <table class="min-w-full text-left">
                <thead class="sticky top-0 bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                        <th class="px-4 py-3"></th>
                        <th class="px-4 py-3">N° PAC</th>
                        <th class="px-4 py-3">P/NP</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3">OBAC</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Estimado</th>
                    </tr>
                </thead>
                <tbody id="tablaPacModal" class="divide-y divide-slate-100">
                    <?php foreach ($pacsDisponibles as $pac): ?>
                        <?php
                            $pacId = (int)($pac['id'] ?? 0);
                            $estaSeleccionado = in_array($pacId, $pacIdsSeleccionados, true);
                            $yaVinculado = (int)($pac['ya_vinculado'] ?? 0) === 1 && !$estaSeleccionado;
                        ?>
                        <tr
                            class="pac-row <?= $yaVinculado ? 'bg-slate-50 opacity-60' : 'hover:bg-slate-50' ?>"
                            data-id="<?= $pacId ?>"
                            data-nopac="<?= h($pac['nopac']) ?>"
                            data-pn="<?= h($pac['pn']) ?>"
                            data-desc="<?= h($pac['descripcion']) ?>"
                            data-obac="<?= h($pac['obac_nombre']) ?>"
                            data-estado="<?= h($pac['estado_nombre']) ?>"
                            data-estimado="<?= (float)$pac['estimado'] ?>"
                            data-periodo="<?= h($pac['periodo'] ?? '') ?>">
                            <td class="px-4 py-3">
                                <?php if ($yaVinculado): ?>
                                    <span class="text-xs font-semibold text-rose-600">Usado</span>
                                <?php else: ?>
                                    <input
                                        type="checkbox"
                                        class="pac-check-modal h-4 w-4"
                                        value="<?= $pacId ?>"
                                        <?= $estaSeleccionado ? 'checked' : '' ?>>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900"><?= h($pac['nopac']) ?></td>
                            <td class="px-4 py-3"><?= h($pac['pn']) ?></td>
                            <td class="px-4 py-3"><?= h($pac['descripcion']) ?></td>
                            <td class="px-4 py-3"><?= h($pac['obac_nombre']) ?></td>
                            <td class="px-4 py-3"><?= h($pac['estado_nombre']) ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                S/ <?= number_format((float)$pac['estimado'], 2, '.', ',') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="shrink-0 border-t border-slate-200 bg-white px-5 py-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-slate-600">
                    Seleccionados en el modal:
                    <span id="contadorModalPac" class="font-semibold text-slate-900">0</span>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        id="btnCerrarModalPac"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancelar
                    </button>

                    <button
                        type="button"
                        id="btnConfirmarPac"
                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                        Agregar seleccionados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.showToast !== 'function') {
        window.showToast = function (message, type = 'info', title = '') {
            const old = document.getElementById('global-toast-fallback');
            if (old) old.remove();

            const toast = document.createElement('div');
            toast.id = 'global-toast-fallback';

            const bgMap = {
                success: 'background:#065f46;color:#ffffff;border:1px solid #10b981;',
                error: 'background:#7f1d1d;color:#ffffff;border:1px solid #ef4444;',
                info: 'background:#0f172a;color:#ffffff;border:1px solid #334155;'
            };

            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 99999;
                min-width: 280px;
                max-width: 420px;
                padding: 14px 16px;
                border-radius: 14px;
                box-shadow: 0 18px 45px rgba(15,23,42,.18);
                font-family: inherit;
                ${bgMap[type] || bgMap.info}
            `;

            toast.innerHTML = `
                <div style="font-size:13px;font-weight:700;line-height:1.2;">
                    ${title ? title : (type === 'error' ? 'Error' : type === 'success' ? 'Correcto' : 'Aviso')}
                </div>
                <div style="margin-top:4px;font-size:13px;line-height:1.45;">
                    ${message}
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.transition = 'opacity .25s ease, transform .25s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-6px)';
            }, 2600);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        };
    }
</script>

<script>
    const pacSeleccionados = new Map();
    const pacsIniciales = <?= json_encode($pacsIniciales, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    pacsIniciales.forEach(pac => {
        pacSeleccionados.set(String(pac.id), pac);
    });

    function formatMoney(value) {
        const n = Number(value || 0);
        return 'S/ ' + n.toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function getTipoProceso() {
        const el = document.getElementById('tipo_proceso');
        return el ? el.value : 'INDIVIDUAL';
    }

    function openModalPac() {
        const modal = document.getElementById('modalPac');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        syncChecksModal();
        actualizarContadorModal();
    }

    function closeModalPac() {
        const modal = document.getElementById('modalPac');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function syncChecksModal() {
        document.querySelectorAll('.pac-check-modal').forEach(chk => {
            chk.checked = pacSeleccionados.has(String(chk.value));
        });
    }

    function actualizarContadorModal() {
        const total = document.querySelectorAll('.pac-check-modal:checked').length;
        document.getElementById('contadorModalPac').textContent = String(total);
    }

    function renderPacsChips() {
        const wrap = document.getElementById('pacsChips');
        if (!wrap) return;

        wrap.innerHTML = '';

        if (pacSeleccionados.size === 0) {
            wrap.innerHTML = `
                <div class="rounded-full border border-dashed border-slate-300 px-3 py-1.5 text-xs text-slate-500">
                    Ningún PAC seleccionado
                </div>
            `;
            return;
        }

        pacSeleccionados.forEach((pac) => {
            const chip = document.createElement('div');
            chip.className = 'inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700';
            chip.innerHTML = `
                <span class="font-semibold">${pac.nopac}</span>
                <span class="text-slate-400">•</span>
                <span>${pac.obac}</span>
            `;
            wrap.appendChild(chip);
        });
    }

    function actualizarResumen() {
        const totalPac = pacSeleccionados.size;
        let totalEstimado = 0;

        pacSeleccionados.forEach(pac => {
            totalEstimado += Number(pac.estimado || 0);
        });

        document.getElementById('pacSeleccionadosInfo').textContent = String(totalPac);
        document.getElementById('estimadoResumen').textContent = formatMoney(totalEstimado);
        document.getElementById('estimado').value = totalEstimado.toFixed(2);
        document.getElementById('resumenTipoProceso').textContent = getTipoProceso();

        renderPacsChips();
    }

    function renderPacsSeleccionados() {
        const tbody = document.getElementById('tablaPacsSeleccionados');
        tbody.innerHTML = '';

        if (pacSeleccionados.size === 0) {
            tbody.innerHTML = `
                <tr id="filaSinPac">
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                        Aún no has agregado PAC al proceso.
                    </td>
                </tr>
            `;
            actualizarResumen();
            return;
        }

        pacSeleccionados.forEach((pac, id) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50';
            tr.innerHTML = `
                <td class="px-4 py-3 font-semibold text-slate-900">${pac.nopac}</td>
                <td class="px-4 py-3">${pac.descripcion}</td>
                <td class="px-4 py-3">${pac.obac}</td>
                <td class="px-4 py-3 text-right font-semibold text-slate-900">${formatMoney(pac.estimado)}</td>
                <td class="px-4 py-3 text-right">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100"
                        onclick="quitarPacSeleccionado('${id}')">
                        Quitar
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        actualizarResumen();
    }

    function quitarPacSeleccionado(id) {
        pacSeleccionados.delete(String(id));
        renderPacsSeleccionados();
        syncChecksModal();
        actualizarContadorModal();
    }
    window.quitarPacSeleccionado = quitarPacSeleccionado;

    function confirmarSeleccionPac() {
        const tipo = getTipoProceso();
        const seleccionadosModal = Array.from(document.querySelectorAll('.pac-check-modal:checked'));

        if (tipo === 'INDIVIDUAL' && seleccionadosModal.length > 1) {
            showToast('Un proceso individual solo puede tener 1 PAC.', 'error', 'Error');
            return;
        }

        if (tipo === 'INDIVIDUAL') {
            pacSeleccionados.clear();
        }

        seleccionadosModal.forEach(chk => {
            const row = chk.closest('.pac-row');
            const id = String(chk.value);

            pacSeleccionados.set(id, {
                id,
                nopac: row.dataset.nopac || '',
                pn: row.dataset.pn || '',
                descripcion: row.dataset.desc || '',
                obac: row.dataset.obac || '',
                estado: row.dataset.estado || '',
                estimado: Number(row.dataset.estimado || 0),
            });
        });

        if (tipo === 'INDIVIDUAL' && pacSeleccionados.size > 1) {
            const firstKey = pacSeleccionados.keys().next().value;
            const firstPac = pacSeleccionados.get(firstKey);
            pacSeleccionados.clear();
            if (firstPac) {
                pacSeleccionados.set(firstKey, firstPac);
            }
        }

        renderPacsSeleccionados();
        closeModalPac();
    }

    function aplicarFiltroPacModal() {
        const q = (document.getElementById('filtroPacBuscar').value || '').toLowerCase().trim();
        const obac = (document.getElementById('filtroPacObac').value || '').toLowerCase().trim();
        const periodo = (document.getElementById('filtroPacPeriodo').value || '').toLowerCase().trim();

        document.querySelectorAll('.pac-row').forEach(row => {
            const texto = [
                row.dataset.nopac || '',
                row.dataset.desc || '',
                row.dataset.obac || '',
                row.dataset.estado || '',
                row.dataset.pn || '',
            ].join(' ').toLowerCase();

            const rowObac = (row.dataset.obac || '').toLowerCase();
            const rowPeriodo = String(row.dataset.periodo || '').toLowerCase();

            const okQ = !q || texto.includes(q);
            const okObac = !obac || rowObac.includes(obac);
            const okPeriodo = !periodo || rowPeriodo.includes(periodo);

            row.style.display = (okQ && okObac && okPeriodo) ? '' : 'none';
        });
    }

    function limpiarFiltroPacModal() {
        document.getElementById('filtroPacBuscar').value = '';
        document.getElementById('filtroPacObac').value = '';
        document.getElementById('filtroPacPeriodo').value = '';
        aplicarFiltroPacModal();
    }

    function activarPaso(numero) {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');

        if (numero === 1) {
            step1.classList.remove('hidden');
            step2.classList.add('hidden');
            return;
        }

        step1.classList.add('hidden');
        step2.classList.remove('hidden');
    }

    function validarPaso1() {
        const tipo = getTipoProceso();
        const total = pacSeleccionados.size;

        if (tipo === 'INDIVIDUAL' && total !== 1) {
            showToast('Un proceso individual debe tener exactamente 1 PAC.', 'error', 'Error');
            return false;
        }

        if (tipo === 'CORPORATIVO' && total < 2) {
            showToast('Un proceso corporativo debe tener al menos 2 PAC.', 'error', 'Error');
            return false;
        }

        return true;
    }

    function setTipoProceso(tipo) {
        const inputTipo = document.getElementById('tipo_proceso');
        if (inputTipo) inputTipo.value = tipo;

        const cardIndividual = document.getElementById('cardTipoIndividual');
        const cardCorporativo = document.getElementById('cardTipoCorporativo');

        [cardIndividual, cardCorporativo].forEach(card => {
            if (!card) return;

            card.classList.remove('border-slate-900', 'bg-slate-900');
            card.classList.add('border-slate-200', 'bg-white');

            card.querySelectorAll('div').forEach(el => {
                el.classList.remove('text-white', 'text-slate-300');
                if (el.textContent.trim() === 'Individual' || el.textContent.trim() === 'Corporativo') {
                    el.classList.add('text-slate-500');
                    el.classList.remove('text-slate-900');
                }
            });

            const nodes = card.querySelectorAll('div');
            if (nodes[0]) {
                nodes[0].classList.remove('text-white', 'text-slate-300');
                nodes[0].classList.add('text-slate-500');
            }
            if (nodes[1]) {
                nodes[1].classList.remove('text-white', 'text-slate-300');
                nodes[1].classList.add('text-slate-900');
            }
            if (nodes[2]) {
                nodes[2].classList.remove('text-white', 'text-slate-300');
                nodes[2].classList.add('text-slate-500');
            }
        });

        const activa = tipo === 'INDIVIDUAL' ? cardIndividual : cardCorporativo;
        if (activa) {
            activa.classList.remove('border-slate-200', 'bg-white');
            activa.classList.add('border-slate-900', 'bg-slate-900');

            const nodes = activa.querySelectorAll('div');
            if (nodes[0]) {
                nodes[0].classList.remove('text-slate-500');
                nodes[0].classList.add('text-slate-300');
            }
            if (nodes[1]) {
                nodes[1].classList.remove('text-slate-900');
                nodes[1].classList.add('text-white');
            }
            if (nodes[2]) {
                nodes[2].classList.remove('text-slate-500');
                nodes[2].classList.add('text-slate-300');
            }
        }

        const regla = document.getElementById('reglaProceso');
        if (regla) {
            regla.textContent = tipo === 'INDIVIDUAL'
                ? 'Solo 1 PAC permitido'
                : 'Debe seleccionar 2 o más PAC';
        }

        if (tipo === 'INDIVIDUAL' && pacSeleccionados.size > 1) {
            const firstKey = pacSeleccionados.keys().next().value;
            const firstPac = pacSeleccionados.get(firstKey);
            pacSeleccionados.clear();
            if (firstPac) {
                pacSeleccionados.set(firstKey, firstPac);
            }
            renderPacsSeleccionados();
            syncChecksModal();
            actualizarContadorModal();
        }

        actualizarResumen();
    }

    document.getElementById('btnAbrirModalPac')?.addEventListener('click', openModalPac);
    document.getElementById('btnCerrarModalPac')?.addEventListener('click', closeModalPac);
    document.getElementById('btnCerrarModalPacTop')?.addEventListener('click', closeModalPac);
    document.getElementById('overlayModalPac')?.addEventListener('click', closeModalPac);
    document.getElementById('btnConfirmarPac')?.addEventListener('click', confirmarSeleccionPac);

    document.querySelectorAll('.pac-check-modal').forEach(chk => {
        chk.addEventListener('change', (e) => {
            if (getTipoProceso() === 'INDIVIDUAL' && e.target.checked) {
                document.querySelectorAll('.pac-check-modal').forEach(other => {
                    if (other !== e.target) other.checked = false;
                });
            }
            actualizarContadorModal();
        });
    });

    document.getElementById('filtroPacBuscar')?.addEventListener('input', aplicarFiltroPacModal);
    document.getElementById('filtroPacObac')?.addEventListener('input', aplicarFiltroPacModal);
    document.getElementById('filtroPacPeriodo')?.addEventListener('input', aplicarFiltroPacModal);
    document.getElementById('btnLimpiarFiltroPac')?.addEventListener('click', limpiarFiltroPacModal);

    document.getElementById('btnIrPaso2')?.addEventListener('click', () => {
        if (!validarPaso1()) return;
        activarPaso(2);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('btnVolverPaso1')?.addEventListener('click', () => {
        activarPaso(1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('btnGuardarProceso')?.addEventListener('click', async () => {
        const btn = document.getElementById('btnGuardarProceso');
        const textoOriginal = btn.textContent;
        const pacIds = Array.from(pacSeleccionados.keys());
        const procesoId = document.getElementById('proceso_id').value;

        if (!validarPaso1()) {
            activarPaso(1);
            return;
        }

        if (!document.getElementById('codigo_proceso').value.trim()) {
            showToast('Debe ingresar el código del proceso.', 'error', 'Error');
            return;
        }

        if (!document.getElementById('descripcion').value.trim()) {
            showToast('Debe ingresar la descripción del proceso.', 'error', 'Error');
            return;
        }

        if (!document.getElementById('convocatoria').value) {
            showToast('Debe ingresar la fecha de convocatoria.', 'error', 'Error');
            return;
        }

        btn.disabled = true;
        btn.textContent = <?= json_encode($esEdicion ? 'Actualizando...' : 'Guardando...') ?>;

        const fd = new FormData();
        if (procesoId && Number(procesoId) > 0) {
            fd.append('id', procesoId);
        }

        fd.append('codigo_proceso', document.getElementById('codigo_proceso').value);
        fd.append('tipo_proceso', document.getElementById('tipo_proceso').value);
        fd.append('expediente', document.getElementById('expediente').value);
        fd.append('descripcion', document.getElementById('descripcion').value);
        fd.append('anio_convocatoria', document.getElementById('anio_convocatoria').value);
        fd.append('periodo', document.getElementById('periodo').value);
        fd.append('convocatoria', document.getElementById('convocatoria').value);
        fd.append('moneda', document.getElementById('moneda').value);
        fd.append('fecha_registro', document.getElementById('fecha_registro').value);
        fd.append('estimado', document.getElementById('estimado').value);

        pacIds.forEach(id => {
            fd.append('pac_ids[]', id);
        });

        try {
            const resp = await fetch('<?= BASE_URL ?>/admin/procesos_guardar', {
                method: 'POST',
                body: fd
            });

            const data = await resp.json();

            if (!data.ok) {
                showToast(data.msg || 'No se pudo guardar el proceso.', 'error', 'Error');
                btn.disabled = false;
                btn.textContent = textoOriginal;
                return;
            }

            showToast(
                data.msg || <?= json_encode($esEdicion ? 'Proceso actualizado correctamente.' : 'Proceso guardado correctamente.') ?>,
                'success',
                'Correcto'
            );

            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                window.location.href = '<?= BASE_URL ?>/admin/procesos';
            }, 700);
        } catch (e) {
            console.error(e);
            showToast('Error al guardar el proceso.', 'error', 'Error');
            btn.disabled = false;
            btn.textContent = textoOriginal;
        }
    });

    setTipoProceso(getTipoProceso());
    renderPacsSeleccionados();
    activarPaso(1);

    <?php if ($esEdicion): ?>
    if (pacSeleccionados.size > 0) {
        actualizarResumen();
    }
    <?php endif; ?>
</script>