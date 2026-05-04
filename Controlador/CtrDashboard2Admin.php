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

        $filtrosComparativos = $filtros;
        $filtrosComparativos['ejecucion'] = '0';

        $kpis                  = MdDashboard2Admin::obtenerKpisGenerales($filtros);
        $porEstado             = MdDashboard2Admin::obtenerResumenPorEstado($filtros);
        $porObac               = MdDashboard2Admin::obtenerResumenPorObac($filtros);
        $porMercado            = MdDashboard2Admin::obtenerResumenPorMercado($filtros);
        $porModalidad          = MdDashboard2Admin::obtenerResumenPorModalidad($filtros);
        $tendenciaMes          = MdDashboard2Admin::obtenerTendenciaMensual($filtros);
        $topDependencias       = MdDashboard2Admin::obtenerTopDependencias($filtros, 5);
        $topObac               = MdDashboard2Admin::obtenerTopObac($filtros, 5);
        $alertas               = MdDashboard2Admin::obtenerAlertasGerenciales($filtros);
        $comparativo           = MdDashboard2Admin::obtenerComparativoFinanciero($filtros);
        $pacCriticos           = MdDashboard2Admin::obtenerPacCriticos($filtros, 8);

        $participacion         = MdDashboard2Admin::obtenerParticipacionSectorDefensa($filtrosComparativos);
        $participacionPie      = MdDashboard2Admin::obtenerParticipacionPie($filtrosComparativos);

        $resumenListas         = MdDashboard2Admin::obtenerResumenListasGenerales($filtros);
        $resumenMercadoDetalle = MdDashboard2Admin::obtenerResumenTipoCompraPorMercado($filtros);

        $obacs   = MdPacAdmin::listarObac();
        $estados = MdPacAdmin::listarEstados();

        $active = 'dashboard2';

        require_once __DIR__ . '/../Vista/modulos/admin/dashboard2.php';
    }
}