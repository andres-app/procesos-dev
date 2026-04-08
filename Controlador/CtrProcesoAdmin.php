<?php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Modelo/MdProcesoAdmin.php';
require_once __DIR__ . '/../Modelo/MdActividadAdmin.php';

final class CtrProcesoAdmin
{
    public static function index(): void
    {
        $filtros = [
            'q'            => $_GET['q'] ?? '',
            'periodo'      => $_GET['periodo'] ?? '',
            'estado_id'    => $_GET['estado_id'] ?? '',
            'tipo_proceso' => $_GET['tipo_proceso'] ?? '',
        ];

        $rows = MdProcesoAdmin::listar($filtros);

        require __DIR__ . '/../Vista/modulos/admin/procesos.php';
    }

    public static function nuevo(): void
    {
        $estadosProceso = MdProcesoAdmin::listarEstadosProceso();

        $filtrosPac = [
            'q'       => $_GET['pac_q'] ?? '',
            'periodo' => $_GET['pac_periodo'] ?? '',
            'obac'    => $_GET['pac_obac'] ?? '',
        ];

        $pacsDisponibles = MdProcesoAdmin::listarPacsDisponibles($filtrosPac);

        $proceso = [
            'id'                => null,
            'codigo_proceso'    => '',
            'tipo_proceso'      => 'INDIVIDUAL',
            'expediente'        => '',
            'obac'              => '',
            'descripcion'       => '',
            'estimado'          => '',
            'estado_id'         => '',
            'anio_convocatoria' => date('Y'),
            'periodo'           => date('Y'),
            'convocatoria'      => '',
            'moneda'            => 'PEN',
            'fecha_registro'    => date('Y-m-d'),
            'objeto_contratacion' => '',
            'ganador'             => '',
            'fecha_adjudicacion'  => '',
            'fecha_consentido'    => '',
        ];

        $pacIdsSeleccionados = [];

        require __DIR__ . '/../Vista/modulos/admin/proceso_form.php';
    }

    public static function editar(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo "ID inválido";
            return;
        }

        $proceso = MdProcesoAdmin::obtener($id);

        if (!$proceso) {
            http_response_code(404);
            echo "Proceso no encontrado";
            return;
        }

        $estadosProceso = MdProcesoAdmin::listarEstadosProceso();

        $filtrosPac = [
            'q'       => $_GET['pac_q'] ?? '',
            'periodo' => $_GET['pac_periodo'] ?? '',
            'obac'    => $_GET['pac_obac'] ?? '',
        ];

        $pacsDisponibles = MdProcesoAdmin::listarPacsDisponibles($filtrosPac);
        $pacsVinculados  = MdProcesoAdmin::obtenerPacsVinculados($id);

        $pacIdsSeleccionados = array_map(
            fn($row) => (int)($row['id'] ?? 0),
            $pacsVinculados
        );

