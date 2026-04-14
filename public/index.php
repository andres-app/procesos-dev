<?php
// public/index.php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../Config/config.php';

/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
$path   = trim((string)($_GET['url'] ?? 'login'), '/');
$parts  = $path === '' ? [] : explode('/', $path);
$module = $parts[0] ?? 'login';
$sub    = $parts[1] ?? null;

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function not_found(string $msg = '404 - Página no encontrada'): void
{
    http_response_code(404);
    echo "
    <div style='font-family:Arial,sans-serif;padding:24px;color:#0f172a'>
        <h1 style='margin:0 0 10px;font-size:28px'>{$msg}</h1>
    </div>";
    exit;
}

function require_admin_login(): void
{
    if (empty($_SESSION['admin_user'])) {
        redirect(BASE_URL . '/admin/login');
    }
}

function require_file(string $file): void
{
    if (!is_file($file)) {
        not_found("404 - No existe: {$file}");
    }
    require $file;
}

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (/admin/...)
|--------------------------------------------------------------------------
*/
if ($module === 'admin') {

    $subRoute = (string)($sub ?? '');

    // /admin -> redirección según sesión
    if ($subRoute === '') {
        redirect(
            !empty($_SESSION['admin_user'])
                ? BASE_URL . '/admin/dashboard'
                : BASE_URL . '/admin/login'
        );
    }

    // públicas
    if ($subRoute === 'login') {
        require_once __DIR__ . '/../Controlador/CtrUsuariosAdmin.php';
        CtrUsuariosAdmin::login();
        exit;
    }

    if ($subRoute === 'verify-2fa') {
        require_once __DIR__ . '/../Controlador/CtrUsuariosAdmin.php';
        CtrUsuariosAdmin::verify2fa();
        exit;
    }

    if ($subRoute === 'setup-2fa') {
        require_once __DIR__ . '/../Controlador/CtrUsuariosAdmin.php';
        CtrUsuariosAdmin::setup2fa();
        exit;
    }

    if ($subRoute === 'logout') {
        require_file(__DIR__ . '/../Vista/modulos/admin/logout.php');
        exit;
    }

    // privadas
    require_admin_login();

    /*
    |--------------------------------------------------------------------------
    | EXPORTS (ADMIN)
    | URLs:
    |   /admin/export_excel/estado
    |   /admin/export_pdf/estado
    |--------------------------------------------------------------------------
    */
    if ($subRoute === 'export_excel') {
        $type = $parts[2] ?? 'estado';
        require_file(__DIR__ . '/../Vista/modulos/admin/exports/RptExcelEstado.php');
        exit;
    }

    if ($subRoute === 'export_pdf') {
        $type = $parts[2] ?? 'estado';
        require_file(__DIR__ . '/../Vista/modulos/admin/exports/pdf.php');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | RUTAS ESPECIALES ADMIN (con controlador)
    |--------------------------------------------------------------------------
    */
    if ($subRoute === 'dashboard') {
        require_once __DIR__ . '/../Controlador/CtrDashboardAdmin.php';
        CtrDashboardAdmin::index();
        exit;
    }

    if ($subRoute === 'actividades') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::actividades();
        exit;
    }

    if ($subRoute === 'procesos_detalle') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::actividades();
        exit;
    }

    // PAC ADMIN
    if ($subRoute === 'pac') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::index();
        exit;
    }

    if ($subRoute === 'pac_form') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::form();
        exit;
    }

    if ($subRoute === 'pac_guardar') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::guardar();
        exit;
    }

    if ($subRoute === 'pac_detalle') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::detalle();
        exit;
    }

    if ($subRoute === 'pac_eliminar') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::eliminar();
        exit;
    }

    if ($subRoute === 'pac_plantilla_csv') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::descargarPlantillaCsv();
        exit;
    }

    if ($subRoute === 'pac_importar_csv') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::importarCsv();
        exit;
    }

    if ($subRoute === 'pac_actividad_guardar') {
        require_once __DIR__ . '/../Controlador/CtrActividadPac.php';
        CtrPacActividad::guardar();
        exit;
    }

    if ($subRoute === 'proceso_actividad_guardar') {
        require_once __DIR__ . '/../Controlador/CtrActividadAdmin.php';
        CtrActividadAdmin::guardar();
        exit;
    }

    if ($subRoute === 'procesos') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::index();
        exit;
    }

    if ($subRoute === 'procesos_nuevo') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::nuevo();
        exit;
    }

    if ($subRoute === 'procesos_editar') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::editar();
        exit;
    }

    if ($subRoute === 'procesos_guardar') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::guardar();
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | VISTAS ADMIN NORMALES (directas)
    |--------------------------------------------------------------------------
    */
    $adminViews = [
        'presupuesto' => __DIR__ . '/../Vista/modulos/admin/presupuesto.php',
        'reportes'    => __DIR__ . '/../Vista/modulos/admin/reportes.php',
    ];

    if (!isset($adminViews[$subRoute])) {
        not_found('404 - Admin: Página no encontrada');
    }

    require_file($adminViews[$subRoute]);
    exit;
}

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
$routes = [

    'login' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrUsuario.php';
        CtrUsuario::login();
    },

    'verify-2fa' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrUsuario.php';
        CtrUsuario::verify2fa();
    },

    'setup-2fa' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrUsuario.php';
        CtrUsuario::setup2fa();
    },

    'logout' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrUsuario.php';
        CtrUsuario::logout();
    },

    'dashboard' => static function (): void {
        require_file(__DIR__ . '/../Vista/modulos/dashboard.php');
    },

    'perfil' => static function (): void {
        require_file(__DIR__ . '/../Vista/modulos/perfil.php');
    },

    'indicadores' => static function (): void {
        require_file(__DIR__ . '/../Vista/modulos/indicadores.php');
    },

    'alertas' => static function (): void {
        require_file(__DIR__ . '/../Vista/modulos/alertas.php');
    },

    'presupuesto' => static function (): void {
        require_file(__DIR__ . '/../Vista/modulos/presupuesto.php');
    },

    'actividades' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrActividades.php';
        CtrActividades::show();
    },

    'procesos' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrProceso.php';
        CtrProceso::index();
    },

    'pac' => static function (): void {
        require_once __DIR__ . '/../Controlador/CtrPac.php';
        CtrPac::index();
    },

    'logout' => static function (): void {
        session_destroy();
        redirect(BASE_URL . '/login');
    },

    'reportes' => static function () use ($sub): void {
        $subRoute = (string)($sub ?? 'index');
        $base     = __DIR__ . '/../Vista/modulos/reportes';

        $reportFiles = [
            'derivados'   => $base . '/derivados.php',
            'procesos'    => $base . '/procesos.php',
            'consolidado' => $base . '/consolidado.php',
            'index'       => __DIR__ . '/../Vista/modulos/reportes.php',
        ];

        $file = $reportFiles[$subRoute] ?? $reportFiles['index'];
        require_file($file);
    },
];

if (!isset($routes[$module])) {
    not_found();
}

$routes[$module]();
