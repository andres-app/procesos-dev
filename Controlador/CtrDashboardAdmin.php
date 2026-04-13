<?php
// Controlador/CtrDashboardAdmin.php
require_once __DIR__ . '/../Modelo/MdDashboardAdmin.php';
require_once __DIR__ . '/../Modelo/MdPacAdmin.php';

class CtrDashboardAdmin
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
            'periodo'   => isset($_GET['periodo']) && $_GET['periodo'] !== ''
                ? $_GET['periodo']
                : $periodoDefault,
            'ejecucion' => isset($_GET['ejecucion'])
                ? ($_GET['ejecucion'] === '0' ? '0' : $_GET['ejecucion'])
                : '4',
            'obac'      => $_GET['obac'] ?? '',
            'estado'    => $_GET['estado'] ?? '',
        ];

        /*
         * Estos filtros son para métricas comparativas/globales.
         * Ignoran SOLO el filtro rápido de ejecución (ACFFAA / Todos),
         * pero sí respetan periodo, obac y estado si el usuario los selecciona.
         */
        $filtrosComparativos = $filtros;
        $filtrosComparativos['ejecucion'] = '0';

        $kpis                  = MdDashboardAdmin::obtenerKpisGenerales($filtros);
        $porEstado             = MdDashboardAdmin::obtenerResumenPorEstado($filtros);
        $porObac               = MdDashboardAdmin::obtenerResumenPorObac($filtros);
        $porMercado            = MdDashboardAdmin::obtenerResumenPorMercado($filtros);
        $porModalidad          = MdDashboardAdmin::obtenerResumenPorModalidad($filtros);
        $tendenciaMes          = MdDashboardAdmin::obtenerTendenciaMensual($filtros);
        $topDependencias       = MdDashboardAdmin::obtenerTopDependencias($filtros, 5);
        $topObac               = MdDashboardAdmin::obtenerTopObac($filtros, 5);
        $alertas               = MdDashboardAdmin::obtenerAlertasGerenciales($filtros);
        $comparativo           = MdDashboardAdmin::obtenerComparativoFinanciero($filtros);
        $pacCriticos           = MdDashboardAdmin::obtenerPacCriticos($filtros, 8);

        /*
         * Estas vistas NO deben quedar sesgadas por el filtro rápido ACFFAA.
         * Se calculan sobre el universo comparativo.
         */
        $participacion         = MdDashboardAdmin::obtenerParticipacionSectorDefensa($filtrosComparativos);
        $participacionPie      = MdDashboardAdmin::obtenerParticipacionPie($filtrosComparativos);

        $resumenListas         = MdDashboardAdmin::obtenerResumenListasGenerales($filtros);
        $resumenMercadoDetalle = MdDashboardAdmin::obtenerResumenTipoCompraPorMercado($filtros);

        $obacs   = MdPacAdmin::listarObac();
        $estados = MdPacAdmin::listarEstados();

        require_once __DIR__ . '/../Vista/modulos/admin/dashboard.php';
    }
}