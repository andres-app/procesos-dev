<?php
// Controlador/CtrActividadPac.php
require_once __DIR__ . '/../Modelo/MdActividadPac.php';

class CtrPacActividad
{
    public static function guardar(): void
    {
        $pacId      = (int)($_POST['pac_id'] ?? 0);
        $tipoActividadId = (int)($_POST['tipo_actividad_id'] ?? 0);
        $fecha      = trim((string)($_POST['fecha'] ?? ''));
        $comentario = trim((string)($_POST['comentario'] ?? ''));


        if ($pacId <= 0) {
            header('Location: ' . BASE_URL . '/admin/pac');
            exit;
        }

        if ($tipoActividadId <= 0 || $fecha === '') {
            header('Location: ' . BASE_URL . '/admin/pac_detalle?id=' . $pacId . '&error=1');
            exit;
        }

        MdPacActividad::guardar([
            'pac_id'            => $pacId,
            'tipo_actividad_id' => $tipoActividadId,
            'fecha'             => $fecha,
            'comentario'        => $comentario,
        ]);

        header('Location: ' . BASE_URL . '/admin/pac_detalle?id=' . $pacId . '&ok=1');
        exit;
    }
}
