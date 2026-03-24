<?php
// Controlador/CtrPacAdmin.php
require_once __DIR__ . '/../Modelo/MdPacAdmin.php';
require_once __DIR__ . '/../Modelo/MdActividadPac.php';

class CtrPacAdmin
{
    public static function index(): void
    {
        $filtros = [
            'q'       => $_GET['q'] ?? '',
            'pn'      => $_GET['pn'] ?? '',
            'estado'  => $_GET['estado'] ?? '',
            'periodo' => $_GET['periodo'] ?? '',
            'obac'    => $_GET['obac'] ?? '',
        ];

        $pacs = MdPacAdmin::listar($filtros);

        $estados       = MdPacAdmin::listarEstados();
        $obacs         = MdPacAdmin::listarObac();
        $fuentes       = MdPacAdmin::listarFuente();
        $selecciones   = MdPacAdmin::listarSeleccion();
        $periodos      = MdPacAdmin::listarPeriodo();
        $listas        = MdPacAdmin::listarListas();
        $entidades     = MdPacAdmin::listarEntidades();
        $modalidades   = MdPacAdmin::listarModalidades();
        $dependencias  = MdPacAdmin::listarDependencias();
        $tipos_mercado = MdPacAdmin::listarTiposMercado();
        $rubros        = MdPacAdmin::listarRubros();

        require_once __DIR__ . '/../Vista/modulos/admin/pac.php';
    }

    public static function detalle(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido';
            exit;
        }

        $pac = MdPacAdmin::obtenerDetalle($id);
        $actividades = MdPacActividad::listarPorPac($id);

        require_once __DIR__ . '/../Vista/modulos/admin/pac_detalle.php';
    }

    public static function guardar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $data = [
                'id'           => $_POST['id'] ?? null,
                'nopac'        => $_POST['nopac'] ?? '',
                'pn'           => $_POST['pn'] ?? 'NP',
                'estado'       => $_POST['estado'] ?? null,
                'descripcion'  => $_POST['descripcion'] ?? '',
                'obac'         => $_POST['obac'] ?? null,
                'seleccion'    => $_POST['seleccion'] ?? null,
                'fuente'       => $_POST['fuente'] ?? null,
                'estimado'     => $_POST['estimado'] ?? 0,
                'periodo'      => $_POST['periodo'] ?? null,
                'lista'        => $_POST['lista'] ?? null,
                'ejecucion'    => $_POST['ejecucion'] ?? null,
                'modalidad'    => $_POST['modalidad'] ?? null,
                'dependencia'  => $_POST['dependencia'] ?? null,
                'mesconvoca'   => $_POST['mesconvoca'] ?? null,
                'certificado'  => $_POST['certificado'] ?? 0,
                'tipo_mercado' => $_POST['tipo_mercado'] ?? null,
                'cantidad'     => $_POST['cantidad'] ?? 0,
                'rubro'        => $_POST['rubro'] ?? null,
            ];

            $id = !empty($data['id']) ? (int)$data['id'] : null;

            if (trim((string)$data['nopac']) === '') {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'El N° PAC es obligatorio.'
                ]);
                exit;
            }

            if (trim((string)$data['descripcion']) === '') {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'La descripción es obligatoria.'
                ]);
                exit;
            }

            if (empty($data['obac'])) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Debe seleccionar un OBAC.'
                ]);
                exit;
            }

            if (MdPacAdmin::existePac(
                trim((string)$data['nopac']),
                !empty($data['obac']) ? (int)$data['obac'] : null,
                trim((string)$data['pn']),
                $id
            )) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Ya existe un PAC con ese N° PAC, OBAC y tipo P/NP.'
                ]);
                exit;
            }

            $ok = $id
                ? MdPacAdmin::actualizar($id, $data)
                : MdPacAdmin::guardar($data);

            echo json_encode([
                'ok'  => $ok,
                'msg' => $ok
                    ? ($id ? 'PAC actualizado correctamente.' : 'PAC guardado correctamente.')
                    : ($id ? 'No se pudo actualizar.' : 'No se pudo guardar.')
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
        $id = (int)($_GET['id'] ?? 0);

        if ($id > 0) {
            MdPacAdmin::eliminar($id);
        }

        header('Location: ' . BASE_URL . '/admin/pac');
        exit;
    }

    public static function descargarPlantillaCsv(): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="plantilla_pac.csv"');
        echo "\xEF\xBB\xBF";

        $out = fopen('php://output', 'w');

        fputcsv($out, [
            'nopac',
            'pn',
            'descripcion',
            'obac',
            'fuente',
            'estado',
            'estimado',
            'seleccion',
            'lista',
            'modalidad',
            'tipo_mercado',
            'rubro',
            'ejecucion',
            'dependencia',
            'mesconvoca',
            'periodo',
            'cantidad',
            'certificado'
        ], ';');

        fputcsv($out, [
            '800',
            'P',
            'EJEMPLO MASIVO 1',
            'FAP',
            'RECURSOS ORDINARIOS',
            'PUBLICADO',
            '316800.00',
            'ADJUDICACION SIMPLIFICADA',
            'LCMN',
            'INDIVIDUAL',
            'NACIONAL',
            'SERVICIO',
            'FAP',
            '',
            'MARZO',
            '2026',
            '1',
            '316800.00'
        ], ';');

        fputcsv($out, [
            '900',
            'NP',
            'EJEMPLO MASIVO 2',
            'CCFFAA',
            'RECURSOS ORDINARIOS',
            'OBSERVADO',
            '85000.00',
            'COMPARACION DE PRECIOS',
            'LCMN',
            'INDIVIDUAL',
            'EXTRANJERO',
            'BIEN',
            'CCFFAA',
            '',
            'ABRIL',
            '2026',
            '1',
            '0.00'
        ], ';');

        fclose($out);
        exit;
    }

    public static function importarCsv(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        try {
            if (!isset($_FILES['csv_file'])) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'No se recibió ningún archivo CSV.'
                ]);
                exit;
            }

            $archivo = $_FILES['csv_file'];

            if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Error al subir el archivo CSV.'
                ]);
                exit;
            }

            if (
                empty($archivo['tmp_name']) ||
                !is_uploaded_file($archivo['tmp_name'])
            ) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Archivo CSV inválido.'
                ]);
                exit;
            }

            $ext = strtolower(pathinfo((string)($archivo['name'] ?? ''), PATHINFO_EXTENSION));
            if ($ext !== 'csv') {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'El archivo debe tener extensión .csv'
                ]);
                exit;
            }

            $resultado = MdPacAdmin::importarDesdeCsv($archivo['tmp_name']);

            $insertados = (int)($resultado['insertados'] ?? 0);
            $omitidos   = (int)($resultado['omitidos'] ?? 0);
            $errores    = $resultado['errores'] ?? [];

            $msg = 'Importación realizada correctamente.';
            if ($insertados > 0 && $omitidos > 0) {
                $msg = 'Importación parcial completada.';
            } elseif ($insertados === 0 && $omitidos > 0) {
                $msg = 'No se importaron registros válidos.';
            }

            echo json_encode([
                'ok'         => true,
                'msg'        => $msg,
                'insertados' => $insertados,
                'omitidos'   => $omitidos,
                'errores'    => $errores
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