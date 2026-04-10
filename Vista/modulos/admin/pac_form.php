<?php
$titulo = !empty($pac['id']) ? 'Editar PAC' : 'Nuevo PAC';
$active = 'pac';
require __DIR__ . '/../../layout/admin_layout.php';

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$esEdicion = !empty($pac['id']);
$pacId = (int)($pac['id'] ?? 0);

$valNopac        = $pac['nopac'] ?? '';
$valPn           = strtoupper((string)($pac['pn'] ?? 'NP'));
$valEstado       = $pac['estado'] ?? '';
$valDescripcion  = $pac['descripcion'] ?? '';
$valObac         = $pac['obac'] ?? '';
$valSeleccion    = $pac['seleccion'] ?? '';
$valFuente       = $pac['fuente'] ?? '';
$valEstimado     = number_format((float)($pac['estimado'] ?? 0), 2, '.', '');
$valPeriodo      = $pac['periodo'] ?? '';
$valLista        = $pac['lista'] ?? '';
$valEjecucion    = $pac['ejecucion'] ?? '';
$valModalidad    = $pac['modalidad'] ?? '';
$valDependencia  = $pac['dependencia'] ?? '';
$valMesconvoca   = $pac['mesconvoca'] ?? '';
$valCertificado  = number_format((float)($pac['certificado'] ?? 0), 2, '.', '');
$valTipoMercado  = $pac['tipo_mercado'] ?? '';
$valCantidad     = (int)($pac['cantidad'] ?? 0);
$valRubro        = $pac['rubro'] ?? '';
$valInversiones  = $pac['inversiones'] ?? '';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-xs text-slate-500">Mantenimiento</div>
            <h1 class="text-xl font-semibold text-slate-900">
                <?= $esEdicion ? 'Editar PAC' : 'Nuevo PAC' ?>
            </h1>
        </div>

        <a
            href="<?= BASE_URL ?>/admin/pac"
            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Volver
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Formulario PAC
                </div>
                <h2 class="mt-1 text-lg font-semibold text-slate-900">
                    <?= $esEdicion ? 'Actualiza la información del PAC' : 'Completa la información del nuevo PAC' ?>
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Registra los datos generales, clasificación y montos del PAC.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <form id="pacForm" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    <input type="hidden" id="pac_id" value="<?= $pacId ?>">

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">N° PAC *</label>
                        <input
                            id="nopac"
                            type="text"
                            value="<?= h($valNopac) ?>"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                            placeholder="Ej: 0024">
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">P / NP *</label>
                        <select
                            id="pn"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="P" <?= $valPn === 'P' ? 'selected' : '' ?>>P</option>
                            <option value="NP" <?= $valPn === 'NP' ? 'selected' : '' ?>>NP</option>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Estado</label>
                        <select
                            id="estado"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($estados as $es): ?>
                                <option value="<?= (int)$es['id'] ?>" <?= (string)$valEstado === (string)$es['id'] ? 'selected' : '' ?>>
                                    <?= h($es['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-12">
                        <label class="mb-1.5 block text-xs text-slate-500">Descripción *</label>
                        <textarea
                            id="descripcion"
                            rows="4"
                            class="w-full rounded-xl border border-slate-200 px-3 py-3 text-sm"
                            placeholder="Descripción del PAC"><?= h($valDescripcion) ?></textarea>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">OBAC *</label>
                        <select
                            id="obac"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($obacs as $o): ?>
                                <option value="<?= (int)$o['id'] ?>" <?= (string)$valObac === (string)$o['id'] ? 'selected' : '' ?>>
                                    <?= h($o['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Fuente</label>
                        <select
                            id="fuente"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($fuentes as $f): ?>
                                <option value="<?= (int)$f['id'] ?>" <?= (string)$valFuente === (string)$f['id'] ? 'selected' : '' ?>>
                                    <?= h($f['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Selección</label>
                        <select
                            id="seleccion"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($selecciones as $s): ?>
                                <option value="<?= (int)$s['id'] ?>" <?= (string)$valSeleccion === (string)$s['id'] ? 'selected' : '' ?>>
                                    <?= h($s['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Periodo</label>
                        <select
                            id="periodo"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($periodos as $p): ?>
                                <option value="<?= (int)$p['id'] ?>" <?= (string)$valPeriodo === (string)$p['id'] ? 'selected' : '' ?>>
                                    <?= h($p['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Lista</label>
                        <select
                            id="lista"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($listas as $l): ?>
                                <option value="<?= (int)$l['id'] ?>" <?= (string)$valLista === (string)$l['id'] ? 'selected' : '' ?>>
                                    <?= h($l['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Ejecución</label>
                        <select
                            id="ejecucion"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($entidades as $e): ?>
                                <option value="<?= (int)$e['id'] ?>" <?= (string)$valEjecucion === (string)$e['id'] ? 'selected' : '' ?>>
                                    <?= h($e['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Modalidad</label>
                        <select
                            id="modalidad"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($modalidades as $m): ?>
                                <option value="<?= (int)$m['id'] ?>" <?= (string)$valModalidad === (string)$m['id'] ? 'selected' : '' ?>>
                                    <?= h($m['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Dependencia</label>
                        <select
                            id="dependencia"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($dependencias as $d): ?>
                                <option value="<?= (int)$d['id'] ?>" <?= (string)$valDependencia === (string)$d['id'] ? 'selected' : '' ?>>
                                    <?= h($d['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Mes convocatoria</label>
                        <input
                            id="mesconvoca"
                            type="text"
                            value="<?= h($valMesconvoca) ?>"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm uppercase"
                            placeholder="Ej: MARZO">
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Tipo mercado</label>
                        <select
                            id="tipo_mercado"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($tipos_mercado as $tm): ?>
                                <option value="<?= (int)$tm['id'] ?>" <?= (string)$valTipoMercado === (string)$tm['id'] ? 'selected' : '' ?>>
                                    <?= h($tm['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="mb-1.5 block text-xs text-slate-500">Rubro</label>
                        <select
                            id="rubro"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm">
                            <option value="">Seleccionar</option>
                            <?php foreach ($rubros as $r): ?>
                                <option value="<?= (int)$r['id'] ?>" <?= (string)$valRubro === (string)$r['id'] ? 'selected' : '' ?>>
                                    <?= h($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1.5 block text-xs text-slate-500">Cantidad</label>
                        <input
                            id="cantidad"
                            type="number"
                            min="0"
                            value="<?= h((string)$valCantidad) ?>"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                            placeholder="0">
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1.5 block text-xs text-slate-500">Estimado</label>
                        <input
                            id="estimado"
                            type="number"
                            step="0.01"
                            min="0"
                            value="<?= h($valEstimado) ?>"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                            placeholder="0.00">
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1.5 block text-xs text-slate-500">Certificado</label>
                        <input
                            id="certificado"
                            type="number"
                            step="0.01"
                            min="0"
                            value="<?= h($valCertificado) ?>"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                            placeholder="0.00">
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1.5 block text-xs text-slate-500">Inversiones</label>
                        <input
                            id="inversiones"
                            type="text"
                            value="<?= h($valInversiones) ?>"
                            class="h-11 w-full rounded-xl border border-slate-200 px-3 text-sm"
                            placeholder="Ej: CUI 123456">
                    </div>
                </form>
            </div>

            <div class="flex items-center justify-between gap-2">
                <a
                    href="<?= BASE_URL ?>/admin/pac"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>

                <button
                    type="button"
                    id="btnGuardarPac"
                    class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                    <?= $esEdicion ? 'Actualizar PAC' : 'Guardar PAC' ?>
                </button>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="text-sm font-semibold text-slate-900">Resumen del PAC</div>

                    <div class="mt-4 space-y-4">
                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500">N° PAC</div>
                            <div id="resumenNopac" class="mt-1 text-sm font-semibold text-slate-900">
                                <?= h($valNopac ?: '-') ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500">Tipo</div>
                            <div id="resumenPn" class="mt-1 text-sm font-semibold text-slate-900">
                                <?= h($valPn) ?>
                            </div>
                        </div>

                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500">Monto estimado</div>
                            <div id="resumenEstimado" class="mt-1 text-sm font-semibold text-slate-900">
                                S/ <?= number_format((float)$valEstimado, 2, '.', ',') ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-[11px] uppercase tracking-wide text-slate-500">Estado del registro</div>
                        <div class="mt-1 text-sm font-medium text-slate-700">
                            <?= $esEdicion ? 'Edición de PAC existente' : 'Creación de nuevo PAC' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof window.showToast !== 'function') {
    window.showToast = function(message, type = 'info', title = '') {
        alert(message);
    };
}

function formatMoney(value) {
    const n = Number(value || 0);
    return 'S/ ' + n.toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function actualizarResumenPac() {
    const nopac = document.getElementById('nopac')?.value || '-';
    const pn = document.getElementById('pn')?.value || '-';
    const estimado = document.getElementById('estimado')?.value || 0;

    document.getElementById('resumenNopac').textContent = nopac;
    document.getElementById('resumenPn').textContent = pn;
    document.getElementById('resumenEstimado').textContent = formatMoney(estimado);
}

[
    'nopac', 'pn', 'estimado'
].forEach(id => {
    document.getElementById(id)?.addEventListener('input', actualizarResumenPac);
    document.getElementById(id)?.addEventListener('change', actualizarResumenPac);
});

document.getElementById('btnGuardarPac')?.addEventListener('click', async () => {
    const btn = document.getElementById('btnGuardarPac');
    const textoOriginal = btn.textContent;
    const pacId = document.getElementById('pac_id').value;

    if (!document.getElementById('nopac').value.trim()) {
        showToast('Debe ingresar el N° PAC.', 'error', 'Error');
        return;
    }

    if (!document.getElementById('descripcion').value.trim()) {
        showToast('Debe ingresar la descripción.', 'error', 'Error');
        return;
    }

    if (!document.getElementById('obac').value) {
        showToast('Debe seleccionar un OBAC.', 'error', 'Error');
        return;
    }

    btn.disabled = true;
    btn.textContent = <?= json_encode($esEdicion ? 'Actualizando...' : 'Guardando...') ?>;

    const fd = new FormData();

    if (pacId && Number(pacId) > 0) {
        fd.append('id', pacId);
    }

    fd.append('nopac', document.getElementById('nopac').value);
    fd.append('pn', document.getElementById('pn').value);
    fd.append('estado', document.getElementById('estado').value);
    fd.append('descripcion', document.getElementById('descripcion').value);
    fd.append('obac', document.getElementById('obac').value);
    fd.append('seleccion', document.getElementById('seleccion').value);
    fd.append('fuente', document.getElementById('fuente').value);
    fd.append('estimado', document.getElementById('estimado').value);
    fd.append('periodo', document.getElementById('periodo').value);
    fd.append('lista', document.getElementById('lista').value);
    fd.append('ejecucion', document.getElementById('ejecucion').value);
    fd.append('modalidad', document.getElementById('modalidad').value);
    fd.append('dependencia', document.getElementById('dependencia').value);
    fd.append('mesconvoca', document.getElementById('mesconvoca').value);
    fd.append('certificado', document.getElementById('certificado').value);
    fd.append('tipo_mercado', document.getElementById('tipo_mercado').value);
    fd.append('cantidad', document.getElementById('cantidad').value);
    fd.append('rubro', document.getElementById('rubro').value);
    fd.append('inversiones', document.getElementById('inversiones').value);

    try {
        const resp = await fetch('<?= BASE_URL ?>/admin/pac_guardar', {
            method: 'POST',
            body: fd
        });

        const data = await resp.json();

        if (!data.ok) {
            showToast(data.msg || 'No se pudo guardar el PAC.', 'error', 'Error');
            btn.disabled = false;
            btn.textContent = textoOriginal;
            return;
        }

        showToast(
            data.msg || <?= json_encode($esEdicion ? 'PAC actualizado correctamente.' : 'PAC guardado correctamente.') ?>,
            'success',
            'Correcto'
        );

        setTimeout(() => {
            window.location.href = '<?= BASE_URL ?>/admin/pac';
        }, 700);
    } catch (e) {
        console.error(e);
        showToast('Error al guardar el PAC.', 'error', 'Error');
        btn.disabled = false;
        btn.textContent = textoOriginal;
    }
});

actualizarResumenPac();
</script>

<?php require __DIR__ . '/../../layout/admin_footer.php'; ?>