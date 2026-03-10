<?php

require_once __DIR__ . '/../Modelo/MdPacAdmin.php';

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

        $obacs       = MdPacAdmin::listarObac();
        $fuentes     = MdPacAdmin::listarFuente();
        $selecciones = MdPacAdmin::listarSeleccion();
        $periodos    = MdPacAdmin::listarPeriodo();

        require_once __DIR__ . '/../Vista/modulos/admin/pac.php';
    }

    public static function guardar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $data = [
                'nopac'       => $_POST['nopac'] ?? '',
                'pn'          => $_POST['pn'] ?? 'NP',
                'estado'      => $_POST['estado'] ?? 'PUBLICADO',
                'descripcion' => $_POST['descripcion'] ?? '',
                'obac'        => $_POST['obac'] ?? null,
                'seleccion'   => $_POST['seleccion'] ?? null,
                'fuente'      => $_POST['fuente'] ?? null,
                'estimado'    => $_POST['estimado'] ?? 0,
                'periodo'     => $_POST['periodo'] ?? null,
            ];

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
                trim((string)$data['pn'])
            )) {
                echo json_encode([
                    'ok'  => false,
                    'msg' => 'Ya existe un PAC con ese N° PAC, OBAC y tipo P/NP.'
                ]);
                exit;
            }

            $ok = MdPacAdmin::guardar($data);

            echo json_encode([
                'ok'  => $ok,
                'msg' => $ok ? 'PAC guardado correctamente.' : 'No se pudo guardar.'
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