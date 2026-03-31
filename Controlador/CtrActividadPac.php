<?php
// Controlador/CtrActividadPac.php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Modelo/MdActividadPac.php';
require_once __DIR__ . '/../Modelo/MdPacAdmin.php';

class CtrPacActividad
{
    public static function guardar(): void
    {
        $pacId           = (int)($_POST['pac_id'] ?? 0);
        $tipoActividadId = (int)($_POST['tipo_actividad_id'] ?? 0);
        $fecha           = trim((string)($_POST['fecha'] ?? ''));
        $comentario      = trim((string)($_POST['comentario'] ?? ''));

        if ($pacId <= 0) {
            header('Location: ' . BASE_URL . '/admin/pac');
            exit;
        }

        if ($tipoActividadId <= 0 || $fecha === '') {
            header('Location: ' . BASE_URL . '/admin/pac_detalle?id=' . $pacId . '&error=1&msg=' . urlencode('Completa la actividad y la fecha.'));
            exit;
        }

        $db = db();

        try {
            $db->beginTransaction();

            $okActividad = MdPacActividad::guardar([
                'pac_id'            => $pacId,
                'tipo_actividad_id' => $tipoActividadId,
                'fecha'             => $fecha,
                'comentario'        => $comentario,
            ], $db);

            if (!$okActividad) {
                throw new RuntimeException('No se pudo guardar la actividad.');
            }

            $okEstado = MdPacAdmin::actualizarEstadoDesdeTipoActividad($pacId, $tipoActividadId, $db);

            if (!$okEstado) {
                throw new RuntimeException('La actividad se guardó, pero no se pudo actualizar el estado del PAC.');
            }

            $db->commit();

            header('Location: ' . BASE_URL . '/admin/pac_detalle?id=' . $pacId . '&ok=1');
            exit;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            header('Location: ' . BASE_URL . '/admin/pac_detalle?id=' . $pacId . '&error=1&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }
}