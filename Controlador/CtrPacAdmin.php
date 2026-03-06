<?php

require_once __DIR__ . '/../Modelo/MdPacAdmin.php';

class CtrPacAdmin
{
    public static function index(): void
    {
        // filtros opcionales desde GET
        $filtros = [
            'q'       => $_GET['q'] ?? '',
            'pn'      => $_GET['pn'] ?? '',
            'estado'  => $_GET['estado'] ?? '',
            'periodo' => $_GET['periodo'] ?? '',
            'obac'    => $_GET['obac'] ?? '',
        ];

        // obtener PAC desde el modelo
        $pacs = MdPacAdmin::listar($filtros);

        // cargar vista
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
                'obac'        => $_POST['obac'] ?? 0,
                'fuente'      => $_POST['fuente'] ?? 0,
                'estimado'    => $_POST['estimado'] ?? 0,
                'periodo'     => $_POST['periodo'] ?? date('Y'),
            ];

            if (trim((string)$data['nopac']) === '') {
                echo json_encode([
                    'ok' => false,
                    'msg' => 'El N° PAC es obligatorio.'
                ]);
                exit;
            }

            if (trim((string)$data['descripcion']) === '') {
                echo json_encode([
                    'ok' => false,
                    'msg' => 'La descripción es obligatoria.'
                ]);
                exit;
            }

            $ok = MdPacAdmin::guardar($data);

            echo json_encode([
                'ok' => $ok,
                'msg' => $ok ? 'PAC guardado correctamente.' : 'No se pudo guardar.'
            ]);
            exit;
        } catch (Throwable $e) {
            echo json_encode([
                'ok' => false,
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }
}