        require __DIR__ . '/../Vista/modulos/admin/proceso_form.php';
    }

    public static function guardar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $estadoConvocadoId = MdProcesoAdmin::obtenerIdEstadoPorCodigo('CONVOCADO');

            if (!$estadoConvocadoId) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'No existe el estado CONVOCADO en estado.'
                ]);
                exit;
            }

            $data = [
                'id'                => $_POST['id'] ?? null,
                'codigo_proceso'    => $_POST['codigo_proceso'] ?? '',
                'tipo_proceso'      => $_POST['tipo_proceso'] ?? 'INDIVIDUAL',
                'expediente'        => $_POST['expediente'] ?? '',
                'obac'              => $_POST['obac'] ?? null,
                'descripcion'       => $_POST['descripcion'] ?? '',
                'estimado'          => $_POST['estimado'] ?? 0,
                'estado_id'         => $estadoConvocadoId,
                'anio_convocatoria' => $_POST['anio_convocatoria'] ?? null,
                'periodo'           => $_POST['periodo'] ?? null,
                'convocatoria'      => $_POST['convocatoria'] ?? null,
                'moneda'            => $_POST['moneda'] ?? 'PEN',
                'fecha_registro'    => $_POST['fecha_registro'] ?? date('Y-m-d'),
                'objeto_contratacion' => $_POST['objeto_contratacion'] ?? null,
                'ganador'             => $_POST['ganador'] ?? null,
                'fecha_adjudicacion'  => $_POST['fecha_adjudicacion'] ?? null,
                'fecha_consentido'    => $_POST['fecha_consentido'] ?? null,
            ];

            $pacIds = $_POST['pac_ids'] ?? [];
            if (!is_array($pacIds)) {
                $pacIds = [];
            }

            $id = !empty($data['id']) ? (int)$data['id'] : null;

            if (trim((string)$data['codigo_proceso']) === '') {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'El código del proceso es obligatorio.'
                ]);
                exit;
            }

            if (trim((string)$data['descripcion']) === '') {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'La descripción del proceso es obligatoria.'
                ]);
                exit;
            }

            if (empty($data['convocatoria'])) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Debe ingresar la fecha de convocatoria.'
                ]);
                exit;
            }

            if (MdProcesoAdmin::existeCodigoProceso(trim((string)$data['codigo_proceso']), $id)) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Ya existe un proceso con ese código.'
                ]);
                exit;
            }

            if (strtoupper((string)$data['tipo_proceso']) === 'CORPORATIVO' && count($pacIds) < 2) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Un proceso corporativo debe tener al menos 2 PAC vinculados.'
                ]);
                exit;
            }

            if (strtoupper((string)$data['tipo_proceso']) === 'INDIVIDUAL' && count($pacIds) !== 1) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Un proceso individual debe tener exactamente 1 PAC vinculado.'
                ]);
                exit;
            }

            $procesoId = $id
                ? (MdProcesoAdmin::actualizarConPacs($id, $data, $pacIds) ? $id : 0)
                : MdProcesoAdmin::guardarConPacs($data, $pacIds);

            echo json_encode([
                'ok'         => true,
                'msg'        => $id ? 'Proceso actualizado correctamente.' : 'Proceso creado correctamente.',
                'proceso_id' => $procesoId,
                'redirect'   => BASE_URL . '/admin/procesos_detalle?id=' . $procesoId
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

    public static function actividades(): void
    {
        try {
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(400);
                echo "ID inválido";
                return;
            }

            $proceso = MdProcesoAdmin::obtener($id);
            if (!$proceso) {
                http_response_code(404);
                echo "Proceso no encontrado";
                return;
            }

            $actividades         = MdActividadAdmin::listarPorProceso($id) ?? [];
            $timelineActividades = self::construirTimelineActividades($actividades);
            $pacs_vinculados     = MdProcesoAdmin::obtenerPacsVinculados($id);
            $tiposActividad      = MdActividadAdmin::listarTiposActividad('PROCESO') ?? [];

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

    private static function construirTimelineActividades(array $actividades): array
    {
        if (empty($actividades)) {
            return [];
        }

        $ultimasPorTipo = [];

        foreach ($actividades as $a) {
            $tipoId = (int)($a['tipo_id'] ?? 0);
            $fecha  = strtotime((string)($a['fecha'] ?? '')) ?: 0;
            $id     = (int)($a['id'] ?? 0);

            if ($tipoId <= 0) {
                $ultimasPorTipo['sin_tipo_' . $id] = $a;
                continue;
            }

            if (!isset($ultimasPorTipo[$tipoId])) {
                $ultimasPorTipo[$tipoId] = $a;
                continue;
            }

            $actualFecha = strtotime((string)($ultimasPorTipo[$tipoId]['fecha'] ?? '')) ?: 0;
            $actualId    = (int)($ultimasPorTipo[$tipoId]['id'] ?? 0);

            if ($fecha > $actualFecha || ($fecha === $actualFecha && $id > $actualId)) {
                $ultimasPorTipo[$tipoId] = $a;
            }
        }

        $resultado = array_values($ultimasPorTipo);

        usort($resultado, function ($x, $y) {
            $fx = strtotime((string)($x['fecha'] ?? '')) ?: 0;
            $fy = strtotime((string)($y['fecha'] ?? '')) ?: 0;

            if ($fx === $fy) {
                return ((int)($x['id'] ?? 0)) <=> ((int)($y['id'] ?? 0));
            }

            return $fx <=> $fy;
        });

        return $resultado;
    }
}