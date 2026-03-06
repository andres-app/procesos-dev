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
}