<?php
// Controlador/CtrDashboard2Admin.php

require_once __DIR__ . '/../Modelo/MdDashboard2Admin.php';
require_once __DIR__ . '/../Modelo/MdPacAdmin.php';

class CtrDashboard2Admin
{
    public static function index(): void
    {
        $periodos = MdPacAdmin::listarPeriodo();

        $periodoDefault = '';
        foreach ($periodos as $p) {
            if (trim((string)($p['nombre'] ?? '')) === '2026') {
                $periodoDefault = (string)$p['id'];
                break;
            }
        }

        $filtros = [
            'periodo' => isset($_GET['periodo']) && $_GET['periodo'] !== ''
                ? $_GET['periodo']
                : $periodoDefault,

            'ejecucion' => isset($_GET['ejecucion'])
                ? ($_GET['ejecucion'] === '0' ? '0' : $_GET['ejecucion'])
                : '4',

            'obac'   => $_GET['obac'] ?? '',
            'estado' => $_GET['estado'] ?? '',
        ];

        $dashboardData = MdDashboard2Admin::obtenerDataDashboard2($filtros);

        $obacs   = MdPacAdmin::listarObac();
        $estados = MdPacAdmin::listarEstados();

        $titulo = 'Dashboard 2';
        $active = 'dashboard2';

        require_once __DIR__ . '/../Vista/modulos/admin/dashboard2.php';
    }
}