<?php
// Controlador/CtrDashboardAdmin.php
require_once __DIR__ . '/../Modelo/MdDashboardAdmin.php';
require_once __DIR__ . '/../Modelo/MdPacAdmin.php';

class CtrDashboardAdmin
{
    public static function index(): void
    {
        $filtros = [
            'periodo'   => $_GET['periodo'] ?? '',
            'ejecucion' => isset($_GET['ejecucion']) ? ($_GET['ejecucion'] === '0' ? '0' : $_GET['ejecucion']) : '4',
            'obac'      => $_GET['obac'] ?? '',
            'estado'    => $_GET['estado'] ?? '',
        ];

        $kpis            = MdDashboardAdmin::obtenerKpisGenerales($filtros);
        $porEstado       = MdDashboardAdmin::obtenerResumenPorEstado($filtros);
        $porObac         = MdDashboardAdmin::obtenerResumenPorObac($filtros);
        $porMercado      = MdDashboardAdmin::obtenerResumenPorMercado($filtros);
        $porModalidad    = MdDashboardAdmin::obtenerResumenPorModalidad($filtros);
        $tendenciaMes    = MdDashboardAdmin::obtenerTendenciaMensual($filtros);
        $topDependencias = MdDashboardAdmin::obtenerTopDependencias($filtros, 5);
        $topObac         = MdDashboardAdmin::obtenerTopObac($filtros, 5);
        $alertas         = MdDashboardAdmin::obtenerAlertasGerenciales($filtros);

        $periodos = MdPacAdmin::listarPeriodo();
        $obacs    = MdPacAdmin::listarObac();
        $estados  = MdPacAdmin::listarEstados();

        require_once __DIR__ . '/../Vista/modulos/admin/dashboard.php';
    }
}