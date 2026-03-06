<?php

declare(strict_types=1);

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
    echo "<h1 style='color:white'>{$msg}</h1>";
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
        require_file(__DIR__ . '/../Vista/modulos/admin/login.php');
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
    | EXPORTS (ADMIN) - MAQUETA
    | URLs:
    |   /admin/export_excel/estado
    |   /admin/export_pdf/estado
    |--------------------------------------------------------------------------
    */
    if ($subRoute === 'export_excel') {
        $type = $parts[2] ?? 'estado'; // /admin/export_excel/{type}
        require_file(__DIR__ . '/../Vista/modulos/admin/exports/excel.php');
        exit;
    }

    if ($subRoute === 'export_pdf') {
        $type = $parts[2] ?? 'estado'; // /admin/export_pdf/{type}
        require_file(__DIR__ . '/../Vista/modulos/admin/exports/pdf.php');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | RUTAS ESPECIALES ADMIN (con controlador)
    |--------------------------------------------------------------------------
    */
    if ($subRoute === 'actividades') {
        require_once __DIR__ . '/../Controlador/CtrProcesoAdmin.php';
        CtrProcesoAdmin::actividades();
        exit;
    }

    if ($subRoute === 'pac') {
        require_once __DIR__ . '/../Controlador/CtrPacAdmin.php';
        CtrPacAdmin::index();
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | VISTAS ADMIN NORMALES (directas)
    |--------------------------------------------------------------------------
    */
    $adminViews = [
        'dashboard'   => __DIR__ . '/../Vista/modulos/admin/dashboard.php',
        'procesos'    => __DIR__ . '/../Vista/modulos/admin/procesos.php',
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
        require_file(__DIR__ . '/../Vista/modulos/pac.php');
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