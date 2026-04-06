<?php
// Controlador/CtrActividadAdmin.php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Modelo/MdProcesoAdmin.php';
require_once __DIR__ . '/../Modelo/MdActividadAdmin.php';

final class CtrActividadAdmin
{
    public static function index(): void
    {
        try {
            $procesoId = (int)($_GET['id'] ?? 0);

            if ($procesoId <= 0) {
                http_response_code(400);
                echo "ID de proceso inválido.";
                return;
            }

            $proceso = MdProcesoAdmin::obtener($procesoId);
            if (!$proceso) {
                http_response_code(404);
                echo "Proceso no encontrado.";
                return;
            }

            $actividades     = MdActividadAdmin::listarPorProceso($procesoId) ?? [];
            $pacs_vinculados = MdProcesoAdmin::obtenerPacsVinculados($procesoId) ?? [];

            // Normaliza claves que la vista puede usar
            $proceso['proceso'] = $proceso['codigo_proceso'] ?? '';
            $proceso['estado']  = $proceso['estado_nombre'] ?? '';

            require __DIR__ . '/../Vista/modulos/admin/procesos_detalle.php';
        } catch (Throwable $e) {
            http_response_code(500);
            echo "<pre style='white-space:pre-wrap'>"
                . "ERROR: " . $e->getMessage() . "\n\n"
                . $e->getFile() . ":" . $e->getLine() . "\n\n"
                . $e->getTraceAsString()
                . "</pre>";
        }
    }

    public static function guardar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $data = [
                'proceso_id'        => $_POST['proceso_id'] ?? 0,
                'tipo_actividad_id' => $_POST['tipo_actividad_id'] ?? null,
                'fecha'             => $_POST['fecha'] ?? '',
                'comentario'        => $_POST['comentario'] ?? '',
            ];

            $procesoId = (int)$data['proceso_id'];

            if ($procesoId <= 0) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Proceso inválido.'
                ]);
                exit;
            }

            $proceso = MdProcesoAdmin::obtener($procesoId);
            if (!$proceso) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'El proceso no existe.'
                ]);
                exit;
            }

            if (empty($data['tipo_actividad_id'])) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Debe seleccionar un tipo de actividad.'
                ]);
                exit;
            }

            if (empty($data['fecha'])) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Debe ingresar la fecha de la actividad.'
                ]);
                exit;
            }

            $actividadId = MdActividadAdmin::guardar($data);

            echo json_encode([
                'ok'           => true,
                'msg'          => 'Actividad registrada correctamente.',
                'actividad_id' => $actividadId,
                'redirect'     => BASE_URL . '/admin/procesos/detalle?id=' . $procesoId
            ]);
            exit;
        } catch (Throwable $e) {
            echo json_encode([
                'ok'  => false,
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }

    public static function eliminar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'ID de actividad inválido.'
                ]);
                exit;
            }

            $actividad = MdActividadAdmin::obtener($id);
            if (!$actividad) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Actividad no encontrada.'
                ]);
                exit;
            }

            $procesoId = (int)($actividad['proceso_id'] ?? 0);

            MdActividadAdmin::eliminar($id);

            echo json_encode([
                'ok'       => true,
                'msg'      => 'Actividad eliminada correctamente.',
                'redirect' => BASE_URL . '/admin/procesos/detalle?id=' . $procesoId
            ]);
            exit;
        } catch (Throwable $e) {
            echo json_encode([
                'ok'  => false,
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }

    public static function tipos(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $tipos = MdActividadAdmin::listarTiposActividad();

            echo json_encode([
                'ok'    => true,
                'items' => $tipos
            ]);
            exit;
        } catch (Throwable $e) {
            echo json_encode([
                'ok'  => false,
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }
}